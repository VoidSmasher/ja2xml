<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Weapon_Mod
 * User: legion
 * Date: 05.05.18
 * Time: 12:40
 */
class Core_Weapon_Mod extends Core_Common {

	const DEFAULT_APS_TO_RELOAD = 20;

	use Core_Common_Static;

	protected static $model_class = 'Model_Weapon_Mod';
	protected $model_name = 'weapon_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function update_weapons_builder_with_data_weapons(Jelly_Builder $builder) {
		$builder
			->select_column('data_weapons.calibre', 'calibre')
			->select_column('data_weapons.name_long', 'weapon_name')
			->select_column('data_weapons.name_short', 'weapon_name_short')
			->select_column('data_weapons.description', 'weapon_description')
			->select_column('data_weapons.description_br', 'weapon_description_br')
			->select_column('data_weapons.year_of_adoption', 'year_of_adoption')
			->select_column('data_weapons.year_of_withdrawal', 'year_of_withdrawal')
			->select_column('data_weapons.amount_built', 'amount_built')
			->select_column('data_weapons.rarity', 'rarity')
			->select_column('data_weapons.weapon_class', 'weapon_class')
			->select_column('data_weapons.weapon_type', 'weapon_type')
			->select_column('data_weapons.length_min', 'length_min')
			->select_column('data_weapons.length_max', 'length_max')
			->select_column('data_weapons.length_barrel', 'length_barrel')
			->select_column('data_weapons.length_front_and_handle', 'length_front_and_handle')
			->select_column('data_weapons.length_front_to_trigger', 'length_front_to_trigger')
			->select_column('data_weapons.height_diff_stock_barrel', 'height_diff_stock_barrel')
			->select_column('data_weapons.weight', 'weight')
			->select_column('data_weapons.weight_empty', 'weight_empty')
			->select_column('data_weapons.weight_loaded', 'weight_loaded')
			->select_column('data_weapons.weight_front_percent', 'weight_front_percent')
			->select_column('data_weapons.fire_rate_semi', 'fire_rate_semi')
			->select_column('data_weapons.fire_rate_auto_min', 'fire_rate_auto_min')
			->select_column('data_weapons.fire_rate_auto_max', 'fire_rate_auto_max')
			->select_column('data_weapons.fire_rate_burst', 'fire_rate_burst')
			->select_column('data_weapons.burst_length', 'burst_length')
			->select_column('data_weapons.no_semi_auto', 'no_semi_auto')
			->select_column('data_weapons.no_full_auto', 'no_full_auto')
			->select_column('data_weapons.muzzle_velocity', 'muzzle_velocity')
			->select_column('data_weapons.mag_size', 'mag_size')
			->select_column('data_weapons.mechanism_action', 'mechanism_action')
			->select_column('data_weapons.mechanism_trigger', 'mechanism_trigger')
			->select_column('data_weapons.mechanism_feature', 'mechanism_feature')
			->select_column('data_weapons.mechanism_reload', 'mechanism_reload')
			->select_column('data_weapons.weapon_qualities', 'weapon_qualities')
			->select_column('data_weapons.default_attachments', 'default_attachments')
			->select_column('data_weapons.possible_attachments', 'possible_attachments')
			->select_column('data_weapons.attachment_mounts', 'attachment_mounts')
			->select_column('data_weapons.is_two_handed', 'is_two_handed')
			->select_column('data_weapons.is_secondary_weapon', 'is_secondary_weapon')
			->select_column('data_weapons.has_hp_iron_sights', 'has_hp_iron_sights')
			->select_column('data_weapons.has_hp_scope_mount', 'has_hp_scope_mount')
			->select_column('data_weapons.has_hk_trigger', 'has_hk_trigger')
			->select_column('data_weapons.has_mag_stanag', 'has_mag_stanag')
			->select_column('data_weapons.has_drum_mag', 'has_drum_mag')
			->select_column('data_weapons.has_calico_mag', 'has_calico_mag')
			->select_column('data_weapons.has_long_mag', 'has_long_mag')
			->select_column('data_weapons.has_bolt_hold_open', 'has_bolt_hold_open')
			->select_column('data_weapons.has_balanced_automatic', 'has_balanced_automatic')
			->select_column('data_weapons.has_compensator', 'has_compensator')
			->select_column('data_weapons.has_muzzle_break', 'has_muzzle_break')
			->select_column('data_weapons.has_recoil_reducing_stock', 'has_recoil_reducing_stock')
			->select_column('data_weapons.has_recoil_buffer_in_stock', 'has_recoil_buffer_in_stock')
			->select_column('data_weapons.has_ported_barrel', 'has_ported_barrel')
			->select_column('data_weapons.has_heavy_barrel', 'has_heavy_barrel')
			->select_column('data_weapons.has_sniper_barrel', 'has_sniper_barrel')
			->select_column('data_weapons.has_floating_barrel', 'has_floating_barrel')
			->select_column('data_weapons.has_replaceable_barrel', 'has_replaceable_barrel')
			->select_column('data_weapons.has_cheek_piece', 'has_cheek_piece')
			->select_column('data_weapons.has_adjustable_cheek_piece', 'has_adjustable_cheek_piece')
			->select_column('data_weapons.has_adjustable_butt_stock', 'has_adjustable_butt_stock')
			->select_column('data_weapons.has_adjustable_grip', 'has_adjustable_grip')
			->select_column('data_weapons.integrated_stock_name', 'integrated_stock_name')
			->select_column('data_weapons.integrated_stock_index', 'integrated_stock_index')
			->select_column('data_weapons.integrated_scope_name', 'integrated_scope_name')
			->select_column('data_weapons.integrated_scope_index', 'integrated_scope_index')
			->select_column('data_weapons.integrated_sight_name', 'integrated_sight_name')
			->select_column('data_weapons.integrated_sight_index', 'integrated_sight_index')
			->select_column('data_weapons.integrated_laser_name', 'integrated_laser_name')
			->select_column('data_weapons.integrated_laser_index', 'integrated_laser_index')
			->select_column('data_weapons.integrated_bipod_name', 'integrated_bipod_name')
			->select_column('data_weapons.integrated_bipod_index', 'integrated_bipod_index')
			->select_column('data_weapons.integrated_foregrip_name', 'integrated_foregrip_name')
			->select_column('data_weapons.integrated_foregrip_index', 'integrated_foregrip_index')
			->select_column('data_weapons.integrated_suppressor_name', 'integrated_suppressor_name')
			->select_column('data_weapons.integrated_suppressor_index', 'integrated_suppressor_index')
			->select_column('data_weapons.accuracy_bonus', 'accuracy_bonus')
			->select_column('data_weapons.accuracy_bonus_percent', 'accuracy_bonus_percent')
			->select_column('data_weapons.range_bonus', 'range_bonus')
			->select_column('data_weapons.range_bonus_percent', 'range_bonus_percent')
			->select_column('data_weapons.ready_bonus', 'ready_bonus')
			->select_column('data_weapons.ready_bonus_percent', 'ready_bonus_percent')
			->select_column('data_weapons.sp4t_bonus', 'sp4t_bonus')
			->select_column('data_weapons.sp4t_bonus_percent', 'sp4t_bonus_percent')
			->select_column('data_weapons.burst_ap_bonus', 'burst_ap_bonus')
			->select_column('data_weapons.afsp5ap_bonus', 'afsp5ap_bonus')
			->select_column('data_weapons.recoil_x_bonus', 'recoil_x_bonus')
			->select_column('data_weapons.recoil_x_bonus_percent', 'recoil_x_bonus_percent')
			->select_column('data_weapons.recoil_y_bonus', 'recoil_y_bonus')
			->select_column('data_weapons.recoil_y_bonus_percent', 'recoil_y_bonus_percent')
			->select_column('data_weapons.handling_bonus', 'handling_bonus')
			->select_column('data_weapons.handling_bonus_percent', 'handling_bonus_percent');
	}

	public static function update_weapons_builder_with_calibres(Jelly_Builder $builder) {
		$builder
			->select_column('calibres.name', 'calibre_name')
			->select_column('calibres.cartridge_weight', 'cartridge_weight')
			->select_column('calibres.coolness', 'calibre_coolness')
			->select_column('calibres.bullet_type', 'bullet_type')
			->select_column('calibres.bullet_weight', 'bullet_weight')
			->select_column('calibres.bullet_diameter', 'bullet_diameter')
			->select_column('calibres.bullet_coefficient', 'bullet_coefficient')
			->select_column('calibres.bullet_start_energy', 'bullet_start_energy')
			->select_column('calibres.bullet_start_speed', 'bullet_start_speed')
			->select_column('calibres.accuracy_angle', 'accuracy_angle')
			->select_column('calibres.accuracy_mult', 'accuracy_mult')
			->select_column('calibres.accuracy_delta', 'accuracy_delta')
			->select_column('calibres.accuracy_x', 'accuracy_x')
			->select_column('calibres.accuracy_weapon_id', 'accuracy_weapon_id')
			->select_column('calibres.accuracy_weapon', 'accuracy_weapon')
			->select_column('calibres.velocity_mult', 'velocity_mult')
			->select_column('calibres.range_angle', 'range_angle')
			->select_column('calibres.range_mult', 'range_mult')
			->select_column('calibres.range_div', 'range_div')
			->select_column('calibres.range_delta', 'range_delta')
			->select_column('calibres.range_weapon_id', 'range_weapon_id')
			->select_column('calibres.range_weapon', 'range_weapon')
			->select_column('calibres.sniper_range_bonus', 'sniper_range_bonus')
			->select_column('calibres.sniper_range_bonus_percent', 'sniper_range_bonus_percent')
			->select_column('calibres.sniper_accuracy_bonus', 'sniper_accuracy_bonus')
			->select_column('calibres.sniper_accuracy_bonus_percent', 'sniper_accuracy_bonus_percent')
			->select_column('calibres.damage', 'calibre_damage')
			->select_column('calibres.burst_recoil', 'calibre_burst_recoil')
			->select_column('calibres.auto_recoil', 'calibre_auto_recoil')
			->select_column('calibres.semi_speed', 'calibre_semi_speed')
			->select_column('calibres.sp4t_pistol_bonus', 'sp4t_pistol_bonus')
			->select_column('calibres.sp4t_mp_bonus', 'sp4t_mp_bonus')
			->select_column('calibres.sp4t_rifle_bonus', 'sp4t_rifle_bonus')
			->select_column('calibres.test_barrel_length', 'test_barrel_length');
	}

	/**
	 * @return Jelly_Builder
	 */
	public static function get_weapons_builder() {
		$builder = Core_Weapon_Mod::factory()->preset_for_admin()->get_builder()
			->join('data_weapons', 'LEFT')->on('data_weapons.uiIndex', '=', 'weapons_mod.uiIndex')
			->join('calibres', 'LEFT')->on('calibres.ubCalibre', '=', 'weapons_mod.ubCalibre')
			->join('items_mod', 'LEFT')->on('items_mod.uiIndex', '=', 'weapons_mod.uiIndex')
//			->where('APsToReloadManually', '>', 0)
			->where('weapons_mod.ubWeaponClass', 'IN', [
				Core_Weapon::CLASS_HANDGUN,
				Core_Weapon::CLASS_SMG,
				Core_Weapon::CLASS_RIFLE,
				Core_Weapon::CLASS_MACHINEGUN,
				Core_Weapon::CLASS_SHOTGUN,
			])
			->where('weapons_mod.ubWeaponType', '!=', Core_Weapon::TYPE_BLANK)
			->where('weapons_mod.ubWeaponType', 'IS NOT', NULL)
			->where('weapons_mod.ubCalibre', '>', 0)
			->select_column('weapons_mod.*')
			->select_column('items_mod.TwoHanded', 'TwoHanded')
			->select_column('items_mod.ubWeight', 'ubWeight')
			->select_column('items_mod.bReliability', 'bReliability')
			->select_column('items_mod.ubCoolness', 'ubCoolness')
			->select_column('items_mod.PercentNoiseReduction', 'PercentNoiseReduction')
			->select_column('items_mod.BR_ROF', 'BR_ROF')
			->select_column('items_mod.STAND_MODIFIERS', 'STAND_MODIFIERS')
			->select_column('items_mod.CROUCH_MODIFIERS', 'CROUCH_MODIFIERS')
			->select_column('items_mod.PRONE_MODIFIERS', 'PRONE_MODIFIERS');

		Core_Weapon_Mod::update_weapons_builder_with_data_weapons($builder);
		Core_Weapon_Mod::update_weapons_builder_with_calibres($builder);

		return $builder;
	}

	/**
	 * @param $uiIndex
	 *
	 * @return Jelly_Model
	 */
	public static function get_weapon($uiIndex) {
		$model = Core_Weapon_Mod::get_weapons_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		return $model;
	}

	/**
	 * @return array
	 */
	public function get_weapons_list() {
		$calibre = Session::instance()->get('calibre');

		$builder = $this->get_builder()
			->where('ubWeaponClass', 'IN', [
				Core_Weapon::CLASS_HANDGUN,
				Core_Weapon::CLASS_SMG,
				Core_Weapon::CLASS_RIFLE,
				Core_Weapon::CLASS_MACHINEGUN,
				Core_Weapon::CLASS_SHOTGUN,
			])
			->where('ubWeaponType', '!=', Core_Weapon::TYPE_BLANK)
			->where('ubWeaponType', 'IS NOT', NULL)
			->order_by('szWeaponName')
			->select_column('uiIndex')
			->select_column('szWeaponName');

		if (!empty($calibre)) {
			$builder->where('ubCalibre', '=', $calibre);
		} else {
			$builder->where('ubCalibre', '>', 0);
		}

		$weapon_name = Session::instance()->get('weapon_name');

		if ($weapon_name) {
			$builder->where('szWeaponName', 'LIKE', Helper_Html::prepare_value_for_sql($weapon_name));
		}

		return $builder
			->select_all()
			->as_array('uiIndex', 'szWeaponName');
	}

} // End Core_Weapon_Mod
