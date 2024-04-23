<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Attachment_Type
 * User: legion
 * Date: 04.01.2020
 * Time: 1:47
 */
abstract class Attachment_Type extends Attachment_Mount {

	const TYPE_MUZZLE = 'Muzzle';
	const TYPE_BARREL_EXTENDER = 'Barrel_Extender';
	const TYPE_SUPPRESSOR = 'Suppressor';
	const TYPE_SUPPRESSOR_SOUND = 'Suppressor_Sound';
	const TYPE_SUPPRESSOR_FLASH = 'Suppressor_Flash';
	const TYPE_CHOKE = 'Choke';
	const TYPE_TARGETING = 'Targeting';
	const TYPE_LASER = 'Laser';
	const TYPE_SCOPE = 'Scope';
	const TYPE_SIGHT = 'Sight';
	const TYPE_IR = 'IR';
	const TYPE_STOCK = 'Stock';
	const TYPE_KNIFE = 'Knife';
	const TYPE_HANDLING = 'Handling';
	const TYPE_BIPOD = 'Bipod';
	const TYPE_FOREGRIP = 'Foregrip';

	const TYPE_SECONDARY_WEAPON = 'Secondary_Weapon';
	const TYPE_ABOVE_BARREL_WEAPON = 'Above_Barrel_Weapon';
	const TYPE_UNDER_BARREL_WEAPON = 'Under_Barrel_Weapon';
	const TYPE_MULTI_CHARGE_GL = 'Multi_Charge_GL';

	const TYPE_REPLACEABLE_BARREL = 'Replaceable_Barrel';
	const TYPE_INTERNAL = 'Internal';
	const TYPE_INTEGRAL = 'Integral';
	const TYPE_TRIGGER_BURST = 'Trigger_Burst';
	const TYPE_ACCESSORY = 'Accessory';
	const TYPE_RIFLE_SLING = 'Rifle_Sling';
	const TYPE_FLASHLIGHT = 'Flashlight';
	const TYPE_FLASHLIGHT_PROJECTOR = 'Flashlight_Projector';
	const TYPE_UPGRADE_KIT = 'Upgrade_Kit';

	const TYPE_LONG = 'Long';
	const TYPE_BATTERIES = 'Batteries';
	const TYPE_HAS_RAIL = 'Has_Rail';
	const TYPE_OLD = 'Old';

	const TYPE_SEMI_AUTO = 'Semi_Auto';
	const TYPE_AUTOMATIC = 'Automatic';
	const TYPE_MAG_ADAPTER = 'Mag_Adapter';
	const TYPE_762x39 = '762x39';
	const TYPE_556x45 = '556x45';
	const TYPE_9x19 = '9x19';

	const TYPE_TWO_HANDED = 'Two_Handed';

	const TYPE_AMMO_PISTOL = 'Ammo_Pistol';
	const TYPE_AMMO_SHOTGUN = 'Ammo_Shotgun';
	const TYPE_AMMO_ROCKET = 'Ammo_Rocket';

	const TYPE_PISTOL = 'Pistol';
	const TYPE_MP = 'MP';
	const TYPE_SHOTGUN = 'Shotgun';
	const TYPE_SMG = 'SMG';
	const TYPE_RIFLE = 'Rifle';
	const TYPE_MACHINEGUN = 'Machinegun';
	const TYPE_SNIPER = 'Sniper';

	private $types_list;
	protected static $types_menu;
	/*
	 * COLORS:
	 * - red
	 * - yellow
	 * - green
	 * - cyan
	 * - blue
	 */
	protected static $types = [
		self::TYPE_MUZZLE => [
			'group' => true,
			'color' => 'yellow',
		],
		self::TYPE_TARGETING => [
			'group' => true,
			'color' => 'cyan',
		],
		self::TYPE_HANDLING => [
			'group' => true,
			'color' => 'yellow',
		],
		self::TYPE_SECONDARY_WEAPON => [
			'group' => true,
			'color' => 'blue',
		],
		self::TYPE_MAG_ADAPTER => [
			'group' => true,
			'color' => 'green',
		],
		self::TYPE_INTERNAL => [
			'group' => true,
		],
		self::TYPE_INTEGRAL => [
			'group' => true,
		],
		self::TYPE_ACCESSORY => [
			'group' => true,
			'color' => 'green',
		],

		self::TYPE_BIPOD => [
			'filter' => true,
			'color' => 'blue',
		],
		self::TYPE_CHOKE => [
			'filter' => true,
		],
		self::TYPE_FOREGRIP => [
			'filter' => true,
			'color' => 'blue',
		],
		self::TYPE_KNIFE => [
			'filter' => true,
			'color' => 'green',
		],
		self::TYPE_LASER => [
			'filter' => true,
			'color' => 'red',
		],
		self::TYPE_SCOPE => [
			'filter' => true,
			'color' => 'cyan',
		],
		self::TYPE_SIGHT => [
			'filter' => true,
			'color' => 'cyan',
		],
		self::TYPE_IR => [
			'color' => 'red',
		],
		self::TYPE_STOCK => [
			'filter' => true,
			'color' => 'blue',
		],
		self::TYPE_SUPPRESSOR => [
			'filter' => true,
			'color' => 'green',
		],
		self::TYPE_SUPPRESSOR_SOUND => [
			'color' => 'blue',
		],
		self::TYPE_SUPPRESSOR_FLASH => [
			'color' => 'yellow',
		],

		self::TYPE_BARREL_EXTENDER => [
			'color' => 'yellow',
		],
		self::TYPE_REPLACEABLE_BARREL => [
			'color' => 'yellow',
		],

		self::TYPE_ABOVE_BARREL_WEAPON => [
			'color' => 'blue',
		],
		self::TYPE_UNDER_BARREL_WEAPON => [
			'color' => 'blue',
		],
		self::TYPE_MULTI_CHARGE_GL => [
			'color' => 'yellow',
		],

		self::TYPE_RIFLE_SLING => [
			'color' => 'blue',
		],

		self::TYPE_UPGRADE_KIT => [
			'filter' => true,
			'color' => 'blue',
		],

		self::TYPE_SEMI_AUTO => [
			'color' => 'yellow',
		],
		self::TYPE_AUTOMATIC => [
			'color' => 'yellow',
		],
		self::TYPE_TRIGGER_BURST => [
			'color' => 'yellow',
		],

		self::TYPE_TWO_HANDED => [
			'color' => 'yellow',
		],

		self::TYPE_PISTOL => [
			'color' => 'yellow',
		],
		self::TYPE_MP => [
			'color' => 'yellow',
		],
		self::TYPE_SHOTGUN => [
			'color' => 'green',
		],
		self::TYPE_SMG => [
			'color' => 'blue',
		],
		self::TYPE_RIFLE => [
			'color' => 'blue',
		],
		self::TYPE_MACHINEGUN => [
			'color' => 'blue',
		],
		self::TYPE_SNIPER => [
			'color' => 'cyan',
		],

		self::TYPE_LONG => [
			'color' => 'blue',
		],
		self::TYPE_FLASHLIGHT => [
			'filter' => true,
			'color' => 'yellow',
		],
		self::TYPE_FLASHLIGHT_PROJECTOR => [
			'color' => 'yellow',
		],
		self::TYPE_BATTERIES => [
			'color' => 'gray',
		],
		self::TYPE_HAS_RAIL => [
			'color' => 'green',
		],

		self::TYPE_AMMO_PISTOL => [
			'color' => 'yellow',
		],
		self::TYPE_AMMO_SHOTGUN => [
			'color' => 'green',
		],
		self::TYPE_AMMO_ROCKET => [
			'color' => 'red',
		],

		self::TYPE_762x39 => [
			'color' => 'green',
		],
		self::TYPE_556x45 => [
			'color' => 'green',
		],
		self::TYPE_9x19 => [
			'color' => 'green',
		],

		self::TYPE_OLD => [
			'color' => 'yellow',
		],
	];

