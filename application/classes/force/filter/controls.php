<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Controls
 * User: legion
 * Date: 24.07.14
 * Time: 15:03
 */
class Force_Filter_Controls extends Force_Attributes {

	protected $_controls = array();

	public function __set($name, $value) {
		switch ($name) {
			case 'control':
				$this->add_control($value);
				break;
			case 'controls':
				$this->add_controls($value);
				break;
		}
	}

	/*
	 * CHECK
	 */

	protected static function _check_control(&$control) {
		if (!is_object($control)) {
			throw new Exception('Wrong control type, object required');
		}
		if (!($control instanceof Force_Filter_Control)) {
			throw new Exception(get_class($control) . ' is not a valid control for Force_Filter');
		}
		return true;
	}

	public function has_control($name) {
		return array_key_exists($name, $this->_controls);
	}

	/**
	 * @param        $name
	 * @param string $op_for_new_control
	 *
	 * @return Force_Filter_Control
	 */
	public function control($name, $op_for_new_control = '=') {
		if (!array_key_exists($name, $this->_controls)) {
			$this->add_control($name, $op_for_new_control);
		}
		return $this->_controls[$name];
	}

	public function add_control($control, $op = '=') {
		if (is_string($control)) {
			/*
			 * Упрощённый формат указания фильтра.
			 * Создаёт фильтры только типа Input.
			 */
			$control = trim($control);
			if (strpos($control, '.') !== false) {
				$parts = explode('.', $control);
				$field = $parts[0] . '.' . $parts[1];
				$control = $parts[1];
			} else {
				$field = $control;
			}
			$control = Force_Filter_Input::factory($control)->where($field, $op);
		}
		if ($this->_check_control($control)) {
			$this->_controls[$control->get_name()] = $control;
		}
		return $this;
	}

	public function add_controls(array $controls) {
		foreach ($controls as $name => $control) {
			$op = '=';
			if (is_string($control)) {
				/*
				 * Упрощённый формат указания фильтра.
				 * Создаёт фильтры только типа Input.
				 */
				if (!is_integer($name)) {
					$op = trim($control);
					$control = trim((string)$name);
				}
			}
			$this->add_control($control, $op);
		}
		return $this;
	}

	public function get_controls() {
		return $this->_controls;
	}

	public function get_control($name, $default = null) {
		if (array_key_exists($name, $this->_controls)) {
			return $this->_controls[$name];
		}
		return $default;
	}

	public function remove_control($name) {
		if (array_key_exists($name, $this->_controls)) {
			unset($this->_controls[$name]);
			return true;
		}
		return false;
	}

} // End Force_Filter_Controls
