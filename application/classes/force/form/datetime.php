<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_DateTime
 * User: legion
 * Date: 26.07.18
 * Time: 14:17
 */
class Force_Form_DateTime extends Force_Form_Control {

	use Force_Control_DateTime;

	public function _render_simple() {
		return Form::input($this->get_name(), $this->get_value(), $this->get_attributes());
	}

} // End Force_Form_DateTime
