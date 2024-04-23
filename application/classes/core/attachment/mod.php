<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachment_Mod
 * User: legion
 * Date: 12.10.19
 * Time: 19:54
 */
class Core_Attachment_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Attachment_Mod';
	protected $model_name = 'attachment_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/**
	 * @param bool $mod
	 * @return Jelly_Builder
	 */
	public static function get_weapons_builder($mod = true) {
		if ($mod) {
			$builder = Core_Attachment_Mod::factory()->preset_for_admin()->get_builder();
			$table = 'attachments_mod';
		} else {
			$builder = Core_Attachment::factory()->preset_for_admin()->get_builder();
			$table = 'attachments';
		}

		$builder
			->join('data_weapons', 'LEFT')->on('data_weapons.uiIndex', '=', $table . '.itemIndex')
			->join('weapons_mod', 'LEFT')->on('weapons_mod.uiIndex', '=', $table . '.itemIndex')
			->join(['data_attachments', 'attach_info'], 'LEFT')->on('attach_info.uiIndex', '=', $table . '.attachmentIndex')
			->join(['items_mod', 'items_item'], 'LEFT')->on('items_item.uiIndex', '=', $table . '.itemIndex')
			->join(['items_mod', 'items_attach'], 'LEFT')->on('items_attach.uiIndex', '=', $table . '.attachmentIndex')
			->where('weapons_mod.ubWeaponClass', 'IN', [
				Core_Weapon::CLASS_HANDGUN,
				Core_Weapon::CLASS_SMG,
				Core_Weapon::CLASS_RIFLE,
				Core_Weapon::CLASS_MACHINEGUN,
				Core_Weapon::CLASS_SHOTGUN,
			])
			->where('weapons_mod.ubWeaponType', '!=', Core_Weapon::TYPE_BLANK)
			->where('weapons_mod.ubWeaponType', 'IS NOT', NULL)
			->where('weapons_mod.ubCalibre', '>', 0)
			->select_column($table . '.*')
			->select_column('weapons_mod.szWeaponName', 'szWeaponName')
			->select_column('weapons_mod.ubWeaponType', 'ubWeaponType')
			->select_column('weapons_mod.ubCalibre', 'ubCalibre')
			->select_column('attach_info.is_fixed', 'is_fixed')
			->select_column('attach_info.attachment_types', 'attachment_types')
			->select_column('items_item.uiIndex', 'uiIndex')
			->select_column('items_item.TwoHanded', 'TwoHanded')
			->select_column('items_item.szLongItemName', 'szLongItemName')
			->select_column('items_item.DefaultAttachment', 'DefaultAttachment')
			->select_column('items_attach.szLongItemName', 'attach_name')
			->select_column('items_attach.nasAttachmentClass', 'attach_nasAttachmentClass');

		Core_Weapon_Mod::update_weapons_builder_with_data_weapons($builder);

		return $builder;
	}

	/**
	 * @param bool $mod
	 * @return Jelly_Builder
	 */
	public static function get_attachments_builder($mod = true) {
		if ($mod) {
			$builder = Core_Attachment_Mod::factory()->preset_for_admin()->get_builder();
			$table = 'attachments_mod';
		} else {
			$builder = Core_Attachment::factory()->preset_for_admin()->get_builder();
			$table = 'attachments';
		}

		$builder
			->join('data_attachments')->on('data_attachments.uiIndex', '=', $table . '.itemIndex')
			->join(['data_attachments', 'attach_info'], 'LEFT')->on('attach_info.uiIndex', '=', $table . '.attachmentIndex')
			->join(['items_mod', 'items_item'], 'LEFT')->on('items_item.uiIndex', '=', $table . '.itemIndex')
			->join(['items_mod', 'items_attach'], 'LEFT')->on('items_attach.uiIndex', '=', $table . '.attachmentIndex')
			->select_column($table . '.*')
			->select_column('attach_info.is_fixed', 'is_fixed')
			->select_column('attach_info.attachment_types', 'attachment_types')
			->select_column('items_item.uiIndex', 'uiIndex')
			->select_column('items_item.szLongItemName', 'szLongItemName')
			->select_column('items_item.DefaultAttachment', 'DefaultAttachment')
			->select_column('items_attach.szLongItemName', 'attach_name')
			->select_column('items_attach.nasAttachmentClass', 'nasAttachmentClass');

		Core_Attachment_Data::update_builder_with_data_attachments($builder);

		return $builder;
	}

	/**
	 * @param $item_index
	 * @param $attach_index
	 * @return Jelly_Model
	 */
	public static function get_attachment($item_index, $attach_index) {
		$attachment = Core_Attachment_Mod::factory()->get_builder()
			->where('itemIndex', '=', $item_index)
			->where('attachmentIndex', '=', $attach_index)
			->limit(1)
			->select();

		return $attachment;
	}

	/**
	 * @param $item_index
	 * @param $attach_index
	 * @param $ap_cost
	 * @param Jelly_Model|NULL $attachment
	 * @return Jelly_Model
	 */
	public static function set_attachment($item_index, $attach_index, $ap_cost, Jelly_Model $attachment = NULL) {
		if (!($attachment instanceof Jelly_Model)) {
			$attachment = Core_Attachment_Mod::get_attachment($item_index, $attach_index);
		}
		$attachment->itemIndex = $item_index;
		$attachment->attachmentIndex = $attach_index;
		$attachment->APCost = $ap_cost;

		return $attachment;
	}

} // End Core_Attachment_Mod