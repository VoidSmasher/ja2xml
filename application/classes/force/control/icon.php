<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Icon
 * User: legion
 * Date: 02.08.17
 * Time: 15:39
 */
trait Force_Control_Icon {

	protected $_icon = '';
	protected $_icon_fixed_width = false;

	/*
	 * SET
	 */

	public function icon($icon_class, $fixed_width = false) {
		$this->_icon = (string)$icon_class;
		$this->icon_fixed_width($fixed_width);
		return $this;
	}

	public function icon_fixed_width($value = true) {
		$this->_icon_fixed_width = boolval($value);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_icon() {
		return Helper_Bootstrap::get_icon($this->_icon, $this->_icon_fixed_width);
	}

} // End Force_Control_Icon
