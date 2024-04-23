<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Buttons
 * User: legion
 * Date: 15.01.15
 * Time: 22:57
 *
 * @property string $button
 */
trait Force_Control_Buttons {

	protected $_buttons = array();
	protected $_show_buttons = true;

	/*
	 * Метод предназначен для вставки в __set в подключаемых классах
	 */
	protected function _set_buttons($name, $value) {
		switch ($name) {
			case 'button':
				$this->button($value);
				break;
		}
	}

	/*
	 * BUTTONS
	 */

	public function button_before($button, $link = null, array $attributes = null) {
		$button = $this->_get_button($button, $link, $attributes);
		$caption = $button->get_label();
		$this->_buttons = array(
				$caption => $button,
			) + $this->_buttons;
		return $this;
	}

	public function button($button, $link = null, array $attributes = null) {
		$button = $this->_get_button($button, $link, $attributes);
		$caption = $button->get_label();
		$this->_buttons[$caption] = $button;
		return $this;
	}

	protected function _get_button($button, $link = null, array $attributes = null) {
		if ($button instanceof Force_Button) {
		} else {
			$caption = (is_string($button) && !empty($button)) ? trim($button) : '';
			if (empty($caption)) {
				return $this;
			}
			$button = Force_Button::factory($caption);
		}
		if (!empty($link)) {
			$button->link($link);
		}
		if (!empty($attributes)) {
			$button->attributes($attributes);
		}
		return $button;
	}

	public function button_html_before($html) {
		$this->_buttons = array(
				strval($html),
			) + $this->_buttons;
		return $this;
	}

	public function button_html($html) {
		$this->_buttons[] = strval($html);
		return $this;
	}

	public function show_buttons($value = true) {
		$this->_show_buttons = boolval($value);
		return $this;
	}

	public function hide_buttons() {
		$this->_show_buttons = false;
		return $this;
	}

} // End Force_Control_Buttons
