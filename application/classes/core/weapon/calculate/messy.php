<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 20:13
 */
trait Core_Weapon_Calculate_Messy {

	public static function calculate_messy_range(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->MaxDistForMessyDeath) ? NULL : $model->MaxDistForMessyDeath;
		}

		if ($model->calc_messy_range !== 'undefined') {
			return $model->calc_messy_range;
		}

		$range = Core_Weapon::calculate_range($model);
		$range_in_tiles = $range / 10;

		$messy_range = pow($range, 2.5) / 8500000 + 6.1;

		$bonus = Core_Weapon::generate_messy_range_bonus($model);
		$bonus->apply($messy_range);

		if ($messy_range > $range_in_tiles) {
			$messy_range = floor($range_in_tiles);
		}

		if ($messy_range < 0) {
			$messy_range = 0;
		}

		return $model->calc_messy_range = round($messy_range);
	}

	/*
	 * MESSY RANGE BONUS
	 */

	public static function generate_messy_range_bonus(Model_Weapon_Group $model) {
		$accuracy = Core_Weapon::calculate_accuracy($model);

		$field = 'MaxDistForMessyDeath';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

//		$bonus->set_bonus_percent($damage, 'Damage');
		$bonus->set_bonus_percent($accuracy, 'Accuracy');

		return $bonus;
	}

} // End Core_Weapon_Calculate_Messy
