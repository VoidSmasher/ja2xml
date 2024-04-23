<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Simple
 * User: legion
 * Date: 15.01.15
 * Time: 22:31
 */
trait Force_Control_Simple {

	protected $_simple = false;

	/*
	 * SET
	 */

	public function simple($value = true) {
		$this->_simple = boolval($value);
		return $this;
	}

	/*
	 * GET
	 */

	public function is_simple() {
		return $this->_simple;
	}

} // End Force_Control_Simple
