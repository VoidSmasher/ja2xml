<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Tabs
 * User: legion
 * Date: 26.05.18
 * Time: 15:54
 */
class Force_Tabs extends Force_Attributes {

	use Force_Tabs_Controls;

	protected $_view = 'force/tabs/default';
	protected $_view_row = NULL;
	protected $_active = NULL;

	public static function factory(array $controls = array()) {
		return new self($controls);
	}

	public function __construct(array $controls = array()) {
		$this->attribute('class', 'nav nav-tabs');
		$this->add_tabs($controls);
	}

	public function __toString() {
		return $this->render();
	}

	public function __set($name, $value) {
		$this->_set_tabs($name, $value);
	}

	public function render() {
		$controls = $this->get_tabs();

		if (!empty($this->_active)) {
			$active = $controls[$this->_active];
		} else {
			$active = array_shift(array_values($controls));
		}

		if ($active instanceof Force_Tab) {
			$active->attribute('class', 'active');
		}

		return View::factory($this->_view)
			->set('attributes', $this->get_attributes())
			->set('controls', $controls)
			->render();
	}

	/*
	 * VIEW
	 */

	public function view($view) {
		$view = (string)$view;
		$this->_view = trim($view, ' /');
		return $this;
	}

	/*
	 * ACTIVE
	 */

	public function activate($name) {
		$this->_active = (string)$name;
		return $this;
	}

	public function is_active($name) {
		return ($this->_active == $name);
	}

} // End Force_Tabs
