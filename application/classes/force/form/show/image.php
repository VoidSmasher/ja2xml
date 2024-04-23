<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Show_Image
 * User: legion
 * Date: 14.11.14
 * Time: 21:42
 */
class Force_Form_Show_Image extends Force_Form_Control {

	protected $_read_only = true;
	protected $_show_error = false;

	protected $_view = 'show/image';
	protected $_icon_class = 'fa-image';

	public function __construct($name = null, $label = null, $value = null) {
		$this->name($name);
		$this->label($label);
		$this->value($value);
		$this->attribute('class', 'control-image');
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	protected function _render_simple() {
		return Html::image($this->get_value(), $this->get_attributes());
	}

} // End Force_Form_Show_Image
