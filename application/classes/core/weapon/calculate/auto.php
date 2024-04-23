<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:57
 */
trait Core_Weapon_Calculate_Auto {

	public static function calculate_auto_shots(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->bAutofireShotsPerFiveAP) ? NULL : $model->bAutofireShotsPerFiveAP;
		}

		if ($model->calc_auto_shots !== 'undefined') {
			return $model->calc_auto_shots;
		}

		if (!$model->has_full_auto()) {
			return $model->calc_auto_shots = NULL;
		}

		$fire_rate = Core_Weapon_Data::get_fire_rate_auto($model);
		$auto_shots = $fire_rate / 220;

		$auto_shots = round($auto_shots);

		$bonus = Core_Weapon::generate_auto_shots_bonus($model);
		$bonus->apply($auto_shots);

		if ($auto_shots < 1) {
			$auto_shots = 1;
		}

		return $model->calc_auto_shots = $auto_shots;
	}

	/*
	 * BONUS
	 */

	public static function generate_auto_shots_bonus(Model_Weapon_Group $model) {
		$field = 'bAutofireShotsPerFiveAP';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if (!empty($model->afsp5ap_bonus)) {
			$bonus->set_bonus($model->afsp5ap_bonus, 'Weapon Bonus');
		}
		if (!empty($model->afsp5ap_bonus_percent)) {
			$bonus->set_bonus_percent($model->afsp5ap_bonus_percent, 'Weapon Bonus');
		}

		return $bonus;
	}

} // End Core_Weapon_Calculate_Auto
