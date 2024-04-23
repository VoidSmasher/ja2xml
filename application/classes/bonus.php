<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Bonus
 * User: legion
 * Date: 29.09.19
 * Time: 18:12
 */
class Bonus {

	private $value = 0;
	private $value_caption;
	private $bonus_data = array();
	private $bonus_data_percent = array();
	private $bonus_data_percent_after = array();
	private $bonus_value = 0;
	private $bonus_value_percent = 0;
	private $bonus_value_percent_after = 0;

	private static $instances = array();

	/**
	 * @param $name
	 * @return Bonus
	 */
	public static function instance($name) {
		if (!array_key_exists($name, self::$instances)) {
			self::$instances[$name] = new self();
		}
		return self::$instances[$name];
	}

	public static function clear() {
		foreach (self::$instances as $name => $instance) {
			unset($instance);
		}
		self::$instances = array();
	}

	public static function check($name) {
		return array_key_exists($name, self::$instances);
	}

	/*
	 * SET
	 */

	public function set_bonus($value, $caption) {
		$this->bonus_data[$caption] = $value;
		$this->bonus_value += $value;
		return $value;
	}

	public function set_bonus_percent($value, $caption, $apply_after_calculation = false) {
		$this->bonus_data_percent[$caption] = $value;
		$this->bonus_value_percent += $value;
		return $value;
	}

	public function set_bonus_percent_after($value, $caption) {
		$this->bonus_data_percent_after[$caption] = $value;
		$this->bonus_value_percent_after += $value;
		return $value;
	}

	/*
	 * APPLY
	 */

	public function apply(&$value, $percent_first = true, $caption = 'Base') {
		$this->value = $value;
		$this->value_caption = $caption;
		if ($percent_first) {
			$value = $this->apply_value_percent($value);
			$value = $this->apply_value($value);
		} else {
			$value = $this->apply_value($value);
			$value = $this->apply_value_percent($value);
		}
		$value = $this->apply_value_percent_after($value);

		return $value;
	}

	private function apply_value(&$value) {
		$bonus_value = $this->get_bonus_value();
		if (!empty($bonus_value)) {
			$value += $bonus_value;
		}

		return $value;
	}

	private function apply_value_percent(&$value) {
		$bonus_value_percent = $this->get_bonus_value_percent();
		if (!empty($bonus_value_percent)) {
			$value += $value * $bonus_value_percent / 100;
		}

		return $value;
	}

	private function apply_value_percent_after(&$value) {
		$bonus_value_percent = $this->get_bonus_value_percent_after();
		if (!empty($bonus_value_percent)) {
			$value += $value * $bonus_value_percent / 100;
		}

		return $value;
	}

	/*
	 * REMOVE
	 */

	public function remove($value, $percent_first = true) {
		$value = $this->remove_value_percent_after($value);
		if ($percent_first) {
			$value = $this->remove_value_percent($value);
			$value = $this->remove_value($value);
		} else {
			$value = $this->remove_value($value);
			$value = $this->remove_value_percent($value);
		}

		return $value;
	}

	private function remove_value(&$value) {
		$bonus_value = $this->get_bonus_value();
		if (!empty($bonus_value)) {
			$value -= $bonus_value;
		}

		return $value;
	}

	private function remove_value_percent(&$value) {
		$bonus_value_percent = $this->get_bonus_value_percent();
		if (!empty($bonus_value_percent)) {
			$bonus_value_percent += 100;
			$value = $value * 100 / $bonus_value_percent;
		}

		return $value;
	}

	private function remove_value_percent_after(&$value) {
		$bonus_value_percent = $this->get_bonus_value_percent_after();
		if (!empty($bonus_value_percent)) {
			$bonus_value_percent += 100;
			$value = $value * 100 / $bonus_value_percent;
		}

		return $value;
	}

	/*
	 * GET
	 */

	public function get_bonus_value() {
		return $this->bonus_value;
	}

	public function get_bonus_value_percent() {
		return $this->bonus_value_percent;
	}

	public function get_bonus_value_percent_after() {
		return $this->bonus_value_percent_after;
	}

	public function get_bonus_line() {
		$result = self::mix_bonuses($this->bonus_value, $this->bonus_value_percent, $this->bonus_value_percent_after);

		return $result;
	}

	public function get_bonus_data() {
		$result = array();
		if ($this->value !== 0) {
			$result[] = round($this->value, 2) . ' ' . $this->value_caption;
		}

		foreach ($this->bonus_data as $caption => $value) {
			if ($value != 0) {
				$result[] = (($value > 0) ? '+' : '') . $value . ' ' . $caption;
			}
		}

		foreach ($this->bonus_data_percent as $caption => $value) {
			if ($value != 0) {
				$result[] = (($value > 0) ? '+' : '') . $value . '% ' . $caption;
			}
		}

		foreach ($this->bonus_data_percent_after as $caption => $value) {
			if ($value != 0) {
				$result[] = (($value > 0) ? '*' : '/') . abs($value) . '% ' . $caption;
			}
		}

		return Helper_String::to_string($result, '<br/>');
	}

	/*
	 * HELPERS
	 */

	private static function mix_bonuses($bonus, $bonus_percent, $bonus_percent_after) {
		if (!empty($bonus_percent)) {
			$bonus_percent = number_format($bonus_percent, 2) . '%';
		} else {
			$bonus_percent = '';
		}

		if (!empty($bonus)) {
			if ($bonus > 0) {
				$bonus = '+' . $bonus;
			}
			if (!empty($bonus_percent)) {
				if ($bonus_percent > 0) {
					$bonus .= '+' . $bonus_percent;
				} else {
					$bonus .= $bonus_percent;
				}
			}
		} else {
			$bonus = $bonus_percent;
		}

		if (!empty($bonus_percent_after)) {
			if ($bonus_percent_after > 0) {
				$bonus .= '*' . number_format($bonus_percent_after, 2) . '%';
			} else {
				$bonus .= '/' . number_format(abs($bonus_percent_after), 2) . '%';
			}
		}

		return $bonus;
	}

} // End Bonus
