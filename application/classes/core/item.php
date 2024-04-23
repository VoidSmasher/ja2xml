<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Item
 * User: legion
 * Date: 08.05.18
 * Time: 21:30
 */
class Core_Item extends Core_Common {

	const ATTACHMENT_CLASS_MUZZLE = 2;
	const ATTACHMENT_CLASS_LASER = 4;
	const ATTACHMENT_CLASS_SIGHT = 8;
	const ATTACHMENT_CLASS_LASER_SIGHT = 12;
	const ATTACHMENT_CLASS_SCOPE = 16;
	const ATTACHMENT_CLASS_SCOPE_SIGHT = 24;
	const ATTACHMENT_CLASS_STOCK = 32;
	const ATTACHMENT_CLASS_MAG_ADAPTER = 64;
	const ATTACHMENT_CLASS_INTERNAL = 128;
	const ATTACHMENT_CLASS_EXTERNAL = 256;
	const ATTACHMENT_CLASS_GRENADE = 1024;
	const ATTACHMENT_CLASS_ROCKET = 2048;
	const ATTACHMENT_CLASS_FOREGRIP = 4096;
	const ATTACHMENT_CLASS_GUN_BARREL_EXTENDER = 262144;
	const ATTACHMENT_CLASS_RIFLE_SLING = 524288;
	const ATTACHMENT_CLASS_MOLLE = 16777216;
	const ATTACHMENT_CLASS_RIFLE_GRENADE_DEVICE = 33554432;

	const NAS_ATTACHMENT_CLASS_MUZZLE = 2;
	const NAS_ATTACHMENT_CLASS_LASER = 4;
	const NAS_ATTACHMENT_CLASS_SIGHT = 8;
	const NAS_ATTACHMENT_CLASS_SCOPE = 16;
	const NAS_ATTACHMENT_CLASS_STOCK = 32;
	const NAS_ATTACHMENT_CLASS_MAG_ADAPTER = 64;
	const NAS_ATTACHMENT_CLASS_INTERNAL = 128;
	const NAS_ATTACHMENT_CLASS_EXTERNAL = 256;
	const NAS_ATTACHMENT_CLASS_UNDER_BARREL = 512;
	const NAS_ATTACHMENT_CLASS_GRENADE = 1024;
	const NAS_ATTACHMENT_CLASS_ROCKET = 2048;
	const NAS_ATTACHMENT_CLASS_MOLLE_SMALL = 4096;
	const NAS_ATTACHMENT_CLASS_MOLLE_LARGE = 8192;

	const NAS_ATTACHMENT_CLASS_SUB_BARREL = 16384;

	const NAS_LAYOUT_CLASS_WEAPON = 1;
	const NAS_LAYOUT_CLASS_LAUNCHER = 2;
	const NAS_LAYOUT_CLASS_MOLLE_LEG = 4;
	const NAS_LAYOUT_CLASS_MOLLE_VEST = 8;
	const NAS_LAYOUT_CLASS_MOLLE_COMBAT_PACK = 16;
	const NAS_LAYOUT_CLASS_MOLLE_BACKPACK = 32;

	const CLASS_WEAPON = 2;

	const PARAM_SAVE = 'field_save';
	const PARAM_MERGE = 'field_merge';
	const PARAM_ROUND = 'field_round';

	/*
	 * При автоматическом слиянии нужно определить по какому правилу заменять значения.
	 * Сравнение показателей проводится по абсолюту.
	 * OVERWRITE - прямая перезапись старого значения новым
	 * OVERWRITE_MAX - из двух значений выберется наибольшее
	 * OVERWRITE_MIN - из двух значений выберется наименьшее
	 * OVERWRITE_BOOLEAN - TRUE имеет приоритет над FALSE
	 * OVERWRITE_NOT_EMPTY - старое будет переписано, если новое не NULL
	 */
	const FIELD_OVERWRITE = 'overwrite';
	const FIELD_OVERWRITE_MIN = 'overwrite_min';
	const FIELD_OVERWRITE_MAX = 'overwrite_max';
	const FIELD_OVERWRITE_BOOLEAN = 'overwrite_boolean';
	const FIELD_OVERWRITE_NOT_EMPTY = 'overwrite_humble';

