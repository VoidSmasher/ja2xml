<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Export_Items
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Export_Items extends Controller_Migration_Template {

	protected $migration_title = 'Экспорт данных в Items.xml';

	const FILE_PATH = 'htdocs/uploads/Items/Items.xml';

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
<ITEMLIST>
</ITEMLIST>';

			$xml = simplexml_load_string($contents);

			$list = Core_Item_Mod::factory()->get_builder()
				->order_by('uiIndex')
				->select_all();

			foreach ($list as $model) {
				$item = $xml->addChild('ITEM');

				$uiIndex = $model->uiIndex;

				$array = $model->as_array();

				foreach ($array as $field => $value) {
					if ($uiIndex == 0 && $field == 'spreadPattern') {
						$item->addChild($field);
					}
					if (in_array($field, [
						'id',
					])) {
						continue;
					}
					switch ($field) {
						case 'STAND_MODIFIERS':
						case 'CROUCH_MODIFIERS':
						case 'PRONE_MODIFIERS':
							$stance_modifiers = $item->addChild($field);
							if (empty($value)) {
								continue;
							}
							$value = json_decode($value, true);
							if (empty($value)) {
								continue;
							}
							foreach ($value as $stance_field => $stance_value) {
								if (is_null($stance_value)) {
									continue;
								}
								$stance_modifiers->addChild($stance_field, htmlspecialchars($stance_value));
							}
							break;
						default:
							if (is_null($value)) {
								continue;
							}
							switch ($field) {
								case 'AvailableAttachmentPoint':
								case 'DefaultAttachment':
									$value = json_decode($value, true);
									if (is_array($value)) {
										foreach ($value as $_value) {
											$item->addChild($field, $_value);
										}
									}
									break;
								default:
									if (is_float($value)) {
										$value = number_format($value, 2, '.', '');
									} else {
										$value = htmlspecialchars($value);
									}
									$item->addChild($field, $value);
									break;
							}
							break;
					}
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

} // End Controller_Migration_Export_Items