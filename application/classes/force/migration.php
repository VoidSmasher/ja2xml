<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Migration
 * User: legion
 * Date: 06.07.12
 * Time: 6:01
 */
class Force_Migration {

	const MIGRATION_COUNT = 'migration-count';
	const MIGRATION_ACTIVE = 'migration-active';
	const MIGRATION_ACTIVE_TIME = 60;

	protected $_title = '';
	protected $_description = '';
	protected $_messages = array();
	protected $_total_steps = 0;
	protected $_deactivate_migration = true;
	protected $_changes_count = 0;
	protected $_block_active_migration = true;

	protected $_mode = null;
	protected $_step = 1;

	protected static $_instance;

	/**
	 * @return Force_Migration
	 */
	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		if (Form::is_post()) {
			$this->_mode = Arr::get($_POST, 'mode', null);
			$this->_step = Arr::get($_POST, 'step', 1);
		}
	}

	public function __toString() {
		return $this->render();
	}

	public function render($count = null) {
		if (!is_null($count)) {
			$this->total_steps($count);
		}
		$title = $this->title();
		if (!empty($this->_description)) {
			$description = nl2br($this->_description);
		} else {
			$description = '';
		}

		Helper_Assets::add_js_vars('migration', array(
			'process_path' => Force_URL::current()
				->action('process')
				->data_json()
				->get_url(),
			'start_path' => Force_URL::current()
				->action('start')
				->data_json()
				->get_url(),
			'finish_path' => Force_URL::current()
				->action('finish')
				->data_json()
				->get_url(),
			'message_before_start' => __('migration.before_start'),
			'message_before_finish' => __('migration.before_finish'),
			'message_start' => __('migration.start'),
			'message_success' => __('migration.success'),
			'message_fail' => __('migration.fail'),

			'button_start' => __('common.start'),
			'button_pause' => __('common.pause'),
			'button_continue' => __('common.continue'),

			'total_steps' => (integer)$count,
		));

		Helper_Assets::add_scripts(array(
			'assets/common/js/pretty_timer.js',
			'assets/migration/js/progress.bar.js',
		));

		return View::factory(FORCE_VIEW . 'migration/progressbar')
			->set('title', $title)
			->set('description', $description)
			->set('button_caption', __('common.start'))
			->set('total_items_count', (integer)$count)
			->render();
	}

	public function total_steps($count) {
		$this->set_count_of_undone_items($count);
		$this->_total_steps = $this->get_count_of_undone_items();
	}

	/*
	 * COUNT OF UNDONE ITEMS
	 */

	public function get_count_of_undone_items() {
		return (int)Session::instance()->get(self::MIGRATION_COUNT, 0);
	}

	public function set_count_of_undone_items($count) {
		Session::instance()->set(self::MIGRATION_COUNT, (int)$count);
	}

	/*
	 * TITLE
	 */

	public function title($title = null) {
		if (!empty($title)) {
			$this->_title = (string)$title;
		} else {
			if (empty($this->_title)) {
				$request = Request::current();
				$this->_title = 'Migration ' . $request->controller();
			}
		}
		return $this->_title;
	}

	/*
	 * DESCRIPTION
	 */

	public function description($text = null, $delimiter = PHP_EOL, $overwrite = false) {
		if ($overwrite) {
			$this->_description = Helper_String::to_string($text, $delimiter);
		} else {
			if (!empty($this->_description)) {
				$this->_description .= (string)$delimiter . Helper_String::to_string($text, $delimiter);
			} else {
				$this->_description = Helper_String::to_string($text, $delimiter);
			}
		}
		return $this->_description;
	}

	/*
	 * MIGRATION CONTROL
	 */

	/*
	 * Следует использовать disable_active_migration_block,
	 * в случаях использования других способов блокировки миграции.
	 * Следует указывать ДО того как будет вызван start();
	 */
	public function disable_active_migration_block() {
		$this->_block_active_migration = FALSE;
		return $this;
	}

	public function check_count($count) {
		if ($count <= 0) {
			$this->stop();
		}
		Cache::instance()->set(self::MIGRATION_ACTIVE, time(), self::MIGRATION_ACTIVE_TIME);
		return $this;
	}

	public function start($count = null, $stop = false, $error = false) {
		if ($this->_block_active_migration) {
			$active_migration = Cache::instance()->get(self::MIGRATION_ACTIVE, false);
			if ($active_migration) {
				$diff = (self::MIGRATION_ACTIVE_TIME + $active_migration) - time();
				if ($diff > 0) {
					$stop = true;
					$this->message_danger(__('migration.conflict', array(
						':time' => Force_Date::factory(0)
							->show_seconds()
							->humanize_delta_time($diff, true),
					)));
					$this->_deactivate_migration = false;
					$error = true;
				} else {
					Cache::instance()->delete(self::MIGRATION_ACTIVE);
				}
			}
		}
		if (!$stop) {
			if (!is_null($count)) {
				$this->total_steps($count);
			}
			Cache::instance()->set(self::MIGRATION_ACTIVE, true, self::MIGRATION_ACTIVE_TIME);
		}
		$this->send_result($stop, $error);
	}

	public function stop($error = false) {
		$this->send_result(true, $error);
	}

	/*
	 * SEND RESULT
	 */

	public function send_error($message, $stop = true) {
		$this->message_danger($message);
		if ($stop) {
			$this->stop(true);
		}
	}

	public function send_result($stop = false, $error = false) {
		$result = array(
			'stop' => boolval($stop),
			'error' => boolval($error),
			'messages' => $this->_messages,
		);

		if ($this->_changes_count > 0) {
			$count = $this->get_count_of_undone_items();
			$count = $count - $this->_changes_count;
			if ($count < 0) {
				$count = 0;
			}

			$this->set_count_of_undone_items($count);
			$result['changes_count'] = $this->_changes_count;
		}

		if ($this->_total_steps > 0) {
			$result['total_steps'] = $this->_total_steps;
		}

		if ($stop) {
			Session::instance()->delete(self::MIGRATION_COUNT);
			if ($this->_deactivate_migration) {
				Cache::instance()->delete(self::MIGRATION_ACTIVE);
			}
		}

		echo json_encode($result);
		exit(0);
	}

	/*
	 * STATUS
	 */

	public function get_mode() {
		return $this->_mode;
	}

	public function get_step() {
		return $this->_step;
	}

	/*
	 * MESSAGES
	 */

	public function message($message) {
		$this->_add_message($message);
		return $this;
	}

	public function message_info($message) {
		$this->_add_message($message, 'info');
		return $this;
	}

	public function message_success($message) {
		$this->_add_message($message, 'success');
		return $this;
	}

	public function message_warning($message) {
		$this->_add_message($message, 'warning');
		return $this;
	}

	public function message_danger($message) {
		$this->_add_message($message, 'danger');
		return $this;
	}

	protected function _add_message($message, $type = 'default') {
		$this->_messages[] = array(
			'message' => $message,
			'type' => $type,
		);
	}

	/*
	 * CHANGES
	 */

	public function set_changes_count($changes_count) {
		$this->_changes_count = (int)$changes_count;
		if ($this->_changes_count < 0) {
			$this->_changes_count = 0;
		}
		return $this;
	}

	public function inc_changes_count() {
		$this->_changes_count++;
		return $this;
	}

} // End Force_Migration
