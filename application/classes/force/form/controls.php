<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Controls
 * User: legion
 * Date: 24.07.14
 * Time: 14:53
 * @require Force_Control_Name
 * @require Force_Control_Label
 *
 * @property Force_Control $control
 * @property array $controls
 */
trait Force_Form_Controls {

	protected $_allow_string_controls = true;
	protected $_control_name_as_control_id = false;
	protected $_control_type_as_control_id = false;

	/*
	 * Ибо нех. Доступ только через функции.
	 */
	private $_controls = array();

	protected function _set_controls($name, $value) {
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

	public static function check_control(&$control) {
		return ($control instanceof Force_Form_Control
			|| $control instanceof Force_Form_Container);
	}

	protected static function _check_control(&$control) {
		if (!self::check_control($control)) {
			throw new Exception(var_export($control, true) . ' is not a valid control for Force_Form');
		}
		return true;
	}

	/*
	 * CONTROLS
	 */

	public function get_control_id(&$control) {
		$unique_id = null;
		if (is_object($control)) {
			if ($control instanceof Force_Form_Container) {
				$unique_id = $control->get_name();
			} elseif ($control instanceof Force_Form_Control) {
				if ($this->_control_type_as_control_id) {
					$unique_id = $control->get_type();
				} elseif ($this->_control_name_as_control_id) {
					$unique_id = $control->get_name();
				} else {
					$unique_id = $control->get_name() . $control->get_label();
				}
			}
		} elseif (is_string($control) && !empty($control)) {
			$unique_id = 'control_' . $control;
		}
		if (empty($unique_id)) {
			$unique_id = 'control_' . count($this->_controls);
		}
		if ($this->_control_name_as_control_id) {
			return $unique_id;
		} else {
			return md5($unique_id);
		}
	}

	public function add_controls(array $controls) {
		foreach ($controls as $control_id => $control) {
			if (!is_string($control_id)) {
				$control_id = null;
			}
			$this->add_control($control, $control_id);
		}
		return $this;
	}

	public function add_control($control, $control_id = null) {
		if (empty($control)) {
			return $this;
		}
		if (is_null($control_id)) {
			$control_id = $this->get_control_id($control);
		}
		if ($this->_allow_string_controls && is_string($control) && !empty($control)) {
			$this->_controls[$control_id] = (string)$control;
		} elseif (self::_check_control($control)) {
			$this->_controls[$control_id] = $control;
		}
		return $this;
	}

	public function get_control_names($recursive = false) {
		$control_names = array();
		$controls = $this->_controls;
		foreach ($controls as $control_id => $control) {
			if (!($control instanceof Force_Form_Container)) {
				if (is_null($control)) {
					$name = $control_id;
				} elseif ($control instanceof Force_Form_Control) {
					$name = $control->get_name();
				} else {
					$name = (string)$control;
				}
				if (!empty($name)) {
					$control_names[] = $name;
				}
			}
			if ($recursive) {
				if ($control instanceof Force_Form_Container) {
					$control_names = array_merge($control_names, $control->get_control_names($recursive));
				}
			}
		}
		return $control_names;
	}

	/*
	 * GET CONTROL
	 */

	public function has_control($control_id) {
		return array_key_exists($control_id, $this->_controls);
	}

	public function get_control($control_id) {
		$control = null;
		if ($this->has_control($control_id)) {
			$control = $this->_controls[$control_id];
		}
		return $control;
	}

	public function get_controls($merge_all_containers = false) {
		$controls = $this->_controls;
		if ($merge_all_containers) {
			foreach ($controls as $control_id => $control) {
				if ($control instanceof Force_Form_Container) {
					$controls = array_merge($controls, $control->get_controls($merge_all_containers));
					unset($controls[$control_id]);
				}
			}
		}
		return $controls;
	}

	public function remove_control($control_id = null) {
		if (is_array($control_id)) {
			foreach ($control_id as $_control_id) {
				$this->remove_control($_control_id);
			}
		}
		if (!empty($control_id) && $this->has_control($control_id)) {
			unset($this->_controls[$control_id]);
		}
		return $this;
	}

	public function remove_controls() {
		$this->_controls = array();
		return $this;
	}

} // End Force_Form_Controls
