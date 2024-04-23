<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Steps
 * User: legion
 * Date: 25.12.15
 * Time: 14:44
 * @todo доделать, но так ли оно вообще должно быть?
 */
trait Force_Form_Steps {

	/*
	 * Ибо нех. Доступ только через функции.
	 */
	private $_steps = array();
	private $_current_step = 1;

	protected function _set_steps($name, $value) {
		switch ($name) {
			case 'step':
				$this->add_step($value);
				break;
			case 'steps':
				$this->add_steps($value);
				break;
		}
	}

	/*
	 * CHECK
	 */

	public static function check_step(&$step) {
		return ($step instanceof Force_Form_Step);
	}

	protected static function _check_step(&$step) {
		if (!self::check_step($step)) {
			throw new Exception(var_export($step, true) . ' is not a valid step for Force_Form');
		}
		return true;
	}

	/*
	 * CONTROLS
	 */

	public function add_steps(array $steps) {
		foreach ($steps as $step) {
			$this->add_step($step);
		}
		return $this;
	}

	public function add_step($step, $step_id = null) {
		if (self::_check_step($step)) {
			if (is_null($step_id)) {
				$step_id = $step->get_number();
			}
			$this->_steps[$step_id] = $step;
		}
		return $this;
	}

	public function get_steps() {
		return $this->_steps;
	}

	public function get_step() {
		return $this->_current_step;
	}

} // End Force_Form_Steps