	const FIELD_INCREMENT = 'increment';
	const FIELD_INCREMENT_JSON = 'increment_json';
	const FIELD_SUMMARY_JSON = 'increment_json';

	const FIELD_STANCE = 'stance';
	const FIELD_MERGE_MANUAL = 'merge_manual';

	const FIELD_ROUND_TO_FIVE = 'round_to_five';

	use Core_Common_Static;
	use Core_Item_Calculate_Coolness;
	use Core_Item_Calculate_Dirt;
	use Core_Item_Calculate_Overheating;
	use Core_Item_Calculate_Reliability;
	use Core_Item_Calculate_Repair;
	use Core_Item_Calculate_Selfdamage;
	use Core_Item_Calculate_Size;

	protected static $model_class = 'Model_Item';
	protected $model_name = 'item';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * IMAGE
	 */

	public static function get_image($item_index) {
		$image_new = '/assets/ja2/images/items-new/' . $item_index . '.png';
		$image = '/assets/ja2/images/items/' . $item_index . '.png';

		if (file_exists(APPPATH . '../htdocs' . $image_new)) {
			$image = HTML::image($image_new);
		} else {
			if (file_exists(APPPATH . '../htdocs' . $image)) {
				$image = HTML::image($image);
			} else {
				$image = '';
			}
		}

		return $image;
	}

	public static function row_image(Jelly_Model $model, Force_List_Row $row, $field = 'image') {
		$model->format($field, Core_Item::get_image($model->uiIndex));
		$row->cell($field)
			->attribute('style', 'background-color:#CA9;text-align: center');
	}

	/*
	 * STANCE to STRING
	 */

	public static function convert_stance_modifiers(Jelly_Model $model) {
		foreach (array(
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
		) as $field) {
			$json = $model->{$field};

			if (empty($json)) {
				continue;
			}

			$values = json_decode($json, true);

			$text = array();

			foreach ($values as $key => $value) {
				$text[] = $key . ':&nbsp;' . $value;
			}

			$text = Helper_String::to_string($text, '<br/>');

			$model->format($field, $text);
		}
	}

	/*
	 * FIELD STATUS
	 */

	public static function check_field_save_status(Jelly_Field $field) {
		return property_exists($field, Core_Item::PARAM_SAVE);
	}

	public static function get_field_save_status(Jelly_Field $field) {
		if (Core_Item::check_field_save_status($field)) {
			return $field->{Core_Item::PARAM_SAVE};
		}
		return NULL;
	}

	public static function check_field_merge_status(Jelly_Field $field) {
		return property_exists($field, Core_Item::PARAM_MERGE);
	}

	public static function get_field_merge_status(Jelly_Field $field) {
		if (Core_Item::check_field_merge_status($field)) {
			return $field->{Core_Item::PARAM_MERGE};
		}
		return NULL;
	}

	public static function check_field_round_status(Jelly_Field $field) {
		return property_exists($field, Core_Item::PARAM_ROUND);
	}

	public static function get_field_round_status(Jelly_Field $field) {
		if (Core_Item::check_field_round_status($field)) {
			return $field->{Core_Item::PARAM_ROUND};
		}
		return NULL;
	}

	/*
	 * AttachmentClass
	 */