	/**
	 * @param Jelly_Model $model
	 * @return array
	 */
	public static function get_types(Jelly_Model $model) {
		return Helper::get_json_as_array($model, 'attachment_types');
	}

	/**
	 * @return array
	 */
	public static function get_types_menu() {

		if (is_null(self::$types_menu)) {
			$groups = array();
			$filters = array();

			foreach (self::$types as $type => $params) {
				$group = Arr::get($params, 'group');
				if ($group) {
					$groups[$type] = '[ ' . $type . ' ]';
				} else {
					$filter = Arr::get($params, 'filter');
					if ($filter) {
						$filters[$type] = $type;
					}
				}
			}

			self::$types_menu = $groups + $filters;
		}

		return self::$types_menu;
	}

	public static function get_type_labels(Jelly_Model $model) {
		$attachment_types = Attachment::get_types($model);

		if (empty($attachment_types)) {
			return '';
		}

		$integrated_types = array();

		foreach ($attachment_types as $attachment_type) {

			$name = Helper::get_bonus_caption($attachment_type);

			if (array_key_exists($attachment_type, self::$types)) {
				$label = Force_Label::factory($name);
				$params = self::$types[$attachment_type];
				Helper::set_label_color($label, $params);

				$integrated_types[] = $label->render();
			} else {
				$integrated_types[] = Force_Label::factory($name)
					->render();
			}
		}
		return implode(' ', $integrated_types);
	}

	public static function get_types_label(Jelly_Model $model) {
		$attachment_types = Attachment::get_types($model);

		$label = Force_Label::factory('');
		$label->color_gray();

		$caption = array();

		foreach ($attachment_types as $attachment_type) {
			$caption[] = Helper::get_bonus_caption($attachment_type);
			$params = Arr::get(self::$types, $attachment_type, []);
			$color = Arr::get($params, 'color');
			switch ($color) {
				case 'red':
					$label->color_red();
					break;
				case 'yellow':
					$label->color_yellow();
					break;
				case 'green':
					$label->color_green();
					break;
				case 'cyan':
					$label->color_cyan();
					break;
				case 'blue':
					$label->color_blue();
					break;
			}
		}

		if (empty($caption)) {
			$caption = 'Unknown';
		} else {
			$caption = implode(', ', $caption);
		}

		$label->label($caption);

		return $label;
	}

	public static function has_type(Jelly_Model $model, $attachment_type, $must_have_all = false) {
		$attachment_types = Attachment::get_types($model);
		if (is_array($attachment_type)) {
			if ($must_have_all) {
				$count = count($attachment_type);
				$match = 0;
				foreach ($attachment_type as $type) {
					if (array_key_exists($type, $attachment_types)) {
						$match++;
					}
				}
				return ($count == $match);
			} else {
				foreach ($attachment_type as $type) {
					if (array_key_exists($type, $attachment_types)) {
						return true;
					}
				}
			}
		} else {
			return array_key_exists($attachment_type, $attachment_types);
		}
		return false;
	}

	/*
	 * INSTANCE INIT
	 */

	protected function init_type() {
		$this->types_list = array();
		foreach (self::$types as $type => $params) {
			$this->types_list[$type] = $type;
		}
	}

	/*
	 * INSTANCE ONLY
	 */

	/**
	 * @return array
	 */
	public function get_type_list() {
		return $this->types_list;
	}

} // End Attachment_Type
