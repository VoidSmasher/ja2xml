<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_CSV_Attachments
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_CSV_Attachments extends Controller_Migration_Template {

	protected $migration_title = 'Импорт данных из attachments.csv';
	protected $migration_description = [];

	const FILE_PATH = 'original/7609/attachments.csv';

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

		$contents = array();

		$first = true;
		$columns = array();

		try {
			if (($handle = fopen($filename, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					if ($first) {
						$columns = $data;
						$first = false;
						continue;
					}
					$contents[] = $data;
				}
				fclose($handle);
			}
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		$changes = 0;

		foreach ($contents as $weapon) {
			$data = array();
			foreach ($columns as $index => $field) {
				$value = Arr::get($weapon, $index, 0);
				$data[$field] = $value;
			}

			$model = Core_Weapon_Data::factory()->get_builder()
				->where('uiIndex', '=', intval($data['uiIndex']))
				->limit(1)
				->select();

			foreach ($data as $field => $value) {
				switch ($field) {
					case 'stock':
						$model->$field = $value;
						break;
					case 'has_bipod':
					case 'has_foregrip':
						$model->set([$field => (boolean)strlen($value)]);
						break;
					default:
						$value = str_replace(',', '.', $value);
						$model->set([$field => floatval($value)]);
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

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_CSV_Attachments