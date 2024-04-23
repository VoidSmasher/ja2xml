<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Horizontal
 * User: legion
 * Date: 17.03.17
 * Time: 18:45
 */
trait Force_Control_Horizontal {

	/*
	 * Трёхпозиционный переключатель
	 * - null
	 * - true
	 * - false
	 */
	private $_form_horizontal = null;
	private $_form_horizontal_label_width = 0;

	/*
	 * FORM STYLE
	 */

	public function form_horizontal($label_width = 2) {
		if ($label_width === true) {
			$label_width = 2;
		}
		if ($label_width > 11) {
			$label_width = 11;
		}
		if ($label_width < 0) {
			$label_width = 0;
		}
		$this->_form_horizontal = is_null($label_width) ? null : boolval($label_width);
		$this->_form_horizontal_label_width = intval($label_width);
		return $this;
	}

	public function is_form_horizontal() {
		return $this->_form_horizontal;
	}

	public function is_form_horizontal_undefined() {
		return is_null($this->_form_horizontal);
	}

	public function get_form_horizontal_label_width() {
		return $this->_form_horizontal_label_width;
	}

} // End Force_Control_Horizontal
