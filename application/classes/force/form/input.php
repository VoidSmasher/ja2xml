<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Input
 * User: legion
 * Date: 24.07.14
 * Time: 11:32
 */
class Force_Form_Input extends Force_Form_Control {

	protected $_view = 'input';
	protected $_icon_class = 'fa-minus';

	protected $_before_input = NULL;
	protected $_after_input = NULL;

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
		return FORM::input($this->get_name(), $this->get_value(), $this->get_attributes());
	}

	public function render() {
		$this->_view_data['before_input'] = $this->_before_input;
		$this->_view_data['after_input'] = $this->_after_input;

		return parent::render();
	}

	/*
	 * SET
	 */

	public function password() {
		$this->attribute('type', 'password', true);
		return $this;
	}

	public function number() {
		$this->attribute('type', 'number', true);
		return $this;
	}

	public function no_cache() {
		$this->attribute('autocomplete', 'off', true);
		return $this;
	}

	/*
	 * BEFORE/AFTER
	 */

	public function before_input($value) {
		$this->_before_input = self::render_input_group_addon($value);
		return $this;
	}

	public function after_input($value) {
		$this->_after_input = self::render_input_group_addon($value);
		return $this;
	}

	final private static function render_input_group_addon($value) {
		if ($value instanceof Force_Button) {
			return '<span class="input-group-btn">' . $value->render() . '</span>';
		}
		if (is_object($value)) {
			if (method_exists($value, 'render')) {
				$value = $value->render();
			}
		}
		if (is_string($value)) {
			return '<span class="input-group-addon">' . $value . '</span>';
		}
		return '';
	}

} // End Force_Form_Input
