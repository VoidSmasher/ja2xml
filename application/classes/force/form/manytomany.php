<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_ManyToMany
 * User: legion
 * Date: 01.08.14
 * Time: 8:18
 */
class Force_Form_ManyToMany extends Force_Form_Control {

	protected $_available_options = array();
	protected $_selected_options = array();

	protected $_view = 'manytomany';
	protected $_icon_class = 'fa-random';

	protected $_allow_combine = false;

	public function __construct($name = null, $label = null, array $options = array(), array $selected_options = array()) {
		$this->add_available_options($options);
		$this->add_selected_options($selected_options);
		$this->name($name);
		$this->label($label);
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, array $options = array(), array $selected_options = array()) {
		return new self($name, $label, $options, $selected_options);
	}

	/*
	 * @todo Force_Form_ManyToMany _render_simple()
	 */
	protected function _render_simple() {
		return '';
	}

	public function render() {
		$this->_simple = false;

		$this->_update_label();
		$this->_check_options();
		$this->_check_for_error();

		$selected_values = (!empty($this->_selected_options)) ? array_keys($this->_selected_options) : array();
		foreach ($selected_values as $index => $option_id) {
			$selected_values[$index] = Form::hidden($this->_name . '[]', $option_id);
		}

		$this->attribute('multiple');
		$this->attribute('class', 'form-control');
		$this->attribute('size', 10, false);

		$attributes_available_list = $this->get_attributes();
		$attributes_selected_list = $this->get_attributes();
		$attributes_available_list['id'] = $this->_name . '_available';
		$attributes_selected_list['id'] = $this->_name . '_selected';

		Helper_Assets::js_vars_push_array('multiple', $this->_name);
		Helper_Assets::add_scripts('assets/common/js/form.multiple.js');

		$label_attributes = [
			'class' => 'control-label',
		];
		$div_attributes['class'] = 'col-sm-6';
		$div_group_attributes = [];
		$div_description_attributes = [];
		if ($this->is_form_horizontal()) {
			$label_attributes['class'] .= ' col-sm-' . $this->get_form_horizontal_label_width();
			$div_group_attributes['class'] = 'col-sm-offset-' . $this->get_form_horizontal_label_width();
			$div_description_attributes['class'] = 'col-sm-' . (12 - $this->get_form_horizontal_label_width()) . 'col-sm-offset-' . $this->get_form_horizontal_label_width() . ' col-xs-12';
		} else {
			$div_group_attributes['class'] = 'row';
		}

		return View::factory($this->_get_view())
			->set('group_attributes', $this->render_group_attributes())
			->bind('attributes_available_list', $attributes_available_list)
			->bind('attributes_selected_list', $attributes_selected_list)
			->bind('label_attributes', $label_attributes)
			->bind('div_attributes', $div_attributes)
			->bind('div_group_attributes', $div_group_attributes)
			->bind('div_description_attributes', $div_description_attributes)
			->bind('show_label', $this->_show_label)
			->set('label', $this->get_label())
			->set('name', $this->get_name())
			->bind('available_options', $this->_available_options)
			->bind('selected_options', $this->_selected_options)
			->set('selected_values', implode("\n", $selected_values))
			->bind('description', $this->_description)
			->set('form_horizontal', $this->is_form_horizontal())
			->render();
	}

	/*
	 * OPTIONS AVAILABLE
	 */

	public function add_available_options(array $options) {
		if (empty($options)) {
			return $this;
		}
		foreach ($options as $key => $value) {
			$this->add_available_option($key, $value);
		}
		return $this;
	}

	public function add_available_option($id, $value) {
		$this->_available_options[(string)$id] = (string)$value;
		return $this;
	}

	public function get_available_options() {
		return $this->_available_options;
	}

	/*
	 * OPTIONS SELECTED
	 */

	public function add_selected_options(array $options) {
		if (empty($options)) {
			return $this;
		}
		foreach ($options as $key => $value) {
			$this->add_selected_option($key, $value);
		}
		return $this;
	}

	public function add_selected_option($id, $value) {
		$this->_selected_options[(string)$id] = (string)$value;
		return $this;
	}

	public function get_selected_options() {
		return $this->_selected_options;
	}

	/*
	 * CHECK
	 */

	protected function _check_options() {
		if (!empty($this->_available_options) && !empty($this->_selected_options)) {
			foreach ($this->_selected_options as $id => $value) {
				if (array_key_exists($id, $this->_available_options)) {
					unset($this->_available_options[$id]);
				}
			}
		}
		return true;
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_before_save(Force_Form_Core $form, Jelly_Model $model) {
		if ($this->is_read_only()) {
			return false;
		}

		$name = $this->get_name();
		$value = $form->get_value($name);
		$link_field_meta = $model->meta()->field($name);

		if (($link_field_meta instanceof Jelly_Field_ManyToMany) || ($link_field_meta instanceof Jelly_Field_HasMany)) {
			if (is_array($value) && !empty($value)) {
				$model->set(array($name => $value));
			} else {
				$model->set(array($name => NULL));
			}
		}
		return true;
	}

} // End Force_Form_ManyToMany
