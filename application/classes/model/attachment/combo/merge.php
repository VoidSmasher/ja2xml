<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Attachment_Combo_Merge
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
class Model_Attachment_Combo_Merge extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('attachment_combo_merges');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer(),
			'usItem' => new Jelly_Field_Integer(),
			'usAttachment1' => new Jelly_Field_Integer(),
			'usAttachment2' => new Jelly_Field_Integer(),
			'usAttachment3' => new Jelly_Field_Integer(),
			'usAttachment4' => new Jelly_Field_Integer(),
			'usAttachment5' => new Jelly_Field_Integer(),
			'usAttachment6' => new Jelly_Field_Integer(),
			'usAttachment7' => new Jelly_Field_Integer(),
			'usAttachment8' => new Jelly_Field_Integer(),
			'usAttachment9' => new Jelly_Field_Integer(),
			'usAttachment10' => new Jelly_Field_Integer(),
			'usAttachment11' => new Jelly_Field_Integer(),
			'usAttachment12' => new Jelly_Field_Integer(),
			'usAttachment13' => new Jelly_Field_Integer(),
			'usAttachment14' => new Jelly_Field_Integer(),
			'usAttachment15' => new Jelly_Field_Integer(),
			'usAttachment16' => new Jelly_Field_Integer(),
			'usAttachment17' => new Jelly_Field_Integer(),
			'usAttachment18' => new Jelly_Field_Integer(),
			'usAttachment19' => new Jelly_Field_Integer(),
			'usAttachment20' => new Jelly_Field_Integer(),
			'usResult' => new Jelly_Field_Integer(),
		));
	}

} // End Model_Attachment_Combo_Merge