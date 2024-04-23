<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Form
 * User: Andrey Verstov
 * Date: 30.07.12
 * Time: 18:04
 */
class Helper_Form {

	/**
	 * @return bool
	 *
	 * @deprecated use Form::is_post() instead
	 */
	public static function is_post() {
		return (array_key_exists('REQUEST_METHOD', $_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST');
	}

	public static function get_boolean_options($key_yes = 1, $key_no = 0) {
		return array(
			$key_no => __('common.no'),
			$key_yes => __('common.yes'),
		);
	}

} // End Helper_Form
