<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Date_Range
 * User: legion
 * Date: 03.09.15
 * Time: 20:15
 */
class Force_Filter_Date_Range extends Force_Filter_Control {

	const DEFAULT_ICON = 'fa-calendar';
	const DATE_RANGE_DIVIDER = ' - ';

	protected $_show_time = false;
	protected $_icon = null;
	protected $_value_begin = null;
	protected $_value_end = null;
	protected $_default_begin = null;
	protected $_default_end = null;

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

		if (empty($this->_value)) {
			if (!empty($this->_default_begin) && !empty($this->_default_end)) {
				$this->_value = $this->_default_begin . self::DATE_RANGE_DIVIDER . $this->_default_end;
			} else {
				$this->_value = null;
			}
		}

		Helper_Assets::add_styles('assets/common/css/bootstrap-datetimepicker.min.css');
		Helper_Assets::add_scripts(array(
			'assets/common/js/moment.min.js',
			'assets/common/js/daterangepicker.js',
			'assets/common/js/form.daterangepicker.js',
		));
		Helper_Assets::add_styles('assets/common/css/daterangepicker.css');
		Helper_Assets::add_scripts('assets/common/js/moment.min.js');
		Helper_Assets::add_scripts('assets/common/js/daterangepicker.js');

		$this->attribute('data-date-format', 'DD.MM.YYYY');

		if ($this->_show_label) {
			$this->attribute('id', $this->get_name(), false);
		}

		$params = array(
			'name' => $this->_name,
			'params' => array(
				'locale' => 'ru',
//				'ranges' => array(
//					'Сегодня' => array(
//						"moment('00:00', 'HH:mm')",
//						"moment('23:59', 'HH:mm')",
//					),
//					'Вчера' => array(
//						"moment('00:00', 'HH:mm').subtract(1, 'days')",
//						"moment('23:59', 'HH:mm').subtract(1, 'days')",
//					),
//					'Последние 7 дней' => array(
//						"moment('00:00', 'HH:mm').subtract(6, 'days')",
//						"moment('23:59', 'HH:mm')",
//					),
//					'Последние 30 дней' => array(
//						"moment('00:00', 'HH:mm').subtract(29, 'days')",
//						"moment('23:59', 'HH:mm')",
//					),
//					'Этот месяц' => array(
//						"moment('00:00', 'HH:mm').startOf('month')",
//						"moment('23:59', 'HH:mm').endOf('month')",
//					),
//					'Прошлый месяц' => array(
//						"moment('00:00', 'HH:mm').subtract(1, 'month').startOf('month')",
//						"moment('23:59', 'HH:mm').subtract(1, 'month').endOf('month')",
//					),
//				),
				'timePicker12Hour' => false,
			),
		);

		if ($this->_show_time) {
			if (!empty($this->_value)) {
//				$this->_value = date('d.m.Y H:i', $this->_value);
			}
			$params['params']['timePicker'] = true;
			$params['params']['timePickerIncrement'] = 1;
			$params['params']['format'] = 'DD.MM.YYYY HH:mm';
		} else {
			if (!empty($this->_value)) {
//				$this->_value = date('d.m.Y', $this->_value);
			}
			$params['params']['format'] = 'DD.MM.YYYY HH:mm';
		}

//		$params['params']['defaultDate'] = $this->_value;
//		$params['params']['format'] = $this->get_attribute('data-date-format', 'DD.MM.YYYY');

		Helper_Assets::js_vars_push_array('form_date_range', $params);

		if (empty($this->_icon)) {
			$this->icon(self::DEFAULT_ICON);
		}

		return View::factory(FORCE_VIEW . 'filter/controls/input_date_range')
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

	/*
	 * DEFAULT VALUE
	 */

	protected function _set_default() {
		if (!empty($this->_default_begin) && !empty($this->_default_end)) {
			$this->_default = $this->_default_begin . Force_Filter_Date_Range::DATE_RANGE_DIVIDER . $this->_default_end;
		}
	}

	public function default_begin($value, $overwrite_empty = true) {
		$this->_default_begin = $value;
		$this->_default_overwrite_empty = $overwrite_empty;
		return $this;
	}

	public function default_end($value, $overwrite_empty = true) {
		$this->_default_end = $value;
		$this->_default_overwrite_empty = $overwrite_empty;
		return $this;
	}

	public function get_default() {
		$this->_set_default();
		return $this->_default;
	}

	public function get_value() {
		$this->_set_default();
		return parent::get_value();
	}

	public function get_date_from($convert_to_mysql_format = false) {
		return self::parse_value_and_get_date_from($this->get_value(), $convert_to_mysql_format, $this->_show_time);
	}

	public function get_date_to($convert_to_mysql_format = false) {
		return self::parse_value_and_get_date_to($this->get_value(), $convert_to_mysql_format, $this->_show_time);
	}

	/*
	 * HELPERS
	 */

	protected static function _get_date($date_range, $date_range_key, $convert_to_mysql_format = false, $show_time = false) {
		if (is_string($date_range) && !empty($date_range)) {
			$date_range = explode(self::DATE_RANGE_DIVIDER, $date_range);
		} else {
			$date_range = array();
		}
		$date = Arr::get($date_range, $date_range_key);

		if (!empty($date) && $convert_to_mysql_format) {
			$date = Force_Date::factory($date)->show_time($show_time)->format_sql();
		}

		return $date;
	}

	public static function parse_value_and_get_date_from($value, $convert_to_mysql_format = false, $show_time = false) {
		return self::_get_date($value, 0, $convert_to_mysql_format, $show_time);
	}

	public static function parse_value_and_get_date_to($value, $convert_to_mysql_format = false, $show_time = false) {
		return self::_get_date($value, 1, $convert_to_mysql_format, $show_time);
	}

} // End Force_Filter_Date_Range
