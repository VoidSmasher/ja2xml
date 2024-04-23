<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Input
 * User: legion
 * Date: 15.07.14
 * Time: 0:56
 */
class Force_Filter_Input extends Force_Filter_Control {

	public function __construct($name, $label = '') {
		$this->name($name);
		$this->label($label);
	}

	public static function factory($name, $label = '') {
		return new self($name, $label);
	}

	public function render() {
		if (empty($this->_label)) {
			$this->_label = $this->_name;
		}

		$this->attribute('class', 'form-control');
		$this->attribute('placeholder', $this->_label);

		return View::factory(FORCE_VIEW . 'filter/controls/input')
			->set('attributes', $this->get_attributes())
			->bind('use_filter_block', $this->_use_filter_block)
			->bind('label', $this->_label)
			->bind('name', $this->_name)
			->bind('value', $this->_value)
			->bind('start_from_new_line', $this->_start_from_new_line)
			->render();
	}

} // End Force_Filter_Input
