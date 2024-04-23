<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 27.03.2021
 * Time: 15:05
 * @property integer id
 * @property string npc_side
 * @property string npc_type
 * @property integer list_index
 * @property string list_name
 * @property integer uiIndex
 */
class Model_Choices_Item extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('choices_item');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'npc_side' => new Jelly_Field_Enum([
				'label' => 'NPC Side',
				'choices' => Core_Choices_Item::get_npc_sides(),
				'default' => Core_Choices_Item::NPC_SIDE_ENEMY,
			]),
			'npc_type' => new Jelly_Field_Enum([
				'label' => 'NPC Type',
				'choices' => Core_Choices_Item::get_npc_types(),
				'default' => Core_Choices_Item::NPC_TYPE_ADMIN,
			]),
			'list_index' => new Jelly_Field_Integer([
				'label' => 'List Index',
				'default' => 0,
			]),
			'list_name' => new Jelly_Field_String([
				'label' => 'List Name',
				'default' => 0,
			]),
			'uiIndex' => new Jelly_Field_Integer([
				'label' => 'uiIndex',
				'default' => 0,
			]),
		));
	}

}