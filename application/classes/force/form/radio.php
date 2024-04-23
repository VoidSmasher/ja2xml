<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Radio
 * User: legion
 * Date: 29.07.14
 * Time: 12:00
 */
class Force_Form_Radio extends Force_Form_Control {

	protected $_options = array();
	protected $_multiple = false;
	protected $_horizontal = false;

	protected $_view = 'radio';
	protected $_icon_class = 'fa-circle';

	public function __construct($name = null, $label = null, array $options = array(), $selected = null) {
		$this->name($name);
		$this->value($selected);
		$this->label($label);
		$this->add_options($options);
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, array $options = array(), $selected = null) {
		return new self($name, $label, $options, $selected);
	}

	protected function _render_simple() {
		$simple = array();
		foreach ($this->_options as $_value => $_label) {
			$checked = ($_value == $this->_value);
			if ($this->_multiple) {
				$radio = Form::checkbox($this->_name, $_value, $checked, $this->get_attributes());
			} else {
				$radio = Form::radio($this->_name, $_value, $checked, $this->get_attributes());
			}
			$simple[] = Form::label(null, $radio . $this->_label);
		}
		$simple = implode(' ', $simple);
		return $simple;
	}

	public function render() {
		$this->_update_label();
		$this->_check_for_error();

		$this->attribute('value', $this->get_value());

		/* ===== SIMPLE RENDER ===== */
		if ($this->_simple) {
			return $this->_render_simple();
		}

		return View::factory($this->_get_view())
			->set('group_attributes', $this->render_group_attributes())
			->set('attributes', $this->get_attributes())
			->bind('show_label', $this->_show_label)
			->set('label', $this->get_label())
			->set('name', $this->get_name())
			->set('value', $this->get_value())
			->bind('description', $this->_description)
			->set('form_horizontal', $this->is_form_horizontal())
			->bind('options', $this->_options)
			->bind('multiple', $this->_multiple)
			->bind('horizontal', $this->_horizontal)
			->render();
	}

	/*
	 * OPTIONS
	 */

	public function add_option($value, $label) {
		$this->_options[$value] = $label;
		return $this;
	}

	public function add_options(array $options) {
		foreach ($options as $value => $label) {
			$this->add_option($value, $label);
		}
		return $this;
	}

	public function get_options() {
		return $this->_options;
	}

	/*
	 * MULTIPLE
	 */

	public function multiple($value = true) {
		$this->_multiple = boolval($value);
		return $this;
	}

	/*
	 * HORIZONTAL
	 */

	public function horizontal($value = true) {
		$this->_horizontal = boolval($value);
		return $this;
	}

} // End Force_Form_Radio
