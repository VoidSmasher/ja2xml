<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Import_Items
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Import_Items extends Controller_Migration_Template {

	protected $migration_title = 'Импорт данных из Items.xml';
	protected $migration_description = [];

	const FILE_PATH = 'ja2/7609/items/Items.xml';

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
			foreach ($xml->ITEM as $item) {
				$uiIndex = intval($item->uiIndex);

				$model = Core_Item::factory()->get_builder()
					->where('uiIndex', '=', $uiIndex)
					->limit(1)
					->select();

				foreach ($item as $field => $value) {
					switch ($field) {
						case 'szItemName':
						case 'szLongItemName':
						case 'szItemDesc':
						case 'szBRName':
						case 'szBRDesc':
							$model->set([
								$field => strval($value),
							]);
							break;
						case 'spreadPattern':
							if ($value instanceof SimpleXMLElement) {
								$value = strval($value);
								if (empty($value)) {
									$value = NULL;
								}
							}
							$model->set([
								$field => $value,
							]);
							break;
						case 'AvailableAttachmentPoint':
						case 'DefaultAttachment':
							$arr = array();
							foreach ($item->{$field} as $_value) {
								$arr[] = intval($_value);
							}
							$model->set([
								$field => json_encode($arr, JSON_UNESCAPED_UNICODE),
							]);
							unset($arr);
							break;
						case 'STAND_MODIFIERS':
						case 'CROUCH_MODIFIERS':
						case 'PRONE_MODIFIERS':
							if ($value instanceof SimpleXMLElement) {
								$stance = array();

								foreach ($value as $stance_field => $stance_value) {
									$stance[$stance_field] = intval($stance_value);
								}

								if (empty($stance)) {
									$model->{$field} = NULL;
								} else {
									$model->set([
										$field => json_encode($stance, JSON_UNESCAPED_UNICODE),
									]);
								}
							}
							break;
						default:
							try {
								$model->set([
									$field => floatval($value),
								]);
							} catch (Exception $e) {
								Log::exception($e, __CLASS__, __FUNCTION__);
							}
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

} // End Controller_Migration_Import_Items