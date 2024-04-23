<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Textarea
 * User: legion
 * Date: 24.07.14
 * Time: 20:48
 */
class Force_Form_Textarea extends Force_Form_Control {

	protected $_view = 'textarea';
	protected $_icon_class = 'fa-file-text-o';

	public function __construct($name = null, $label = null, $value = null) {
		$this->name($name);
		$this->label($label);
		$this->value($value);
		$this->attribute('class', 'form-control');
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	protected function _render_simple() {
		return Form::textarea($this->get_name(), $this->get_value(), $this->get_attributes());
	}

} // End Force_Form_Textarea
