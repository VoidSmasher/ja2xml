<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Date_Range
 * User: legion
 * Date: 03.09.15
 * Time: 20:15
 * @deprecated
 */
class Force_Form_Date_Range extends Force_Form_Input {

	//@todo ВЫЕБАТЬ И ВЫСУШИТЬ ЭТУ ПОЕБЕНЬ!!!

	protected $_show_time = true;
	protected $_icon = null;
	protected $_value_begin = null;
	protected $_value_end = null;
	protected $_default_begin = null;
	protected $_default_end = null;
	protected $_default = null;
	protected $_default_overwrite_empty = false;
	protected $_date_format = 'DD.MM.YYYY';
	protected $_date_format_php = 'd.m.Y';
	protected $_date_time_format = 'DD.MM.YYYY HH:mm';
	protected $_date_time_format_php = 'd.m.Y H:i';
	protected $_time_format = 'HH:mm';
	protected $_time_format_php = 'H:i';

	protected $_view = 'input_date_range';
	protected $_icon_class = 'fa-calendar';

	public function __construct($name = null, $label = null, $value = null) {
		parent::__construct($name, $label, $value);
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	public function render() {
		$this->_update_label();

		if (empty($this->_value)) {
			$this->_value = null;
		}

		if ($this->_show_error && Helper_Error::has_error($this->_name)) {
			if ($this->_simple) {
				$this->attribute('class', 'error has-error');
			} else {
				$this->group_attribute('class', 'error has-error');
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

		$this->attribute('data-date-format', $this->_date_format);

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
//			if (!empty($this->_value)) {
//				$this->_value = date('d.m.Y H:i', $this->_value);
//			}
			$params['params']['timePicker'] = true;
			$params['params']['timePickerIncrement'] = 15;
			$params['params']['format'] = $this->_date_time_format;
		} else {
//			if (!empty($this->_value)) {
//				$this->_value = date('d.m.Y', $this->_value);
//			}
			$params['params']['format'] = $this->_date_format;
		}

//		$params['params']['defaultDate'] = $this->_value;
//		$params['params']['format'] = $this->get_attribute('data-date-format', 'DD.MM.YYYY');

		Helper_Assets::js_vars_push_array('form_date_range', $params);

		/* ===== SIMPLE RENDER ===== */
		if ($this->_simple) {
			return $this->_render_simple();
		}

		if (!empty($this->_custom_view)) {
			$view = $this->_custom_view;
		} else {
			$view = self::CONTROLS_VIEW . $this->_view;
		}

		return View::factory($view)
			->set('group_attributes', $this->render_group_attributes())
			->set('attributes', $this->get_attributes())
			->bind('show_label', $this->_show_label)
			->set('label', $this->get_label())
			->set('name', $this->get_name())
			->set('value', $this->get_value())
			->set('icon', $this->get_icon())
			->bind('description', $this->_description)
			->set('form_horizontal', $this->is_form_horizontal())
			->render();
	}

	/*
	 * DEFAULT
	 */

	protected function _set_default() {
		if (!empty($this->_default_begin) && !empty($this->_default_end)) {
			if (is_numeric($this->_default_begin)) {
				if ($this->_show_time) {
					$this->_default_begin = date($this->_date_time_format_php, $this->_default_begin);
				} else {
					$this->_default_begin = date($this->_date_format_php, $this->_default_begin);
				}
				if ($this->_show_time) {
					$this->_default_end = date($this->_date_time_format_php, $this->_default_end);
				} else {
					$this->_default_end = date($this->_date_format_php, $this->_default_end);
				}
			}
			$this->_default = $this->_default_begin . ' - ' . $this->_default_end;
		}
	}

	public function get_value() {
		$this->_set_default();
		if (empty($this->_value && $this->_default_overwrite_empty && !empty($this->_default))) {
			$this->_value = $this->_default;
		}
		return parent::get_value();
	}

	/*
	 * SET
	 */

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

	public function format($date_format = 'DD.MM.YYYY') {
		$this->_date_format = (string)$date_format;
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

} // End Force_Form_Date_Range
