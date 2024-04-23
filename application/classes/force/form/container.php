<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Container
 * User: legion
 * Date: 10.12.15
 * Time: 19:01
 */
abstract class Force_Form_Container extends Force_Attributes {

	use Force_Control_Label;
	use Force_Control_Name;
	use Force_Control_Horizontal;
	use Force_Form_Controls;

	/*
	 * RENDER
	 */

	abstract public function render($container_body = null);

	public function __toString() {
		return $this->render();
	}

	public function __set($name, $value) {
		$this->_set_controls($name, $value);
	}

	/*
	 * PREDEFINED SETUP
	 */

	public function preset_for_admin() {
		return $this;
	}

} // End Force_Form_Container
