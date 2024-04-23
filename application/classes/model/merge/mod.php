<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Merge_Mod
 * User: legion
 * Date: 25.04.2021
 * Time: 10:29
 * @property integer id
 * @property integer firstItemIndex
 * @property integer secondItemIndex
 * @property integer firstResultingItemIndex
 * @property integer secondResultingItemIndex
 * @property integer mergeType
 * @property integer APCost
 */
class Model_Merge_Mod extends Model_Merge {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('merges_mod');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'firstItemIndex' => new Jelly_Field_Integer([
				'label' => 'Source Item',
			]),
			'secondItemIndex' => new Jelly_Field_Integer([
				'label' => 'Target Item',
			]),
			'firstResultingItemIndex' => new Jelly_Field_Integer([
				'label' => 'Target Item Result',
			]),
			'secondResultingItemIndex' => new Jelly_Field_Integer([
				'label' => 'Source Item Transformation',
			]),
			'mergeType' => new Jelly_Field_Integer([
				'label' => 'Merge Type',
			]),
			'APCost' => new Jelly_Field_Integer([
				'label' => 'AP Cost',
			]),
		));
	}

} // End Model_Merge_Mod