<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Calibre
 * User: legion
 * Date: 05.05.18
 * Time: 8:19
 *
 * Calibre fields
 * @property string calibre_name
 * @property string cartridge_name
 * @property float cartridge_weight
 * @property float cartridge_length
 * @property float case_length
 * @property integer calibre_coolness
 * @property string bullet_type
 * @property float bullet_weight
 * @property float bullet_diameter
 * @property float bullet_coefficient
 * @property float test_barrel_length
 * @property integer bullet_start_energy
 * @property integer bullet_start_speed
 * @property float accuracy_angle
 * @property float accuracy_mult
 * @property integer accuracy_delta
 * @property integer accuracy_x
 * @property integer accuracy_weapon_id
 * @property integer accuracy_weapon
 * @property float velocity_mult
 * @property float range_angle
 * @property float range_mult
 * @property float range_div
 * @property integer range_delta
 * @property integer range_weapon_id
 * @property integer range_weapon
 * @property integer sniper_range_bonus
 * @property integer sniper_range_bonus_percent
 * @property integer sniper_accuracy_bonus
 * @property integer sniper_accuracy_bonus_percent
 * @property float damage
 * @property float semi_speed
 * @property float burst_recoil
 * @property float auto_recoil
 * @property float sp4t_pistol_bonus
 * @property float sp4t_mp_bonus
 * @property float sp4t_rifle_bonus
 */
class Model_Calibre extends Jelly_Model {

	public $calc_bullet_speed = array();
	public $calc_energy = array();

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('calibres');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'bullet_id' => new Jelly_Field_Integer(),
			'ubCalibre' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),
			'coolness' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),
			'name' => new Jelly_Field_String([
				'allow_null' => false,
				'default' => 'Nothing',
			]),

			'cartridge_name' => new Jelly_Field_String([
				'convert_empty' => true,
			]),
			'cartridge_weight' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'cartridge_length' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'case_length' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'pellet_weight' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'pellet_number' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'bullet_diameter' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_diameter_in' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'bullet_type' => new Jelly_Field_String([
				'default' => NULL,
			]),

			'bullet_name' => new Jelly_Field_String([
				'convert_empty' => true,
			]),
			'bullet_weight' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_weight_gran' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'bullet_coefficient' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_coefficient_g1' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_coefficient_g7' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'bullet_start_speed' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_start_energy' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'test_barrel_length' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'damage' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'semi_speed' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'burst_recoil' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'auto_recoil' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'accuracy_angle' => new Jelly_Field_Float([
				'allow_null' => false,
				'default' => 0,
			]),
			'accuracy_mult' => new Jelly_Field_Float([
				'allow_null' => false,
				'default' => 0,
			]),
			'accuracy_delta' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),
			'accuracy_x' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),
			'accuracy_weapon_id' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'accuracy_weapon' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'velocity_mult' => new Jelly_Field_Float([
				'allow_null' => false,
				'default' => 1,
			]),

			'range_angle' => new Jelly_Field_Float([
				'allow_null' => false,
				'default' => 1,
			]),
			'range_mult' => new Jelly_Field_Float([
				'allow_null' => false,
				'default' => 1,
			]),
			'range_div' => new Jelly_Field_Float([
				'allow_null' => false,
				'default' => 1,
			]),
			'range_delta' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),
			'range_weapon_id' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'range_weapon' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'sniper_range_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'sniper_range_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'sniper_accuracy_bonus' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),
			'sniper_accuracy_bonus_percent' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

			'sp4t_pistol_bonus' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'sp4t_mp_bonus' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'sp4t_rifle_bonus' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
		));
	}

} // End Model_Calibre