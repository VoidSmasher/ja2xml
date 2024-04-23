<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Cache
 * User: legion
 * Date: 10.12.12
 * Time: 13:25
 */
class Helper_Cache {

	public static function get_lifetime($key, $default = 60) {
		$expire_time = (int)Kohana::$config->load('cache.cache_expire_times.' . $key);
		if (is_null($expire_time)) {
			$expire_time = (int)$default;
		}
		return $expire_time;
	}

} // End Helper_Cache
