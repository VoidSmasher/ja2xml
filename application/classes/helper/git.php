<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 10.07.12
 * Time: 11:18
 */
class Helper_Git {

	protected static $_commit_hash = '';

	public static function get_current_commit() {
		if (empty(self::$_commit_hash)) {
			try {
				exec('git rev-parse --verify HEAD 2> /dev/null', $output);
				self::$_commit_hash = $output[0];
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		return self::$_commit_hash;
	}

} // End Helper_Git
