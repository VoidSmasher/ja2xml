<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_List_Row_Cell
 * User: legion
 * Date: 03.12.14
 * Time: 21:43
 */
class Force_List_Row_Cell extends Force_Attributes {

	protected $_name = null;
	protected $_overwrite = null;

	public function __construct($name) {
		$this->_name = (string)$name;
	}

	public static function factory($name) {
		return new self($name);
	}

	public function overwrite_column_attributes($overwrite = true) {
		$this->_overwrite = $overwrite;
		return $this;
	}

	public function get_overwrite() {
		return $this->_overwrite;
	}

} // End Force_List_Row_Cell
