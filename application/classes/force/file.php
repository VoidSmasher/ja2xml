<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_File
 * User: legion
 * Date: 09.08.17
 * Time: 0:28
 */
class Force_File {

	use Force_Control_Name;
	use Force_Control_Label;
	use Force_Control_Required;
	use Force_File_Core;

	protected static $_instances = array();

	public function __construct($file_type) {
		$this->file_type($file_type);
	}

	/**
	 * @param $file_type
	 *
	 * @return Force_File
	 */
	public static function instance($file_type) {
		if (!array_key_exists($file_type, self::$_instances)) {
			self::$_instances[$file_type] = new self($file_type);
		}
		return self::$_instances[$file_type];
	}

} // End Force_File
