<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Modal
 * User: legion
 * Date: 15.01.15
 * Time: 22:14
 */
class Force_Modal extends Force_Attributes {

	use Force_Control_Name;
	use Force_Control_Label;
	use Force_Control_Buttons;
	use Force_Attributes_Modal;

	protected $_content = null;
	protected $_show_close = true;
	protected $_modal_fade = false;

	public function __construct($name) {
		$this->name($name);
	}

	public function __toString() {
		return $this->render();
	}

	public function __set($name, $value) {
		$this->_set_buttons($name, $value);
	}

	public static function factory($name) {
		return new self($name);
	}

	public function render() {
		$this->attribute('class', 'modal', false);
		if ($this->_modal_fade) {
			$this->attribute('class', 'fade', false);
		}
		if ($this->_show_label) {
			$this->attribute('id', $this->get_name(), false);
		}

		if ($this->_show_buttons && empty($this->_buttons)) {
			$this->button_close();
		}

		$this->attribute('tabindex', '-1');
		$this->attribute('role', 'dialog');
		$this->modal_attribute('class', 'modal-dialog');
		$this->modal_attribute('role', 'document');

		return View::factory(FORCE_VIEW . 'modal/default')
			->bind('name', $this->_name)
			->bind('label', $this->_label)
			->bind('content', $this->_content)
			->bind('show_close', $this->_show_close)
			->bind('show_label', $this->_show_label)
			->bind('show_buttons', $this->_show_buttons)
			->bind('buttons', $this->_buttons)
			->set('attributes', $this->render_attributes())
			->set('modal_attributes', $this->render_modal_attributes())
			->render();
	}

	public function content($value) {
		$this->_content = (string)$value;
		return $this;
	}

	public function button_close($caption_or_button = null) {
		if ((!($caption_or_button instanceof Force_Button) && !is_string($caption_or_button)) || empty($caption_or_button)) {
			$caption_or_button = __('common.close');
		}

		if (is_string($caption_or_button)) {
			$caption_or_button = Force_Button::factory($caption_or_button)
				->attribute('data-dismiss', 'modal');
		}

		if ($caption_or_button instanceof Force_Button) {
			$this->button($caption_or_button->attribute('data-dismiss', 'modal'));
		}
		return $this;
	}

	/*
	 * FOOTER
	 */

	/**
	 * @param bool|true $value
	 *
	 * @return $this
	 * @deprecated use show_buttons instead
	 */
	public function show_footer($value = true) {
		$this->show_buttons($value);
		return $this;
	}

	/**
	 * @return $this
	 * @deprecated use hide_buttons instead
	 */
	public function hide_footer() {
		$this->hide_buttons();
		return $this;
	}

	/*
	 * CLOSE SYMBOL
	 */

	public function show_close($value = true) {
		$this->_show_close = boolval($value);
		return $this;
	}

	public function hide_close() {
		$this->_show_close = false;
		return $this;
	}

	/*
	 * FADE
	 */

	public function modal_fade($value = true) {
		$this->_modal_fade = boolval($value);
		return $this;
	}

	/*
	 * SIZE
	 */

	public function modal_lg() {
		$this->replace_modal_attribute_class('modal-sm', 'modal-lg');
		return $this;
	}

	public function modal_sm() {
		$this->replace_modal_attribute_class('modal-lg', 'modal-sm');
		return $this;
	}

} // End Force_Modal
