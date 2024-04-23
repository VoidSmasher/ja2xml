<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Weapon
 * User: legion
 * Date: 29.03.2020
 * Time: 12:54
 */
class Weapon extends Weapon_Quality {

	private static $instance;

	public function __construct() {
		$this->init_quality();
	}

	/**
	 * @return Weapon
	 */
	public static function instance() {
		if (!(self::$instance instanceof Weapon)) {
			self::$instance = new Weapon();
		}
		return self::$instance;
	}

} // End Weapon
