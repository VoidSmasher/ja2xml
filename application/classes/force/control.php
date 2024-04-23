<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control
 * User: legion
 * Date: 15.08.14
 * Time: 14:42
 */
abstract class Force_Control extends Force_Attributes {

	use Force_Control_Name;
	use Force_Control_Label;
	use Force_Control_Value;
	use Force_Control_Simple;

	/*
	 * RENDER
	 */

	abstract public function render();

	public function __toString() {
		return $this->render();
	}

} // End Force_Control