	public static function get_attachment_classes($AttachmentClass = null) {
		$classes = array(
			self::ATTACHMENT_CLASS_FOREGRIP,
			self::ATTACHMENT_CLASS_GRENADE,
			self::ATTACHMENT_CLASS_GUN_BARREL_EXTENDER,
			self::ATTACHMENT_CLASS_INTERNAL,
			self::ATTACHMENT_CLASS_EXTERNAL,
			self::ATTACHMENT_CLASS_LASER,
			self::ATTACHMENT_CLASS_LASER_SIGHT,
			self::ATTACHMENT_CLASS_MAG_ADAPTER,
			self::ATTACHMENT_CLASS_MUZZLE,
			self::ATTACHMENT_CLASS_RIFLE_GRENADE_DEVICE,
			self::ATTACHMENT_CLASS_RIFLE_SLING,
			self::ATTACHMENT_CLASS_ROCKET,
			self::ATTACHMENT_CLASS_SCOPE,
			self::ATTACHMENT_CLASS_SCOPE_SIGHT,
			self::ATTACHMENT_CLASS_SIGHT,
			self::ATTACHMENT_CLASS_STOCK,
			self::ATTACHMENT_CLASS_MOLLE,
		);
		$list = array();
		foreach ($classes as $class) {
			$list[$class] = self::get_AttachmentClass_name($class);
		}

		/*
		 * Добавляем класс, если такого не оказалось в списке.
		 */
		if ($AttachmentClass && !array_key_exists($AttachmentClass, $list)) {
			$list[$AttachmentClass] = '!!! ' . $AttachmentClass;
		}
		return $list;
	}

	public static function get_AttachmentClass_name($AttachmentClass) {
		switch ($AttachmentClass) {
			case self::ATTACHMENT_CLASS_MUZZLE:
				$name = 'Muzzle';
				break;
			case self::ATTACHMENT_CLASS_LASER:
				$name = 'Laser';
				break;
			case self::ATTACHMENT_CLASS_SIGHT:
				$name = 'Sight';
				break;
			case self::ATTACHMENT_CLASS_LASER_SIGHT:
				$name = 'Laser Sight';
				break;
			case self::ATTACHMENT_CLASS_SCOPE:
				$name = 'Scope';
				break;
			case self::ATTACHMENT_CLASS_SCOPE_SIGHT:
				$name = 'Scope Sight';
				break;
			case self::ATTACHMENT_CLASS_STOCK:
				$name = 'Stock';
				break;
			case self::ATTACHMENT_CLASS_MAG_ADAPTER:
				$name = 'Mag. Adapter';
				break;
			case self::ATTACHMENT_CLASS_INTERNAL:
				$name = 'Internal';
				break;
			case self::ATTACHMENT_CLASS_EXTERNAL:
				$name = 'External';
				break;
			case self::ATTACHMENT_CLASS_GRENADE:
				$name = 'Grenade';
				break;
			case self::ATTACHMENT_CLASS_ROCKET:
				$name = 'Rocket';
				break;
			case self::ATTACHMENT_CLASS_FOREGRIP:
				$name = 'Foregrip';
				break;
			case self::ATTACHMENT_CLASS_GUN_BARREL_EXTENDER:
				$name = 'Barrel Extender';
				break;
			case self::ATTACHMENT_CLASS_RIFLE_SLING:
				$name = 'Sling';
				break;
			case self::ATTACHMENT_CLASS_MOLLE:
				$name = 'MOLLE';
				break;
			case self::ATTACHMENT_CLASS_RIFLE_GRENADE_DEVICE:
				$name = 'Grenade Device';
				break;
			default:
				$name = $AttachmentClass;
		}
		return $name;
	}

	public static function get_AttachmentClass_label($AttachmentClass, $caption = NULL) {
		if (empty($caption)) {
			$caption = self::get_AttachmentClass_name($AttachmentClass);
		}
		$label = Force_Label::factory($caption);

		switch ($AttachmentClass) {
			case self::ATTACHMENT_CLASS_MUZZLE:
			case self::ATTACHMENT_CLASS_RIFLE_GRENADE_DEVICE:
			case self::ATTACHMENT_CLASS_LASER:
			case self::ATTACHMENT_CLASS_GUN_BARREL_EXTENDER:
				$label->color_yellow();
				break;
			case self::ATTACHMENT_CLASS_SIGHT:
			case self::ATTACHMENT_CLASS_SCOPE:
			case self::ATTACHMENT_CLASS_SCOPE_SIGHT:
			case self::ATTACHMENT_CLASS_LASER_SIGHT:
				$label->color_cyan();
				break;
			case self::ATTACHMENT_CLASS_MAG_ADAPTER:
				$label->color_green();
				break;
			case self::ATTACHMENT_CLASS_STOCK:
			case self::ATTACHMENT_CLASS_FOREGRIP:
			case self::ATTACHMENT_CLASS_MOLLE:
				$label->color_blue();
				break;
			default:
				$label->color_gray();
		}

		return $label;
	}

