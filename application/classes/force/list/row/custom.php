<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_List_Row_Custom
 * User: legion
 * Date: 27.11.14
 * Time: 19:45
 */
class Force_List_Row_Custom {

	protected $_data = null;
	protected $_params = array();

	public function __construct($data, $params) {
		$this->_data = $data;
		$this->_params = $params;
	}

	public static function factory($data, $params) {
		return new self($data, $params);
	}

	public function get_data() {
		return $this->_data;
	}

	public function get_params() {
		return $this->_params;
	}

} // End Force_List_Row_Custom
