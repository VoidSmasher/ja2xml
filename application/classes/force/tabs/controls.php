<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Tabs_Controls
 * User: legion
 * Date: 26.05.18
 * Time: 16:13
 */
trait Force_Tabs_Controls {

	/*
	 * Ибо нех. Доступ только через функции.
	 */
	private $_tabs = array();

	protected function _set_tabs($name, $value) {
		switch ($name) {
			case 'control':
				$this->add_tab($value);
				break;
			case 'controls':
				$this->add_tabs($value);
				break;
		}
	}

	/*
	 * CONTROLS
	 */

	protected function add_tabs(array $controls) {
		foreach ($controls as $control => $link) {
			if (is_integer($control)) {
				$this->add_tab($link);
			} elseif (is_string($control) && is_string($link)) {
				$this->add_tab($control, $link);
			}
		}
		return $this;
	}

	protected function add_tab($control, $link = null) {
		if (is_string($control)) {
			$control = Force_Tab::factory($control)->link($link);
		}

		if (!($control instanceof Force_Tab)) {
			throw new Exception(var_export($control, true) . ' is not a valid control for Force_Tabs');
		}

		$control->tabs = $this;

		$name = $control->get_name();

		$this->_tabs[$name] = $control;

		return $this;
	}

	/*
	 * GET
	 */

	protected function get_tabs() {
		return $this->_tabs;
	}

	/*
	 * QUICK ACCESS
	 */

	/**
	 * @param $name
	 *
	 * @return Force_Tab
	 * @throws Exception
	 */
	protected function tab($name) {
		$name = (string)$name;

		if (!array_key_exists($name, $this->_tabs) || !($this->_tabs[$name] instanceof Force_Tab)) {
			$tab = Force_Tab::factory($name);
			$this->add_tab($tab);
		}

		return $this->_tabs[$name];
	}

} // End Force_Tabs_Controls
