<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Magazine
 * User: legion
 * Date: 08.11.19
 * Time: 14:58
 */
class Model_Magazine extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('magazines');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer(),
			'ubCalibre' => new Jelly_Field_Integer(),
			'ubMagSize' => new Jelly_Field_Integer(),
			'ubAmmoType' => new Jelly_Field_Integer(),
			'ubMagType' => new Jelly_Field_Integer(),
		));
	}

} // End Model_Magazine