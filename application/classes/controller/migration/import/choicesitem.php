<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Import_ChoicesItem extends Controller_Migration_Template {

	protected $migration_title = 'Импорт данных из файлов ItemChoices';
	protected $migration_description = [];

	const FILE_PATH = 'ja2/7609/inventory';

	public function get_count() {
		return 1;
	}

	public function action_index() {
		$this->migration_description = self::FILE_PATH . '/ItemChoices_(side)_(type).xml';
		parent::action_index();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		DB::query(Database::DELETE, 'TRUNCATE choices_item')->execute();

		$changes = 0;

		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_ENEMY, Core_Choices_Item::NPC_TYPE_COMMON);
		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_ENEMY, Core_Choices_Item::NPC_TYPE_ADMIN);
		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_ENEMY, Core_Choices_Item::NPC_TYPE_REGULAR);
		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_ENEMY, Core_Choices_Item::NPC_TYPE_ELITE);

		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_MILITIA, Core_Choices_Item::NPC_TYPE_GREEN);
		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_MILITIA, Core_Choices_Item::NPC_TYPE_REGULAR);
		$changes += $this->item_choices(Core_Choices_Item::NPC_SIDE_MILITIA, Core_Choices_Item::NPC_TYPE_ELITE);

		if ($changes < 1) {
			$this->migration->send_error('No changes! Import failed!');
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

	protected function item_choices($npc_side, $npc_type) {
		if ($npc_type === Core_Choices_Item::NPC_TYPE_COMMON) {
			$filename = APPPATH . '../' . self::FILE_PATH . "/EnemyItemChoices.xml";
		} else {
			$filename = APPPATH . '../' . self::FILE_PATH . "/ItemChoices_{$npc_side}_{$npc_type}.xml";
		}

		$changes = 0;

		try {
			$fp = fopen($filename, 'r');
			$contents = fread($fp, filesize($filename));
			fclose($fp);

			$xml = new SimpleXMLElement($contents);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		try {
			foreach ($xml->ENEMYITEMCHOICES as $enemy_item_choices) {
				$list_index = intval($enemy_item_choices->uiIndex);
				$list_name = strval($enemy_item_choices->name);

				for ($i = 1; $i < $enemy_item_choices->ubChoices; $i++) {
					$uiIndex = $enemy_item_choices->{'bItemNo' . $i};

					if ($uiIndex < 1) {
						continue;
					}

					/**
					 * @var $model Model_Choices_Item
					 */
					$model = Core_Choices_Item::factory()->create();

					$model->npc_side = $npc_side;
					$model->npc_type = $npc_type;
					$model->list_index = $list_index;
					$model->list_name = $list_name;
					$model->uiIndex = $uiIndex;

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
			}
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		return $changes;
	}

}