<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Common_Static
 * User: legion
 * Date: 27.05.16
 * Time: 17:42
 */
trait Core_Common_Static {

	public static function is_model($model, $check_for_loaded = false) {
		return Helper_Jelly::is_model($model, self::$model_class, $check_for_loaded);
	}

	public static function is_loaded($model) {
		return self::is_model($model, true);
	}

} // End Core_Common_Static
