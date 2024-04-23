<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Import_Pockets
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Import_Pockets extends Controller_Migration_Template {

	protected $migration_title = 'Импорт данных из Pockets.xml';
	protected $migration_description = [];

	const FILE_PATH = 'ja2/7609/items/Pockets.xml';

	public function get_count() {
		return 1;
	}

	public function action_index() {
		$this->migration_description = self::FILE_PATH;
		parent::action_index();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$filename = APPPATH . '../' . self::FILE_PATH;

		try {
			$fp = fopen($filename, 'r');
			$contents = fread($fp, filesize($filename));
			fclose($fp);

			$xml = new SimpleXMLElement($contents);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		$changes = 0;

		try {
			foreach ($xml->POCKET as $pocket) {
				$model = Core_Pocket::factory()->get_builder()
					->where('pIndex', '=', $pocket['pIndex'])
					->limit(1)
					->select();

				foreach ($pocket as $field => $value) {
					switch ($field) {
						case 'pName':
							$model->set([
								$field => strval($value),
							]);
							break;
						default:
							$model->set([
								$field => intval($value),
							]);
							break;
					}
				}

				try {
					$model->save();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
					$this->migration->send_error($e->getMessage());
				}

				if ($model->saved()) {
					$changes++;
				}
			}
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		if ($changes < 1) {
			$this->migration->send_error('No changes! Import failed!');
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Import_Pockets