	/*
	 * nasAttachmentClass
	 */

	public static function get_default_nas_attachment_classes() {
		return array(
			self::NAS_ATTACHMENT_CLASS_MUZZLE => self::NAS_ATTACHMENT_CLASS_MUZZLE,
			self::NAS_ATTACHMENT_CLASS_LASER => self::NAS_ATTACHMENT_CLASS_LASER,
			self::NAS_ATTACHMENT_CLASS_SIGHT => self::NAS_ATTACHMENT_CLASS_SIGHT,
			self::NAS_ATTACHMENT_CLASS_SCOPE => self::NAS_ATTACHMENT_CLASS_SCOPE,
			self::NAS_ATTACHMENT_CLASS_STOCK => self::NAS_ATTACHMENT_CLASS_STOCK,
			self::NAS_ATTACHMENT_CLASS_MAG_ADAPTER => self::NAS_ATTACHMENT_CLASS_MAG_ADAPTER,
			self::NAS_ATTACHMENT_CLASS_INTERNAL => self::NAS_ATTACHMENT_CLASS_INTERNAL,
			self::NAS_ATTACHMENT_CLASS_EXTERNAL => self::NAS_ATTACHMENT_CLASS_EXTERNAL,
			self::NAS_ATTACHMENT_CLASS_UNDER_BARREL => self::NAS_ATTACHMENT_CLASS_UNDER_BARREL,
		);
	}

	public static function get_nasAttachmentClass_name($nasAttachmentClass) {
		switch ($nasAttachmentClass) {
			case self::NAS_ATTACHMENT_CLASS_MUZZLE:
				$name = 'Muzzle';
				break;
			case self::NAS_ATTACHMENT_CLASS_LASER:
				$name = 'Laser';
				break;
			case self::NAS_ATTACHMENT_CLASS_SIGHT:
				$name = 'Sight';
				break;
			case self::NAS_ATTACHMENT_CLASS_SCOPE:
				$name = 'Scope';
				break;
			case self::NAS_ATTACHMENT_CLASS_STOCK:
				$name = 'Stock';
				break;
			case self::NAS_ATTACHMENT_CLASS_MAG_ADAPTER:
				$name = 'Mag. Adapter';
				break;
			case self::NAS_ATTACHMENT_CLASS_INTERNAL:
				$name = 'Internal';
				break;
			case self::NAS_ATTACHMENT_CLASS_EXTERNAL:
				$name = 'External';
				break;
			case self::NAS_ATTACHMENT_CLASS_UNDER_BARREL:
				$name = 'Barrel';
				break;
			case self::NAS_ATTACHMENT_CLASS_GRENADE:
				$name = 'Grenade';
				break;
			case self::NAS_ATTACHMENT_CLASS_ROCKET:
				$name = 'Rocket';
				break;
			case self::NAS_ATTACHMENT_CLASS_MOLLE_SMALL:
				$name = 'MOLLE Small';
				break;
			case self::NAS_ATTACHMENT_CLASS_MOLLE_LARGE:
				$name = 'MOLLE Large';
				break;
			default:
				$name = $nasAttachmentClass;
		}
		return $name;
	}

