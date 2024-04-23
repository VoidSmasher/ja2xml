<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Note
 * User: legion
 * Date: 14.11.14
 * Time: 21:42
 */
class Force_Form_Note extends Force_Form_Control {

	protected $_alert_color = null;

	protected $_read_only = true;
	protected $_show_error = false;
	protected $_allow_combine = false;

	protected $_view = 'note';
	protected $_icon_class = 'fa-sticky-note-o';

	public function __construct($label = null, $text = null) {
		$this->label($label);
		$this->value($text);
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($label = null, $text = null) {
		return new self($label, $text);
	}

	protected function _render_simple() {
		$this->attribute('class', 'alert');
		if (!empty($this->_alert_color)) {
			$this->attribute('class', $this->_alert_color);
		}

		$this->_value = Helper_String::to_string($this->_value, '<br/>');

		return View::factory(self::CONTROLS_VIEW . 'simple/note')
			->set('attributes', $this->get_attributes())
			->bind('label', $this->_label)
			->bind('value', $this->_value)
			->render();
	}

	public function render() {
		if (!empty($this->_alert_color)) {
			$this->group_attribute('class', 'alert');
			$this->group_attribute('class', $this->_alert_color);
		}

		$this->_value = Helper_String::to_string($this->_value, '<br/>');

		return parent::render();
	}

	/*
	 * STYLES
	 */

	public function alert_success() {
		$this->_alert_color = 'alert-success';
		return $this;
	}

	public function alert_info() {
		$this->_alert_color = 'alert-info';
		return $this;
	}

	public function alert_warning() {
		$this->_alert_color = 'alert-warning';
		return $this;
	}

	public function alert_danger() {
		$this->_alert_color = 'alert-danger';
		return $this;
	}

} // End Force_Form_Note
