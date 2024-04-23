<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:38
 */
trait Core_Weapon_Calculate_Range {

	public static function calculate_range(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->usRange) ? NULL : $model->usRange;
		}

		if ($model->calc_range !== 'undefined') {
			return $model->calc_range;
		}

		$range = Core_Weapon::get_range_unmodified($model);

		/*
		 * range = pow(range_u, angle) * mult + delta
		 * делим потому что множители иначе получаются далеко за запятой и сильно страдает точность
		 */

		if ($model->range_angle > 0 && $model->range_angle != 1) {
			$range = pow($range, $model->range_angle);
		}

		if ($model->range_mult != 0) {
			$range *= $model->range_mult;
		}

		$range += $model->range_delta;

		$bonus = Core_Weapon::generate_range_bonus($model);

		if ($bonus_value = $bonus->get_bonus_value()) {
			$range += $bonus_value;
		}

		if ($bonus_value_percent = $bonus->get_bonus_value_percent()) {
			$range += $range * $bonus_value_percent / 100;
		}

		return $model->calc_range = Helper::round_to_five($range);
	}

	/*
	 * BONUS
	 */

	public static function generate_range_bonus(Model_Weapon_Group $model) {
		$field = 'usRange';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->range_bonus) {
			$bonus->set_bonus($model->range_bonus, 'Weapon Bonus');
		}
		if ($model->range_bonus_percent) {
			$bonus->set_bonus_percent($model->range_bonus_percent, 'Weapon Bonus');
		}

		$weapon_type = Core_Weapon::get_weapon_type($model);

		switch ($weapon_type) {
			case Core_Weapon::TYPE_SNIPER:
				if ($model->sniper_range_bonus) {
					$bonus->set_bonus($model->sniper_range_bonus, 'Sniper Bonus');
				}
				if ($model->sniper_range_bonus_percent) {
					$bonus->set_bonus_percent($model->sniper_range_bonus_percent, 'Sniper Bonus');
				}
				break;
		}

		if ($model->has_sniper_barrel) {
			$bonus->set_bonus_percent(8, 'Matching Barrel');
		}

		return $bonus;
	}

	/*
	 * HELPERS
	 */

	public static function get_clean_range(Model_Weapon_Group $model, $range) {
		$bonus = Core_Weapon::generate_range_bonus($model);

		$range_bonus_percent = $bonus->get_bonus_value_percent();
		$range_bonus = $bonus->get_bonus_value();

		if ($range_bonus_percent) {
			$range_bonus_percent += 100;
			$range = $range * 100 / $range_bonus_percent;
		}

		if ($range_bonus) {
			$range -= $range_bonus;
		}

		return $range;
	}

	public static function get_range_unmodified(Model_Weapon_Group $model) {
		$bullet_speed = Core_Calibre::calculate_bullet_speed($model, $model->length_barrel);

		if (!$bullet_speed || !$model->bullet_weight || !$model->bullet_coefficient) {
			return $model->usRange;
		}

		return Core_Bullet::get_range($bullet_speed, $model->bullet_weight, $model->bullet_diameter, $model->bullet_coefficient);
	}

	public static function get_range_delta(Model_Weapon_Group $model, $range) {
		$range = Core_Weapon::get_clean_range($model, $range);

		$range_unmodified = Core_Weapon::get_range_unmodified($model);

		// range = pow(range_u, angle) * mult + delta

		$range_delta = $range - pow($range_unmodified, $model->range_angle) * $model->range_mult;

		return $range_delta;
	}

	public static function get_range_mult(Model_Weapon_Group $model, $range) {
		$range = Core_Weapon::get_clean_range($model, $range);

		$range_unmodified = Core_Weapon::get_range_unmodified($model);

		/*
		 * range = pow(range_u, angle) / mult + delta
		 * mult = pow(range_u, angle) / (range - delta)
		 * делим потому что множители иначе получаются далеко за запятой и сильно страдает точность
		 */
		$range_mult = pow($range_unmodified, $model->range_angle) / ($range - $model->range_delta);

		return $range_mult;
	}

} // End Core_Weapon_Calculate_Range