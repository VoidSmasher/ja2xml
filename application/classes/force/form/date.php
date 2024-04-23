<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Date
 * User: legion
 * Date: 24.10.14
 * Time: 12:59
 */
class Force_Form_Date extends Force_Form_Input {

	protected $_pick_date = true;
	protected $_pick_time = false;
	protected $_pick_seconds = false;

	protected $_default_value = NULL;

	protected $_view = 'input_date';
	protected $_icon_class = 'fa-calendar';

	public function __construct($name = null, $label = null, $value = null) {
		parent::__construct($name, $label, $value);
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	public function render() {
		$this->_update_label();
		$this->_check_for_error();

		/*
		 * Иначе херь какая-то
		 */
		if (!$this->_pick_date && !$this->_pick_time) {
			$this->_pick_date = true;
		}

		Helper_Assets::add_styles('assets/common/css/bootstrap-datetimepicker.min.css');
		Helper_Assets::add_scripts(array(
			'assets/common/js/moment.min.js',
			'assets/common/js/bootstrap-datetimepicker.min.js',
			'assets/common/js/form.datetimepicker.js',
		));

		$this->attribute('class', 'form-control');
		$this->attribute('data-date-format', 'DD.MM.YYYY');

		$params = array(
			'name' => $this->_name,
			'params' => array(
				'locale' => 'ru',
			),
		);

		$this->attribute('data-date-useSeconds', 0);
		$date_format = '';
		$data_date_format = '';
		if ($this->_pick_date) {
			$this->attribute('data-date-pickDate', 1);
			$date_format = 'd.m.Y';
			$data_date_format = 'DD.MM.YYYY';
		} else {
			$this->attribute('data-date-pickDate', 0);
		}
		if ($this->_pick_time) {
			if (empty($date_format)) {
				$date_format = 'H:i';
				$data_date_format = 'HH:mm';
			} else {
				$date_format .= ' H:i';
				$data_date_format .= ' HH:mm';
			}
			if ($this->_pick_seconds) {
				$date_format .= ':s';
				$data_date_format .= ':ss';
				$this->attribute('data-date-useSeconds', 1);
			}
			$this->attribute('data-date-pickTime', 1);
			if ($this->_pick_date) {
				$this->attribute('data-date-sideBySide', 1);
			}
		} else {
			$this->attribute('data-date-pickTime', 0);
		}

		if (!empty($data_date_format)) {
			$this->attribute('data-date-format', $data_date_format, true);
		}
		$params['params']['format'] = $this->get_attribute('data-date-format', 'DD.MM.YYYY');

		/*
		 * VALUE
		 */

		if (empty($this->_value) && !empty($this->_default_value)) {
			$this->_value = $this->_default_value;
		}

		if (!empty($this->_value)) {
			$datetime = Force_Date::factory($this->_value);
			if (!empty($date_format)) {
				$this->_value = $datetime->format($date_format);
			}
			$params['params']['defaultDate'] = $datetime->format_sql();
		}

		Helper_Assets::js_vars_push_array('form_datetime', $params);

		/* ===== SIMPLE RENDER ===== */
		if ($this->_simple) {
			return $this->_render_simple();
		}

		return parent::render();
	}

	/*
	 * SET
	 */

	public function pick_date($value = true) {
		$this->_pick_date = boolval($value);
		return $this;
	}

	public function pick_time($value = true) {
		$this->_pick_time = boolval($value);
		return $this;
	}

	public function pick_seconds($value = true) {
		$this->_pick_time = true;
		$this->_pick_seconds = boolval($value);
		return $this;
	}

	public function default_value($value) {
		$this->_default_value = $value;
		return $this;
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$new_value = strtotime($new_value);
		if ($new_value == 0) {
			$new_value = ($this->is_allow_null_value()) ? null : time();
		}
		$this->value($new_value);
		return $this->get_value();
	}

} // End Force_Form_Date