	public static function get_nasAttachmentClass_label($nasAttachmentClass, $caption = null) {
		if (empty($caption)) {
			$caption = self::get_nasAttachmentClass_name($nasAttachmentClass);
		}
		$label = Force_Label::factory($caption);

		switch ($nasAttachmentClass) {
			case self::NAS_ATTACHMENT_CLASS_MUZZLE:
			case self::NAS_ATTACHMENT_CLASS_LASER:
				$label->color_yellow();
				break;
			case self::NAS_ATTACHMENT_CLASS_SIGHT:
			case self::NAS_ATTACHMENT_CLASS_SCOPE:
				$label->color_cyan();
				break;
			case self::NAS_ATTACHMENT_CLASS_MAG_ADAPTER:
			case self::NAS_ATTACHMENT_CLASS_MOLLE_LARGE:
				$label->color_green();
				break;
			case self::NAS_ATTACHMENT_CLASS_STOCK:
			case self::NAS_ATTACHMENT_CLASS_UNDER_BARREL:
			case self::NAS_ATTACHMENT_CLASS_MOLLE_SMALL:
				$label->color_blue();
				break;
			case self::NAS_ATTACHMENT_CLASS_INTERNAL:
			case self::NAS_ATTACHMENT_CLASS_EXTERNAL:
				$label->color_gray();
				break;
		}

		return $label;
	}

	/*
	 * nasLayoutClass
	 */

	public static function get_nas_layout_classes($nasLayoutClass = null) {
		$classes = array(
			self::NAS_LAYOUT_CLASS_WEAPON,
			self::NAS_LAYOUT_CLASS_LAUNCHER,
			self::NAS_LAYOUT_CLASS_MOLLE_LEG,
			self::NAS_LAYOUT_CLASS_MOLLE_VEST,
			self::NAS_LAYOUT_CLASS_MOLLE_COMBAT_PACK,
			self::NAS_LAYOUT_CLASS_MOLLE_BACKPACK,
		);
		$list = array();
		foreach ($classes as $class) {
			$list[$class] = self::get_nasLayoutClass_name($class);
		}

		/*
		 * Добавляем класс, если такого не оказалось в списке.
		 */
		if ($nasLayoutClass && !array_key_exists($nasLayoutClass, $list)) {
			$list[$nasLayoutClass] = '!!! ' . $nasLayoutClass;
		}
		return $list;
	}

	public static function get_nasLayoutClass_name($nasLayoutClass) {
		switch ($nasLayoutClass) {
			case self::NAS_LAYOUT_CLASS_WEAPON:
				$name = 'Weapon';
				break;
			case self::NAS_LAYOUT_CLASS_LAUNCHER:
				$name = 'Launcher';
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_LEG:
				$name = 'MOLLE Leg';
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_VEST:
				$name = 'MOLLE Vest';
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_COMBAT_PACK:
				$name = 'MOLLE Combat Pack';
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_BACKPACK:
				$name = 'MOLLE Backpack';
				break;
			default:
				$name = $nasLayoutClass;
				break;
		}

		return $name;
	}

	public static function get_nasLayoutClass_label($nasLayoutClass, $caption = NULL) {
		if (empty($caption)) {
			$caption = self::get_nasLayoutClass_name($nasLayoutClass);
		}

		$label = Force_Label::factory($caption);
		switch ($nasLayoutClass) {
			case self::NAS_LAYOUT_CLASS_WEAPON:
			case self::NAS_LAYOUT_CLASS_LAUNCHER:
				$label->color_yellow();
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_LEG:
				$label->color_green();
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_VEST:
				$label->color_cyan();
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_COMBAT_PACK:
				$label->color_blue();
				break;
			case self::NAS_LAYOUT_CLASS_MOLLE_BACKPACK:
				$label->color_gray();
				break;
		}

		return $label;
	}

	public static function apply_filter_by_id(Force_Filter $filter, Jelly_Builder $builder, $field = 'uiIndex', $param = 'id') {
		$ids = $filter->get_value($param);
		if ($ids) {
			$ids = explode(',', $ids);
			$builder->where($field, 'IN', $ids);
		}
	}

} // End Core_Item
