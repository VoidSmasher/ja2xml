<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Item_Transformation_Mod
 * User: legion
 * Date: 08.05.2021
 * Time: 13:30
 * @property integer usItem
 * @property integer usResult1
 * @property integer usResult2
 * @property integer usResult3
 * @property integer usResult4
 * @property integer usResult5
 * @property integer usResult6
 * @property integer usResult7
 * @property integer usResult8
 * @property integer usResult9
 * @property integer usResult10
 * @property integer usAPCost
 * @property integer iBPCost
 * @property string szMenuRowText
 * @property string szTooltipText
 */
class Model_Item_Transformation_Mod extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('item_transformations_mod');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'usItem' => new Jelly_Field_Integer([
				'title' => 'Source Item',
			]),
			'usResult1' => new Jelly_Field_Integer([
				'title' => 'Result 1',
			]),
			'usResult2' => new Jelly_Field_Integer([
				'title' => 'Result 2',
			]),
			'usResult3' => new Jelly_Field_Integer([
				'title' => 'Result 3',
			]),
			'usResult4' => new Jelly_Field_Integer([
				'title' => 'Result 4',
			]),
			'usResult5' => new Jelly_Field_Integer([
				'title' => 'Result 5',
			]),
			'usResult6' => new Jelly_Field_Integer([
				'title' => 'Result 6',
			]),
			'usResult7' => new Jelly_Field_Integer([
				'title' => 'Result 7',
			]),
			'usResult8' => new Jelly_Field_Integer([
				'title' => 'Result 8',
			]),
			'usResult9' => new Jelly_Field_Integer([
				'title' => 'Result 9',
			]),
			'usResult10' => new Jelly_Field_Integer([
				'title' => 'Result 10',
			]),
			'usAPCost' => new Jelly_Field_Integer([
				'title' => 'AP Cost',
			]),
			'iBPCost' => new Jelly_Field_Integer([
				'title' => 'BP Cost',
			]),
			'szMenuRowText' => new Jelly_Field_String([
				'title' => 'Menu Row Text',
			]),
			'szTooltipText' => new Jelly_Field_String([
				'title' => 'Tooltip Text',
			]),
		));
	}

} // End Model_Item_Transformation_Mod