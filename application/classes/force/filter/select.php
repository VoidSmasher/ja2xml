<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Select
 * User: legion
 * Date: 15.07.14
 * Time: 0:56
 */
class Force_Filter_Select extends Force_Filter_Control {

	protected $_values = array();
	protected $_value_rules = array();

	/*
	 * DEFAULT VALUE
	 */
	protected $_add_placeholder = true;
	protected $_placeholder_value = '---';
	protected $_placeholder_label = '---';

	/*
	 * MULTIPLE
	 */
	protected $_multiple = false;
	protected $_multiple_select_all = false;
	protected $_multiple_filter = false;
	protected $_multiple_clickable_groups = false;
	protected $_multiple_max_height = false;
	protected $_multiple_reset_button = false;

	public function __construct($name, $label = '', array $values = array(), $add_placeholder = true) {
		$this->name($name);
		$this->label($label);
		$this->_values = $values;
		$this->_add_placeholder = boolval($add_placeholder);
		$this->attribute('class', 'form-control');
	}

	public static function factory($name, $label = '', array $values = array(), $add_placeholder = true) {
		return new self($name, $label, $values, $add_placeholder);
	}

	public function get_value_label($value) {
		if (is_array($value)) {
			$selected = array();
			foreach ($value as $_value) {
				foreach ($this->_values as $_key => $_option) {
					if (is_array($_option)) { //optgroup
						if (array_key_exists($_value, $_option)) {
							$selected[] = $_option[$_value];
						}
					} else {
						if ($_key == $_value) {
							$selected[] = $_option;
						}
					}
				}
			}
			return implode(', ', $selected);
		}
		if (array_key_exists($value, $this->_values)) {
			return $this->_values[$value];
		}
		return $value;
	}

	public function render() {
		if (empty($this->_label)) {
			$this->_label = $this->_name;
		}

		if ($this->_show_label) {
			$this->attribute('id', $this->get_name(), false);
		}

		if ($this->_multiple) {
			Helper_Assets::add_styles('/assets/common/css/bootstrap-multiselect.css');
			Helper_Assets::add_scripts('/assets/common/js/bootstrap-multiselect.js');
			Helper_Assets::add_scripts('/assets/common/js/form.multiselect.js');
			$this->attribute('multiple');

			$this->_name .= '[]';
			$params = array(
				'name' => $this->_name,
				'params' => array(
					'selectAllText' => ' Выбрать всё',
					'nonSelectedText' => 'Ничего не выбрано',
					'nSelectedText' => 'выбрано',
					'allSelectedText' => 'Всё выбрано',
					'filterPlaceholder' => 'Поиск',
					'numberDisplayed' => 1,
				),
				'reset_button' => $this->_multiple_reset_button,
			);

			if ($this->_multiple_select_all) {
				$params['params']['includeSelectAllOption'] = true;
			}

			if ($this->_multiple_filter) {
				$params['params']['enableFiltering'] = true;
				$params['params']['enableCaseInsensitiveFiltering'] = true;
			}

			if ($this->_multiple_clickable_groups) {
				$params['params']['enableClickableOptGroups'] = true;
			}

			if ($this->_multiple_max_height) {
				$params['params']['maxHeight'] = $this->_multiple_max_height;
			}

			Helper_Assets::js_vars_push_array('form_multiselect', $params);
		} else {
			if ($this->_add_placeholder) {
				$this->_values = array($this->_placeholder_value => $this->_placeholder_label) + $this->_values;
			}
		}

		return View::factory(FORCE_VIEW . 'filter/controls/select')
			->set('attributes', $this->get_attributes())
			->bind('use_filter_block', $this->_use_filter_block)
			->bind('label', $this->_label)
			->bind('show_label', $this->_show_label)
			->bind('name', $this->_name)
			->bind('values', $this->_values)
			->bind('value', $this->_value)
			->bind('multiple_reset_button', $this->_multiple_reset_button)
			->bind('start_from_new_line', $this->_start_from_new_line)
			->render();
	}

	public function set_placeholder($value, $label = null) {
		$this->_add_placeholder = true;
		$this->_placeholder_value = $value;
		if (!is_null($label)) {
			$this->_placeholder_label = $label;
		}
		return $this;
	}

	/*
	 * Select as Multiple
	 */

	public function multiple($value = true) {
		$this->_multiple = boolval($value);
		return $this;
	}

	public function multiple_select_all($value = true) {
		$this->_multiple_select_all = boolval($value);
		return $this;
	}

	public function multiple_filter($value = true) {
		$this->_multiple_filter = boolval($value);
		return $this;
	}

	public function multiple_clickable_groups($value = true) {
		$this->_multiple_clickable_groups = boolval($value);
		return $this;
	}

	public function multiple_max_height($integer_value) {
		$this->_multiple_max_height = abs((int)$integer_value);
		return $this;
	}

	public function multiple_reset_button($value = true) {
		$this->_multiple_reset_button = boolval($value);
		return $this;
	}

	/*
	 * VALUE RULES
	 * Для понимания как работают value_rules ознакомьтесь с документацией.
	 * @url http://newproject.local/developer/documentation_filter
	 */
	public function value_rule($key_or_array, $value = null) {
		if (is_array($key_or_array)) {
			$this->_value_rules = array_merge($this->_value_rules, $key_or_array);
		} else {
			$this->_value_rules[$key_or_array] = $value;
		}
		return $this;
	}

	public function set_value_rules(array $value_rules = array()) {
		$this->_value_rules = array_merge($this->_value_rules, $value_rules);
		return $this;
	}

	public function get_value_rules() {
		if ($this->_add_placeholder) {
			$this->set_value_rules(array(
				'!=' => $this->_placeholder_value,
			));
		}
		return $this->_value_rules;
	}

} // End Force_Filter_Select
