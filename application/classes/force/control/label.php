<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Label
 * User: legion
 * Date: 15.01.15
 * Time: 22:21
 */
trait Force_Control_Label {

	protected $_label = null;
	protected $_show_label = true;

	/*
	 * SET
	 */

	public function label($value) {
		$this->_label = (string)$value;
		return $this;
	}

	public function show_label($value = true) {
		$this->_show_label = boolval($value);
		return $this;
	}

	public function hide_label() {
		$this->_show_label = false;
		return $this;
	}

	public function is_show_label() {
		return $this->_show_label;
	}

	/*
	 * GET
	 */

	public function get_label() {
		return $this->_label;
	}

} // End Force_Control_Label
