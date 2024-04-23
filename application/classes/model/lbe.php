<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_LBE
 * User: legion
 * Date: 14.11.19
 * Time: 23:19
 */
class Model_LBE extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('load_bearing_equipment');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'lbeIndex' => new Jelly_Field_Integer(),
			'lbeClass' => new Jelly_Field_Integer(),
			'lbeCombo' => new Jelly_Field_Integer(),
			'lbeFilledSize' => new Jelly_Field_Integer(),
			'lbeAvailableVolume' => new Jelly_Field_Integer(),
			'lbePocketsAvailable' => new Jelly_Field_Integer(),
			'lbePocketIndex1' => new Jelly_Field_Integer(),
			'lbePocketIndex2' => new Jelly_Field_Integer(),
			'lbePocketIndex3' => new Jelly_Field_Integer(),
			'lbePocketIndex4' => new Jelly_Field_Integer(),
			'lbePocketIndex5' => new Jelly_Field_Integer(),
			'lbePocketIndex6' => new Jelly_Field_Integer(),
			'lbePocketIndex7' => new Jelly_Field_Integer(),
			'lbePocketIndex8' => new Jelly_Field_Integer(),
			'lbePocketIndex9' => new Jelly_Field_Integer(),
			'lbePocketIndex10' => new Jelly_Field_Integer(),
			'lbePocketIndex11' => new Jelly_Field_Integer(),
			'lbePocketIndex12' => new Jelly_Field_Integer(),
		));
	}

} // End Model_LBE