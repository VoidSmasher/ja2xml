<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Hidden
 * User: legion
 * Date: 23.12.17
 * Time: 15:30
 */
class Force_Form_Hidden extends Force_Form_Control {

	protected $_view = 'input';
	protected $_icon_class = 'fa-minus';

	public function __construct($name = null, $value = null) {
		$this->name($name);
		$this->value($value);
	}

	public static function factory($name = null, $value = null) {
		return new self($name, $value);
	}

	protected function _render_simple() {
		return FORM::hidden($this->get_name(), $this->get_value(), $this->get_attributes());
	}

	public function render() {
		return $this->_render_simple();
	}

} // End Force_Form_Hidden
