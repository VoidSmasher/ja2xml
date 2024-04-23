<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Session
 * User: Ener
 * Date: 28.04.12
 * Time: 3:38
 */
class Helper_Session {

	public static function init() {
		if (is_null(Session::instance()->get('created_at'))) {
			Session::instance()->set('created_at', time());
		}

//		die(date('d.m.Y H:i:s', Session::instance()->get('created_at')));

		return true;
	}

	public static function duration() {
		$result = false;
		if (Session::instance()->get('created_at', 0) > 0) {
			$result = time() - Session::instance()->get('created_at', 0);
		}

		return $result;
	}

} // End Helper_Session
