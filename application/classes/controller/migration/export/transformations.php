<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Export_Transformations
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Export_Transformations extends Controller_Migration_Template {

	protected $migration_title = 'Экспорт данных в Item_Transformations.xml';

	const FILE_PATH = 'htdocs/uploads/Items/Item_Transformations.xml';

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
<TRANSFORMATIONS_LIST>
</TRANSFORMATIONS_LIST>';

			$xml = simplexml_load_string($contents);

			$list = Core_Item_Transformation_Mod::factory()->get_builder()
				->order_by('usItem')
				->order_by('usResult1')
				->order_by('usResult2')
				->order_by('usResult3')
				->order_by('usResult4')
				->order_by('usResult5')
				->order_by('usResult6')
				->order_by('usResult7')
				->order_by('usResult8')
				->order_by('usResult9')
				->order_by('usResult10')
				->select_all();

			foreach ($list as $model) {
				/** @var Model_Item_Transformation_Mod $model */

				$merge = $xml->addChild('TRANSFORM');

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

} // End Controller_Migration_Export_Transformations