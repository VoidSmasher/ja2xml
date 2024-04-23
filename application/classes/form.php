<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Form
 * User: legion
 * Date: 20.08.15
 * Time: 21:25
 */
class Form extends Kohana_Form {

	public static function is_post() {
		return (array_key_exists('REQUEST_METHOD', $_SERVER) && $_SERVER['REQUEST_METHOD'] == 'POST');
	}

} // End Form
