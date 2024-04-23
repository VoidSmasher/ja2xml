<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Item_Mod
 * User: legion
 * Date: 08.05.18
 * Time: 21:30
 */
class Core_Item_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Item_Mod';
	protected $model_name = 'item_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function update_items_builder_with_weapons(Jelly_Builder $builder) {
		$builder
			->select_column('weapons_mod.ubWeaponClass', 'ubWeaponClass')
			->select_column('weapons_mod.ubWeaponType', 'ubWeaponType')
			->select_column('weapons_mod.szWeaponName', 'szWeaponName')
			->select_column('weapons_mod.ubCalibre', 'ubCalibre')
			->select_column('weapons_mod.ubReadyTime', 'ubReadyTime')
			->select_column('weapons_mod.ubShotsPer4Turns', 'ubShotsPer4Turns')
			->select_column('weapons_mod.ubShotsPerBurst', 'ubShotsPerBurst')
			->select_column('weapons_mod.ubBurstPenalty', 'ubBurstPenalty')
			->select_column('weapons_mod.ubBulletSpeed', 'ubBulletSpeed')
			->select_column('weapons_mod.ubImpact', 'ubImpact')
			->select_column('weapons_mod.ubDeadliness', 'ubDeadliness')
			->select_column('weapons_mod.bAccuracy', 'bAccuracy')
			->select_column('weapons_mod.ubMagSize', 'ubMagSize')
			->select_column('weapons_mod.usRange', 'usRange')
			->select_column('weapons_mod.usReloadDelay', 'usReloadDelay')
			->select_column('weapons_mod.BurstAniDelay', 'BurstAniDelay')
			->select_column('weapons_mod.ubAttackVolume', 'ubAttackVolume')
			->select_column('weapons_mod.ubHitVolume', 'ubHitVolume')
			->select_column('weapons_mod.sSound', 'sSound')
			->select_column('weapons_mod.sBurstSound', 'sBurstSound')
			->select_column('weapons_mod.sSilencedBurstSound', 'sSilencedBurstSound')
			->select_column('weapons_mod.sReloadSound', 'sReloadSound')
			->select_column('weapons_mod.sLocknLoadSound', 'sLocknLoadSound')
			->select_column('weapons_mod.SilencedSound', 'SilencedSound')
			->select_column('weapons_mod.bBurstAP', 'bBurstAP')
			->select_column('weapons_mod.bAutofireShotsPerFiveAP', 'bAutofireShotsPerFiveAP')
			->select_column('weapons_mod.APsToReload', 'APsToReload')
			->select_column('weapons_mod.SwapClips', 'SwapClips')
			->select_column('weapons_mod.MaxDistForMessyDeath', 'MaxDistForMessyDeath')
			->select_column('weapons_mod.AutoPenalty', 'AutoPenalty')
			->select_column('weapons_mod.NoSemiAuto', 'NoSemiAuto')
			->select_column('weapons_mod.EasyUnjam', 'EasyUnjam')
			->select_column('weapons_mod.APsToReloadManually', 'APsToReloadManually')
			->select_column('weapons_mod.ManualReloadSound', 'ManualReloadSound')
			->select_column('weapons_mod.nAccuracy', 'nAccuracy')
			->select_column('weapons_mod.bRecoilX', 'bRecoilX')
			->select_column('weapons_mod.bRecoilY', 'bRecoilY')
			->select_column('weapons_mod.ubAimLevels', 'ubAimLevels')
			->select_column('weapons_mod.ubRecoilDelay', 'ubRecoilDelay')
			->select_column('weapons_mod.Handling', 'Handling')
			->select_column('weapons_mod.usOverheatingJamThreshold', 'usOverheatingJamThreshold')
			->select_column('weapons_mod.usOverheatingDamageThreshold', 'usOverheatingDamageThreshold')
			->select_column('weapons_mod.usOverheatingSingleShotTemperature', 'usOverheatingSingleShotTemperature')
			->select_column('weapons_mod.HeavyGun', 'HeavyGun');
	}

	/**
	 * @param bool $mod
	 * @return Jelly_Builder
	 */
	public static function get_weapons_builder($mod = true) {
		if ($mod) {
			$builder = Core_Item_Mod::factory()->preset_for_admin()->get_builder();
			$table = 'items_mod';
		} else {
			$builder = Core_Item::factory()->preset_for_admin()->get_builder();
			$table = 'items';
		}

		$builder
			->join('weapons_mod', 'LEFT')->on('weapons_mod.uiIndex', '=', $table . '.uiIndex')
			->join('data_weapons', 'LEFT')->on('data_weapons.uiIndex', '=', $table . '.uiIndex')
			->join('calibres', 'LEFT')->on('calibres.ubCalibre', '=', 'weapons_mod.ubCalibre')
			->join(['data_attachments', 'stock_attachments'], 'LEFT')->on('stock_attachments.uiIndex', '=', 'data_weapons.integrated_stock_index')
			->where($table . '.usItemClass', 'IN', [
				Core_Item::CLASS_WEAPON,
			])
			->where($table . '.DefaultUndroppable', 'IS', NULL)
			->select_column($table . '.*')
			->select_column('stock_attachments.ItemSizeBonus', 'stock_ItemSizeBonus');

		if ($mod) {
			$builder->join('items')->on('items.uiIndex', '=', $table . '.uiIndex')
				->select_column('items.DefaultAttachment', 'DefaultAttachment_original');
		}

		Core_Item_Mod::update_items_builder_with_weapons($builder);
		Core_Weapon_Mod::update_weapons_builder_with_data_weapons($builder);
		Core_Weapon_Mod::update_weapons_builder_with_calibres($builder);

		return $builder;
	}

	/**
	 * @param bool $mod
	 * @return Jelly_Builder
	 */
	public static function get_attachments_builder() {
		$table = 'items_mod';
		$builder = Core_Item_Mod::factory()->preset_for_admin()->get_builder();

		$builder
			->join('data_attachments')->on('data_attachments.uiIndex', '=', $table . '.uiIndex')
			->join('items','left')->on('items.uiIndex', '=', $table . '.uiIndex')
//			->where($table . '.DefaultUndroppable', 'IS', NULL)
			->select_column($table . '.*')
			->select_column($table . '.uiIndex', 'uiIndex')
			->select_column('items.DefaultAttachment', 'DefaultAttachment_original');

		Core_Attachment_Data::update_builder_with_data_attachments($builder);

		return $builder;
	}

	public static function calculate_unaredynamic(Model_Weapon_Group $model) {
		$unaredynamic = ($model->weight > 6) ? 1 : NULL;

		return $unaredynamic;
	}

	/*
	 * Default Attachments
	 */

	public static function get_default_attachments(Jelly_Model $model, $field = 'DefaultAttachment') {
		$default_attachments = json_decode($model->{$field}, true);

		if (!is_array($default_attachments)) {
			$default_attachments = array();
		}

		if (!empty($default_attachments)) {
			$default_attachments = array_flip($default_attachments);
			foreach ($default_attachments as $attach_index => $number) {
				$default_attachments[$attach_index] = $attach_index;
			}
		}

		return $default_attachments;
	}

} // End Core_Item_Mod
