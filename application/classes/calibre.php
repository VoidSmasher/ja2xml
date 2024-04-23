<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Calibre
 * User: legion
 * Date: 09.05.2021
 * Time: 1:04
 */
class Calibre {

	public static $info = array();

	/**
	 * @param Model_Weapon_Group $weapon
	 * @return DTO_Calibre_Info
	 */
	public static function get_info_by_weapon(Model_Weapon_Group $weapon) {
		if (array_key_exists($weapon->ubCalibre, self::$info)) {
			return self::$info[$weapon->ubCalibre];
		}

		return self::$info[$weapon->ubCalibre] = new DTO_Calibre_Info($weapon->cartridge_weight);
	}

} // End Calibre
