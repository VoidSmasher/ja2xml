<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Name
 * User: legion
 * Date: 15.01.15
 * Time: 22:20
 */
trait Force_Control_Name {

	protected $_name = null;

	/*
	 * SET
	 */

	public function name($value) {
		$this->_name = (string)$value;
		return $this;
	}

	/*
	 * GET
	 */

	public function get_name() {
		return $this->_name;
	}

} // End Force_Control_Name
