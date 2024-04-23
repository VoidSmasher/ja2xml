<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Control
 * User: legion
 * Date: 15.07.14
 * Time: 3:40
 */
abstract class Force_Filter_Control extends Force_Control {

	/*
	 * Необходимо обновить PHP до версии 5.4 или выше.
	 */
	use Force_Filter_Conditions;
	use Force_Attributes_Group;

	protected $_name = '';
	protected $_label = '';
	protected $_value = '';
	protected $_default = null;
	protected $_default_overwrite_empty = false;
	protected $_form_method = 'get';

	protected $_start_from_new_line = false;
	protected $_use_filter_block = true;
	protected $_submit = false;

	/*
	 * VALUE
	 */

	public function default_value($value, $overwrite_empty = true) {
		$this->_default = $value;
		$this->_default_overwrite_empty = $overwrite_empty;
		return $this;
	}

	public function get_value() {
		if ($this->_value === '') {
			$this->_value = Arr::get($this->_get_data_array(), $this->get_name(), $this->_default);

			if ($this->_default_overwrite_empty && empty($this->_value)) {
				$this->_value = $this->_default;
			}
		}
		return $this->_value;
	}

	/*
	 * VIEW SETUP
	 */

	public function start_from_new_line($value = true) {
		$this->_start_from_new_line = boolval($value);
		return $this;
	}

	public function use_filter_block($value = true) {
		$this->_use_filter_block = boolval($value);
		return $this;
	}

	/*
	 * FORM METHOD
	 */

	public function form_method_post() {
		$this->_form_method = 'post';
		return $this;
	}

	public function form_method_get() {
		$this->_form_method = 'get';
		return $this;
	}

	protected function _get_data_array() {
		switch ($this->_form_method) {
			case 'post':
				return $_POST;
			case 'get':
				return $_GET;
			default:
				return $_REQUEST;
		}
	}

	/*
	 * SUBMIT
	 */

	public function submit($value = true) {
		$this->_submit = boolval($value);
		return $this;
	}

} // End Force_Filter_Control
