<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Ftp
 * User: legion
 * Date: 20.07.12
 * Time: 8:57
 */
class Helper_Ftp {

	public static function get_connection($ftp_params) {
		$connection = @ftp_connect($ftp_params['host'], null, isset($ftp_params['timeout']) ? $ftp_params['timeout'] : 90);

		$login = @ftp_login($connection, $ftp_params['login'], $ftp_params['pass']);

		if (isset($ftp_params['pasv']) && $ftp_params['pasv'] == true) {
			@ftp_pasv($connection, true);
		}

		return ($connection && $login) ? $connection : false;
	}

	public static function copy_file($connection, $source, $destination, $close_connection = true) {
		$is_uploaded = false;

		if ($connection) {
			$result = self::create_path($connection, $destination);
			if (!$result) {
				return false;
			}
			$is_uploaded = ftp_put($connection, basename($destination), $source, FTP_BINARY);
			if ($close_connection) {
				self::close_connection($connection);
			}
		}
		return $is_uploaded;
	}

	protected static function check_dir($connection, $dir) {
		$result = false;
		if (!empty($dir)) {
			if (@ftp_chdir($connection, $dir)) {
				$result = true;
			} else {
				$result = @ftp_mkdir($connection, $dir);
				if ($result && @ftp_chdir($connection, $dir)) {
					$result = true;
				} else {
					$result = false;
				}
			}
		}
		return $result;
	}

	public static function remove_file($connection, $filename) {
		try {
			$result = @ftp_delete($connection, $filename);
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}

	public static function create_path($connection, $path) {
		$result = true;
		$path = dirname($path);
		$path = explode('/', $path);
		foreach ($path as $dir) {
			$result = self::check_dir($connection, $dir);
			if (!$result) {
				break;
			}
		}
		return $result;
	}

	public static function close_connection($connection) {
		ftp_close($connection);
	}

} // End Helper_Ftp
