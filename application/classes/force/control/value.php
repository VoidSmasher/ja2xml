<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Value
 * User: legion
 * Date: 15.01.15
 * Time: 22:23
 */
trait Force_Control_Value {

	protected $_value = null;

	/*
	 * SET
	 */

	public function value($value) {
		$this->_value = $value;
		return $this;
	}

	/*
	 * GET
	 */

	public function get_value() {
		return $this->_value;
	}

} // End Force_Control_Value
