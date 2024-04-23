<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Slot_Mod
 * User: legion
 * Date: 13.10.19
 * Time: 1:07
 */
class Core_Slot_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Slot_Mod';
	protected $model_name = 'slot_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function get_weapon_nas_attachment_classes($nasAttachmentClass = null) {
		$builder = Core_Slot_Mod::factory()->get_builder()
			->where('uiSlotIndex', '>', 0)
			->where('nasLayoutClass', '=', 1)
			->order_by('szSlotName');

		$list = $builder
			->select_all()
			->as_array('nasAttachmentClass', 'szSlotName');

		if ($nasAttachmentClass && !array_key_exists($nasAttachmentClass, $list)) {
			$list[$nasAttachmentClass] = '!!! ' . $nasAttachmentClass;
		}

		return $list;
	}

} // End Core_Slot_Mod