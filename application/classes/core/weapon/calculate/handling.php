<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:47
 */
trait Core_Weapon_Calculate_Handling {

	public static function calculate_handling(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->Handling) ? NULL : $model->Handling;
		}

		if ($model->calc_handling !== 'undefined') {
			return $model->calc_handling;
		}

		$handling = Core_Weapon::calculate_ready($model);

		$bonus = Core_Weapon::generate_handling_bonus($model);
		$bonus->apply($handling);

		$handling = round($handling);

		if ($handling < 1) {
			$handling = 1;
		}

		return $model->calc_handling = $handling;
	}

	/*
	 * BONUS
	 */

	public static function generate_handling_bonus(Model_Weapon_Group $model) {
		$field = 'Handling';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->handling_bonus) {
			$bonus->set_bonus($model->handling_bonus, 'Weapon Bonus');
		}
		if ($model->handling_bonus_percent) {
			$bonus->set_bonus_percent($model->handling_bonus_percent, 'Weapon Bonus');
		}

//		if (!Core_Weapon_Data::has_stock($model)) {
//			$bonus->set_bonus_percent(10, 'No Stock');
//		}

//		if (!Core_Weapon_Data::is_two_handed($model)) {
//			$bonus->set_bonus_percent(10, 'One Handed');
//		}

		return $bonus;
	}

} // End Core_Weapon_Calculate_Handling
