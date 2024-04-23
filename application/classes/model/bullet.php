<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Bullet
 * User: legion
 * Date: 26.09.19
 * Time: 5:21
 */
class Model_Bullet extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('bullets');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'ubCalibre' => new Jelly_Field_Integer([
				'allow_null' => false,
				'default' => 0,
			]),

			'test_barrel_length' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'test_barrel_length_in' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),

			'bullet_name' => new Jelly_Field_String([
				'label' => __('common.name'),
				'convert_empty' => true,
			]),
			'bullet_weight' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_weight_gran' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_start_speed' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'bullet_start_energy' => new Jelly_Field_Float([
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

			'pellet_weight' => new Jelly_Field_Float([
				'convert_empty' => true,
			]),
			'pellet_number' => new Jelly_Field_Integer([
				'convert_empty' => true,
			]),

		));
	}

} // End Model_Bullet