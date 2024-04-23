<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Uploader
 * User: ener
 * Date: 21.07.14
 * Time: 18:02
 */
trait Controller_Common_Uploader {

	public function action_summernoteuploader() {
		if (array_key_exists('file', $_FILES)) {
			$filename = Helper_Image::upload($_FILES['file'], $_FILES['file']['name'], 'summernote');
			if ($filename) {
				echo Helper_Image::get_filename($filename, 'summernote');
			}
		}
		exit(0);
	}

} // End Controller_Uploader