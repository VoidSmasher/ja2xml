<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Show_Value
 * User: legion
 * Date: 17.11.14
 * Time: 18:22
 */
class Force_Form_Show_Value extends Force_Form_Control {

	protected $_read_only = true;
	protected $_show_error = false;

	protected $_view = 'show/value';
	protected $_icon_class = 'fa-minus';

	public function __construct($name = null, $label = null, $value = null) {
		$this->name($name);
		$this->label($label);
		$this->value($value);
		$this->attribute('class', 'control-text');
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	protected function _render_simple() {
		return $this->get_value();
	}

} // End Force_Form_Show_Value
