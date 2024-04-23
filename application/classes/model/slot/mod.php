<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Slot_Mod
 * User: legion
 * Date: 13.10.19
 * Time: 1:02
 * @property integer id
 * @property integer uiSlotIndex
 * @property string szSlotName
 * @property integer nasAttachmentClass
 * @property integer nasLayoutClass
 * @property integer usDescPanelPosX
 * @property integer usDescPanelPosY
 * @property integer fMultiShot
 * @property integer fBigSlot
 * @property integer ubPocketMapping
 */
class Model_Slot_Mod extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('slots_mod');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiSlotIndex' => new Jelly_Field_Integer([
				'default' => 0,
			]),
			'szSlotName' => new Jelly_Field_String([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'nasAttachmentClass' => new Jelly_Field_Integer([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'nasLayoutClass' => new Jelly_Field_Integer([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'usDescPanelPosX' => new Jelly_Field_Integer([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'usDescPanelPosY' => new Jelly_Field_Integer([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'fMultiShot' => new Jelly_Field_Integer([
				'default' => 0,
			]),
			'fBigSlot' => new Jelly_Field_Integer([
				'default' => 0,
			]),
			'ubPocketMapping' => new Jelly_Field_Integer([
				'default' => 0,
			]),
		));
	}

} // End Model_Slot_Mod