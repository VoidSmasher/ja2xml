<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Kohana_Exception
 * User: legion
 * Date: 14.11.17
 * Time: 17:28
 */
class Kohana_Exception extends Kohana_Kohana_Exception {

	public static function handler(Exception $e) {
		if (Kohana::$environment != Kohana::DEVELOPMENT) {
			self::$error_view = TEMPLATE_VIEW . 'error/layout';
		}
		parent::handler($e);
	}

} // End Kohana_Exception
