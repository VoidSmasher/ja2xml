<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Attachment_Combo_Merge_Mod
 * User: legion
 * Date: 08.05.2021
 * Time: 12:12
 * @property integer uiIndex
 * @property integer usItem
 * @property integer usAttachment1
 * @property integer usAttachment2
 * @property integer usAttachment3
 * @property integer usAttachment4
 * @property integer usAttachment5
 * @property integer usAttachment6
 * @property integer usAttachment7
 * @property integer usAttachment8
 * @property integer usAttachment9
 * @property integer usAttachment10
 * @property integer usAttachment11
 * @property integer usAttachment12
 * @property integer usAttachment13
 * @property integer usAttachment14
 * @property integer usAttachment15
 * @property integer usAttachment16
 * @property integer usAttachment17
 * @property integer usAttachment18
 * @property integer usAttachment19
 * @property integer usAttachment20
 * @property integer usResult
 */
class Model_Attachment_Combo_Merge_Mod extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('attachment_combo_merges_mod');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer([
				'label' => 'ACM id',
			]),
			'usItem' => new Jelly_Field_Integer([
				'label' => 'Source Item',
			]),
			'usAttachment1' => new Jelly_Field_Integer([
				'label' => 'Attachment 1',
			]),
			'usAttachment2' => new Jelly_Field_Integer([
				'label' => 'Attachment 2',
			]),
			'usAttachment3' => new Jelly_Field_Integer([
				'label' => 'Attachment 3',
			]),
			'usAttachment4' => new Jelly_Field_Integer([
				'label' => 'Attachment 4',
			]),
			'usAttachment5' => new Jelly_Field_Integer([
				'label' => 'Attachment 5',
			]),
			'usAttachment6' => new Jelly_Field_Integer([
				'label' => 'Attachment 6',
			]),
			'usAttachment7' => new Jelly_Field_Integer([
				'label' => 'Attachment 7',
			]),
			'usAttachment8' => new Jelly_Field_Integer([
				'label' => 'Attachment 8',
			]),
			'usAttachment9' => new Jelly_Field_Integer([
				'label' => 'Attachment 9',
			]),
			'usAttachment10' => new Jelly_Field_Integer([
				'label' => 'Attachment 10',
			]),
			'usAttachment11' => new Jelly_Field_Integer([
				'label' => 'Attachment 11',
			]),
			'usAttachment12' => new Jelly_Field_Integer([
				'label' => 'Attachment 12',
			]),
			'usAttachment13' => new Jelly_Field_Integer([
				'label' => 'Attachment 13',
			]),
			'usAttachment14' => new Jelly_Field_Integer([
				'label' => 'Attachment 14',
			]),
			'usAttachment15' => new Jelly_Field_Integer([
				'label' => 'Attachment 15',
			]),
			'usAttachment16' => new Jelly_Field_Integer([
				'label' => 'Attachment 16',
			]),
			'usAttachment17' => new Jelly_Field_Integer([
				'label' => 'Attachment 17',
			]),
			'usAttachment18' => new Jelly_Field_Integer([
				'label' => 'Attachment 18',
			]),
			'usAttachment19' => new Jelly_Field_Integer([
				'label' => 'Attachment 19',
			]),
			'usAttachment20' => new Jelly_Field_Integer([
				'label' => 'Attachment 20',
			]),
			'usResult' => new Jelly_Field_Integer([
				'label' => 'Result Item',
			]),
		));
	}

} // End Model_Attachment_Combo_Merge_Mod