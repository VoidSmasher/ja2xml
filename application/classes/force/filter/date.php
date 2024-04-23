<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Date
 * User: legion
 * Date: 15.07.14
 * Time: 0:56
 */
class Force_Filter_Date extends Force_Filter_Control {

	const DEFAULT_ICON = 'fa-calendar';

	protected $_show_time = false;
	protected $_icon = null;

	public function __construct($name, $label = '') {
		$this->name($name);
		$this->label($label);
		$this->group_attribute('class', 'input-group date');
		$this->attribute('class', 'form-control');
	}

	public static function factory($name, $label = '') {
		return new self($name, $label);
	}

	public function render() {
		if (empty($this->_label)) {
			$this->_label = $this->_name;
		}

		if (!empty($this->_value)) {
			if (!is_numeric($this->_value)) {
				$this->_value = strtotime($this->_value);
			}
		} else {
			$this->_value = null;
		}

		Helper_Assets::add_styles('assets/common/css/bootstrap-datetimepicker.min.css');
		Helper_Assets::add_scripts(array(
			'assets/common/js/moment.min.js',
			'assets/common/js/bootstrap-datetimepicker.min.js',
			'assets/common/js/form.datetimepicker.js',
		));

		$this->attribute('data-date-format', 'DD.MM.YYYY');

		if ($this->_show_label) {
			$this->attribute('id', $this->get_name(), false);
		}

		$params = array(
			'name' => $this->_name,
			'params' => array(
				'locale' => 'ru',
			),
		);

		if ($this->_show_time) {
			if (!empty($this->_value)) {
				$this->_value = date('d.m.Y H:i', $this->_value);
			}
			$this->attribute('data-date-format', 'DD.MM.YYYY HH:mm', true);
			$this->attribute('data-date-pickTime', 1);
			$this->attribute('data-date-sideBySide', 1);
		} else {
			if (!empty($this->_value)) {
				$this->_value = date('d.m.Y', $this->_value);
			}
			$this->attribute('data-date-pickTime', 0);
		}

		$params['params']['format'] = $this->get_attribute('data-date-format', 'DD.MM.YYYY');
		if (!empty($this->_value)) {
			$params['params']['defaultDate'] = Force_Date::factory($this->_value)->format_sql();
		}

		Helper_Assets::js_vars_push_array('form_datetime', $params);

		if (empty($this->_icon)) {
			$this->icon(self::DEFAULT_ICON);
		}

		return View::factory(FORCE_VIEW . 'filter/controls/input_date')
			->set('group_attributes', $this->get_group_attributes())
			->set('attributes', $this->get_attributes())
			->bind('use_filter_block', $this->_use_filter_block)
			->bind('label', $this->_label)
			->bind('show_label', $this->_show_label)
			->bind('icon', $this->_icon)
			->bind('name', $this->_name)
			->bind('value', $this->_value)
			->bind('start_from_new_line', $this->_start_from_new_line)
			->render();
	}

	/*
	 * SET
	 */

	public function icon($icon_class, $fw = true) {
		$this->_icon = Helper_Bootstrap::get_icon($icon_class, $fw);
		return $this;
	}

	/*
	 * TIME
	 */

	public function show_time($value = true) {
		$this->_show_time = boolval($value);
		return $this;
	}

	public function hide_time() {
		$this->_show_time = false;
		return $this;
	}

} // End Force_Filter_Date
