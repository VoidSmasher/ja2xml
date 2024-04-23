<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Tab
 * User: legion
 * Date: 26.05.18
 * Time: 15:53
 */
class Force_Tab extends Force_Attributes {

	use Force_Control_Name;
	use Force_Control_Label;
	use Force_Control_Link;
	use Force_Attributes_Link;

	protected $_active = false;
	/*
	 * Содержит в себе ссылку на родительский селектор для управления активностью элементов.
	 */
	protected $_tabs;

	public static function factory($name, $label = NULL) {
		return new self($name, $label);
	}

	public function __construct($name, $label = NULL) {
		$this->name($name);
		$this->label($label);
		$this->attribute('role', 'presentation');
	}

	public function __set($name, $value) {
		if ($name == 'tabs' && $value instanceof Force_Tabs) {
			$this->_tabs = $value;
		}
	}

	/*
	 * ACTIVE
	 */

	public function active() {
		if ($this->_tabs instanceof Force_Tabs) {
			$this->_tabs->activate($this->get_name());
		}
		$this->_active = true;
		return $this;
	}

	public function is_active() {
		if ($this->_tabs instanceof Force_Tabs) {
			$this->_active = $this->_tabs->is_active($this->get_name());
		}
		return $this->_active;
	}

} // End Force_Tab
