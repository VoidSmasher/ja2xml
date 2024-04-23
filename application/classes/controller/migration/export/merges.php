<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Export_Merges
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Export_Merges extends Controller_Migration_Template {

	protected $migration_title = 'Экспорт данных в Merges.xml';

	const FILE_PATH = 'htdocs/uploads/Items/Merges.xml';

	public function get_count() {
		return 1;
	}

	public function action_index() {
		$filename = APPPATH . '../' . self::FILE_PATH;
		$this->migration->description('Пишет в файл: ' . $filename);
		$this->migration->description('Если такой файл уже существует, он будет создан заново.');
		parent::action_index();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$filename = APPPATH . '../' . self::FILE_PATH;

		$changes = 0;

		try {
			if (file_exists($filename)) {
				unlink($filename);
			}

			$contents = '<?xml version="1.0" encoding="utf-8"?>
<MERGELIST>
</MERGELIST>';

			$xml = simplexml_load_string($contents);

			$list = Core_Merge_Mod::factory()->get_builder()
				->order_by('firstItemIndex')
				->order_by('secondItemIndex')
				->order_by('firstResultingItemIndex')
				->order_by('secondResultingItemIndex')
				->select_all();

			foreach ($list as $model) {
				/** @var Model_Merge_Mod $model */

				$merge = $xml->addChild('MERGE');

				$array = $model->as_array();

				foreach ($array as $field => $value) {
					if (in_array($field, [
						'id',
					])) {
						continue;
					}
					$merge->addChild($field, $value);
				}

				unset($array);

				$changes++;
			}

			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			$dom->save($filename);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Export_Merges