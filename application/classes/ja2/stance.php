<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: JA2_Stance
 * User: legion
 * Date: 07.11.19
 * Time: 17:34
 */
class JA2_Stance {

	const FLAT_BASE = 'FlatBase';
	const PERCENT_BASE = 'PercentBase';
	const FLAT_AIM = 'FlatAim';
	const PERCENT_CAP = 'PercentCap';
	const PERCENT_HANDLING = 'PercentHandling';
	const PERCENT_TARGET_TRACKING_SPEED = 'PercentTargetTrackingSpeed';
	const PERCENT_DROP_COMPENSATION = 'PercentDropCompensation';
	const PERCENT_MAX_COUNTER_FORCE = 'PercentMaxCounterForce';
	const PERCENT_COUNTER_FORCE_ACCURACY = 'PercentCounterForceAccuracy';
	const PERCENT_COUNTER_FORCE_FREQUENCY = 'PercentCounterForceFrequency';
	const AIM_LEVELS = 'AimLevels';

	protected $stance;

	protected $fields = array(
		self::FLAT_BASE,
		self::PERCENT_BASE,
		self::FLAT_AIM,
		self::PERCENT_CAP,
		self::PERCENT_HANDLING,
		self::PERCENT_TARGET_TRACKING_SPEED,
		self::PERCENT_DROP_COMPENSATION,
		self::PERCENT_MAX_COUNTER_FORCE,
		self::PERCENT_COUNTER_FORCE_ACCURACY,
		self::PERCENT_COUNTER_FORCE_FREQUENCY,
		self::AIM_LEVELS,
	);

	protected $modifiers = array();

	public static function factory($stance, $json = null) {
		return new self($stance, $json);
	}

	public function __construct($stance, $json = null) {
		$this->stance = $stance;
		$this->modifiers = array();

		$this->set_json($json);
	}

	public function set_json($json) {
		if (!empty($json)) {
			$modifiers = json_decode($json, true);
			if (is_array($modifiers)) {
				$this->modifiers = $modifiers;
			}
		} else {
			$this->modifiers = array();
		}
	}

	public function set_value($field, $value, $can_modify = false) {
		if (!array_key_exists($field, $this->modifiers)) {
			$this->modifiers[$field] = $value;
		} elseif ($can_modify) {
			$this->modifiers[$field] += $value;
		}
	}

	public function modify_value($field, $value) {
		$this->set_value($field, $value, true);
	}

	public function modify_by_percent($field, $percent) {
		if (array_key_exists($field, $this->modifiers)) {
			$value = $this->modifiers[$field];

			$percent = 100 + $percent;

			$value = $value * $percent / 100;

			$this->modifiers[$field] = round($value);
		}
		return $this;
	}

	public function get_modifiers() {
		return $this->modifiers;
	}

	public function render() {
		if (!empty($this->modifiers)) {
			$value = json_encode($this->modifiers);
		} else {
			$value = NULL;
		}

		return $value;
	}

	public function update_MODIFIERS($STAND_OR_CROUCH_MODIFIERS, $can_modify = true) {
		if ($STAND_OR_CROUCH_MODIFIERS instanceof JA2_Stance) {
			$STAND_OR_CROUCH_MODIFIERS = $STAND_OR_CROUCH_MODIFIERS->get_modifiers();
		}
		if (!is_array($STAND_OR_CROUCH_MODIFIERS)) {
			return $this;
		}

		foreach ($STAND_OR_CROUCH_MODIFIERS as $field => $value) {
			$this->set_value($field, $value, $can_modify);
		}
		return $this;
	}

	public function clean_MODIFIERS($STAND_OR_CROUCH_MODIFIERS) {
		if ($STAND_OR_CROUCH_MODIFIERS instanceof JA2_Stance) {
			$STAND_OR_CROUCH_MODIFIERS = $STAND_OR_CROUCH_MODIFIERS->get_modifiers();
		}
		if (!is_array($STAND_OR_CROUCH_MODIFIERS)) {
			return $this;
		}

		foreach ($this->modifiers as $field => $value) {
			if (array_key_exists($field, $STAND_OR_CROUCH_MODIFIERS)) {
				if ($value == $STAND_OR_CROUCH_MODIFIERS[$field]) {
					unset($this->modifiers[$field]);
				}
			}
		}
		return $this;
	}

	public function FlatBase($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::FLAT_BASE, $value);
		} else {
			$this->set_value(self::FLAT_BASE, $value, true);
		}
		return $this;
	}

	public function PercentBase($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_BASE, $value);
		} else {
			$this->set_value(self::PERCENT_BASE, $value, true);
		}
		return $this;
	}

	public function FlatAim($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::FLAT_AIM, $value);
		} else {
			$this->set_value(self::FLAT_AIM, $value, true);
		}
		return $this;
	}

	public function PercentCap($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_CAP, $value);
		} else {
			$this->set_value(self::PERCENT_CAP, $value, true);
		}
		return $this;
	}

	public function PercentHandling($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_HANDLING, $value);
		} else {
			$this->set_value(self::PERCENT_HANDLING, $value, true);
		}
		return $this;
	}

	public function PercentTargetTrackingSpeed($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_TARGET_TRACKING_SPEED, $value);
		} else {
			$this->set_value(self::PERCENT_TARGET_TRACKING_SPEED, $value, true);
		}
		return $this;
	}

	public function PercentDropCompensation($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_DROP_COMPENSATION, $value);
		} else {
			$this->set_value(self::PERCENT_DROP_COMPENSATION, $value, true);
		}
		return $this;
	}

	public function PercentMaxCounterForce($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_MAX_COUNTER_FORCE, $value);
		} else {
			$this->set_value(self::PERCENT_MAX_COUNTER_FORCE, $value, true);
		}
		return $this;
	}

	public function PercentCounterForceAccuracy($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_COUNTER_FORCE_ACCURACY, $value);
		} else {
			$this->set_value(self::PERCENT_COUNTER_FORCE_ACCURACY, $value, true);
		}
		return $this;
	}

	public function PercentCounterForceFrequency($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::PERCENT_COUNTER_FORCE_FREQUENCY, $value);
		} else {
			$this->set_value(self::PERCENT_COUNTER_FORCE_FREQUENCY, $value, true);
		}
		return $this;
	}

	public function AimLevels($value, $modify_by_percent = false) {
		if ($modify_by_percent) {
			$this->modify_by_percent(self::AIM_LEVELS, $value);
		} else {
			$this->set_value(self::AIM_LEVELS, $value, true);
		}
		return $this;
	}

} // End JA2_Stance
