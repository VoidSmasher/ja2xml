<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Checkbox
 * User: legion
 * Date: 24.07.14
 * Time: 22:38
 */
class Force_Form_Checkbox extends Force_Form_Control {

	protected $_view = 'checkbox';
	protected $_icon_class = 'fa-check';

	public function __construct($name = null, $label = null, $selected = false) {
		$this->name($name);
		$this->value($selected);
		$this->label($label);
	}

	public static function factory($name = null, $label = null, $selected = null) {
		return new self($name, $label, $selected);
	}

	protected function _render_simple() {
		$this->attribute('id', $this->get_name(), false);

		$checkbox = Form::checkbox($this->get_name(), NULL, $this->get_value(), $this->get_attributes());
		if ($this->_show_label) {
			$label = '&nbsp;' . Form::label($this->get_name(), $this->get_label(), array(
					'style' => 'cursor:pointer',
				));
		} else {
			$label = '';
		}
		return $checkbox . $label;
	}

	public function render() {
		$this->remove_group_attribute_class('form-group');
		if ($this->is_form_horizontal()) {
			$this->group_attribute('class', 'checkbox-inline col-sm-10 col-sm-offset-2');
		} else {
			$this->group_attribute('class', 'checkbox');
		}

		$description_attributes = array();
		if ($this->is_form_horizontal()) {
			$description_attributes['class'] = 'col-sm-' . (12 - $this->get_form_horizontal_label_width()) . ' col-sm-offset-' . $this->get_form_horizontal_label_width();
		}

		$this->_view_data['description_attributes'] = $description_attributes;

		return parent::render();
	}

	/*
	 * OVERRIDE VALUE SETTER
	 */

//	public function value($value) {
//		$this->_value = boolval($value);
//		return $this;
//	}

} // End Force_Form_Checkbox
