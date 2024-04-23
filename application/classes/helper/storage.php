<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Storage
 * User: legion
 * Date: 10.07.13
 * Time: 13:01
 */
class Helper_Storage {

	protected static $_data = array();

	public static function set($key, $value) {
		self::$_data[$key] = $value;
	}

	public static function bind($key, &$value) {
		self::$_data[$key] = $value;
	}

	public static function get($key, $default = null) {
		if (array_key_exists($key, self::$_data)) {
			return self::$_data[$key];
		} else {
			return $default;
		}
	}

} // End Helper_Storage
