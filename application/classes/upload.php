<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Upload
 * User: legion
 * Date: 08.08.17
 * Time: 20:00
 */
class Upload extends Kohana_Upload {

	public static function get_max_file_size() {
		return Num::bytes(ini_get('upload_max_filesize'));
	}

} // End Upload
