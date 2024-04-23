<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class Model_Weapon_Data
 *
 * @property integer $id
 * @property string $name_long
 * @property string $name_short
 * @property string $name_br
 * @property string $description
 * @property string $description_br
 * @property string $comment
 * @property string $manufacturer
 * @property string $constructor
 */
class Model_Weapon_Data extends Model_Weapon_Group {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('data_weapons');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),
			'calibre' => new Jelly_Field_Integer([
				'default' => 0,
			]),
			'name_long' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
				'allow_null' => true,
				'convert_empty' => true,
			]),
			'name_short' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
				'allow_null' => true,
				'convert_empty' => true,
			]),
			'name_br' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
				'allow_null' => true,
				'convert_empty' => true,
			]),
			'description' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							1000,
						),
					),
				),
				'allow_null' => true,
				'convert_empty' => true,
			]),
			'description_br' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							1000,
						),
					),
				),
				'allow_null' => true,
				'convert_empty' => true,
			]),
			'comment' => new Jelly_Field_Text(),
			'constructor' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
			]),
			'manufacturer' => new Jelly_Field_String([
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
			]),
			'year_of_adoption' => new Jelly_Field_Integer(),
			'year_of_withdrawal' => new Jelly_Field_Integer(),
			'amount_built' => new Jelly_Field_Integer(),
			'rarity' => new Jelly_Field_Integer(),

			'weapon_class' => new Jelly_Field_Integer(),
			'weapon_type' => new Jelly_Field_Integer(),

			'length_min' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'length_max' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'length_barrel' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'length_front_and_handle' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'length_front_to_trigger' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'height_diff_stock_barrel' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'fire_rate_semi' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'fire_rate_burst' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'fire_rate_auto_min' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'fire_rate_auto_max' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'burst_length' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'no_semi_auto' => new Jelly_Field_Boolean(),
			'no_full_auto' => new Jelly_Field_Boolean(),

			'mag_size' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'muzzle_velocity' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'weight' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'weight_empty' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'weight_loaded' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'weight_front_percent' => new Jelly_Field_Integer(),

			'mechanism_action' => new Jelly_Field_String(),
			'mechanism_trigger' => new Jelly_Field_String(),
			'mechanism_feature' => new Jelly_Field_String(),
			'mechanism_reload' => new Jelly_Field_String(),
			'comfort' => new Jelly_Field_String(),

			'weapon_qualities' => new Jelly_Field_Text([
				'label' => 'Weapon Qualities',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'default_attachments' => new Jelly_Field_Text([
				'label' => 'Default Attachments',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'possible_attachments' => new Jelly_Field_Text([
				'label' => 'Possible Attachments',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'attachment_mounts' => new Jelly_Field_Text([
				'label' => 'Attachment Mounts',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),

			'integrated_stock_name' => new Jelly_Field_String([
				'label' => 'Integrated Stock',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_stock_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_scope_name' => new Jelly_Field_String([
				'label' => 'Integrated Scope',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_scope_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_sight_name' => new Jelly_Field_String([
				'label' => 'Integrated Sight',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_sight_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_laser_name' => new Jelly_Field_String([
				'label' => 'Integrated Laser',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_laser_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_bipod_name' => new Jelly_Field_String([
				'label' => 'Integrated Bipod',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_bipod_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_foregrip_name' => new Jelly_Field_String([
				'label' => 'Integrated Foregrip',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_foregrip_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_suppressor_name' => new Jelly_Field_String([
				'label' => 'Integrated Suppressor',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'integrated_suppressor_index' => new Jelly_Field_Integer([
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),

			'has_hp_iron_sights' => new Jelly_Field_Boolean([
				'label' => 'High Profile Iron Sights',
				'default' => false,
			]),

			'has_hp_scope_mount' => new Jelly_Field_Boolean([
				'label' => 'High Profile Scope Mount',
				'default' => false,
			]),

			'has_hk_trigger' => new Jelly_Field_Boolean([
				'label' => 'AR Trigger',
				'default' => false,
			]),

			'has_mag_stanag' => new Jelly_Field_Boolean([
				'label' => 'STANAG Mag',
				'default' => false,
			]),
			'has_drum_mag' => new Jelly_Field_Boolean([
				'label' => 'Drum Mag',
				'default' => false,
			]),
			'has_calico_mag' => new Jelly_Field_Boolean([
				'label' => 'Calico Mag',
				'default' => false,
			]),
			'has_long_mag' => new Jelly_Field_Boolean([
				'label' => 'Long Mag',
				'default' => false,
			]),

			'has_bolt_hold_open' => new Jelly_Field_Boolean([
				'label' => 'Bolt Hold Open',
				'default' => false,
			]),
			'has_balanced_automatic' => new Jelly_Field_Boolean([
				'label' => 'Balanced Automatic',
				'default' => false,
			]),
			'has_recoil_reducing_stock' => new Jelly_Field_Boolean([
				'label' => 'Recoil-reducing Stock',
				'default' => false,
			]),
			'has_recoil_buffer_in_stock' => new Jelly_Field_Boolean([
				'label' => 'Recoil Buffer in Stock',
				'default' => false,
			]),
			'has_compensator' => new Jelly_Field_Boolean([
				'label' => 'Compensator',
				'default' => false,
			]),
			'has_muzzle_break' => new Jelly_Field_Boolean([
				'label' => 'Muzzle Break',
				'default' => false,
			]),

			'has_ported_barrel' => new Jelly_Field_Boolean([
				'label' => 'Ported Barrel',
				'default' => false,
			]),
			'has_heavy_barrel' => new Jelly_Field_Boolean([
				'label' => 'Heavy Barrel',
				'default' => false,
			]),
			'has_sniper_barrel' => new Jelly_Field_Boolean([
				'label' => 'Matching Barrel',
				'default' => false,
			]),
			'has_floating_barrel' => new Jelly_Field_Boolean([
				'label' => 'Free-Floating Barrel',
				'default' => false,
			]),
			'has_replaceable_barrel' => new Jelly_Field_Boolean([
				'label' => 'Replaceable Barrel',
				'default' => false,
			]),

			'has_cheek_piece' => new Jelly_Field_Boolean([
				'label' => 'Cheek-piece',
				'default' => false,
			]),
			'has_adjustable_cheek_piece' => new Jelly_Field_Boolean([
				'label' => 'Adjustable Cheek-piece',
				'default' => false,
			]),
			'has_adjustable_butt_stock' => new Jelly_Field_Boolean([
				'label' => 'Adjustable Butt-stock',
				'default' => false,
			]),
			'has_adjustable_grip' => new Jelly_Field_Boolean([
				'label' => 'Adjustable Grip',
				'default' => false,
			]),

			'is_two_handed' => new Jelly_Field_Boolean([
				'label' => 'Two-handed weapon',
				'allow_null' => true,
				'default' => false,
			]),
			'is_secondary_weapon' => new Jelly_Field_Boolean([
				'label' => 'Secondary Weapon',
				'default' => false,
			]),

			'targeting_range' => new Jelly_Field_Integer([
				'label' => 'Targeting range',
				'convert_empty' => true,
			]),
			'effective_range' => new Jelly_Field_Integer([
				'label' => 'Effective range',
				'convert_empty' => true,
			]),
			'moa_claimed' => new Jelly_Field_Float([
				'label' => 'MOA (claimed)',
				'convert_empty' => true,
			]),
			'moa_claimed_range' => new Jelly_Field_Integer([
				'label' => 'MOA range (claimed)',
				'convert_empty' => true,
			]),
			'moa_test_average' => new Jelly_Field_Float([
				'label' => 'MOA test average',
				'convert_empty' => true,
			]),
			'test_data' => new Jelly_Field_Text([
				'label' => 'Test Data',
			]),

			'accuracy_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'accuracy_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'range_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'range_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'ready_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'ready_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'sp4t_bonus' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'sp4t_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'aptrm_bonus' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'aptrm_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'reload_bonus' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'reload_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'burst_ap_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'afsp5ap_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'recoil_x_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'recoil_x_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'recoil_y_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'recoil_y_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'handling_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'handling_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
		));
	}

} // End Model_Weapon_Data
