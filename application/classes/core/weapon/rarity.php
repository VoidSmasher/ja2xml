<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Weapon_Rarity
 * User: legion
 * Date: 19.07.2020
 * Time: 8:03
 */
trait Core_Weapon_Rarity {

	protected static $rarity_list = array(
		Core_Weapon_Data::RARITY_VERY_COMMON => 'Very Common',
		Core_Weapon_Data::RARITY_COMMON => 'Common',
		Core_Weapon_Data::RARITY_RARE => 'Rare',
		Core_Weapon_Data::RARITY_VERY_RARE => 'Very Rare',
		Core_Weapon_Data::RARITY_EXCLUSIVE => 'Exclusive',
	);

	public static function get_rarity_list() {
		return self::$rarity_list;
	}

	public static function get_rarity_label($rarity) {
		$caption = Arr::get(self::$rarity_list, $rarity, 'Unknown');

		$label = Force_Label::factory($caption);

		switch ($rarity) {
			case Core_Weapon_Data::RARITY_VERY_COMMON:
				$label->color_cyan();
				break;
			case Core_Weapon_Data::RARITY_COMMON:
				$label->color_blue();
				break;
			case Core_Weapon_Data::RARITY_RARE:
				$label->color_green();
				break;
			case Core_Weapon_Data::RARITY_VERY_RARE:
				$label->color_yellow();
				break;
			case Core_Weapon_Data::RARITY_EXCLUSIVE:
				$label->color_red();
				break;
		}

		return $label;
	}

} // End Core_Weapon_Rarity
