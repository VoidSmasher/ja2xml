<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 10.07.12
 * Time: 11:18
 */
class Helper_Daemon {

	const LOCKER_DIRECTORY = 'daemons/';

	public static function lock($daemon, $pid) {
		return (bool)file_put_contents(APPPATH . self::LOCKER_DIRECTORY . $daemon, $pid);
	}

	public static function unlock($daemon) {
		return unlink(APPPATH . self::LOCKER_DIRECTORY . $daemon);
	}

	public static function check($daemon) {
		return (file_exists(APPPATH . self::LOCKER_DIRECTORY . $daemon) && (bool)posix_getsid(self::get_pid($daemon)));
	}

	public static function get_pid($daemon) {
		return file_get_contents(APPPATH . self::LOCKER_DIRECTORY . $daemon);
	}

} // End Helper_Daemon