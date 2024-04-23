<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Pocket
 * User: legion
 * Date: 14.11.19
 * Time: 23:15
 */
class Model_Pocket extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('pockets');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'pIndex' => new Jelly_Field_Integer(),
			'pName' => new Jelly_Field_String(),
			'pSilhouette' => new Jelly_Field_Integer(),
			'pType' => new Jelly_Field_Integer(),
			'pRestriction' => new Jelly_Field_Integer(),
			'pVolume' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize0' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize1' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize2' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize3' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize4' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize5' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize6' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize7' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize8' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize9' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize10' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize11' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize12' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize13' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize14' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize15' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize16' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize17' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize18' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize19' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize20' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize21' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize22' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize23' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize24' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize25' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize26' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize27' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize28' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize29' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize30' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize31' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize32' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize33' => new Jelly_Field_Integer(),
			'ItemCapacityPerSize34' => new Jelly_Field_Integer(),
		));
	}

} // End Model_Pocket