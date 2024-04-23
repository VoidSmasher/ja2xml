<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:42
 */
trait Core_Weapon_Calculate_Ready {

	public static function calculate_ready(Model_Weapon_Group $model, $round = true) {
		if (empty($model->length_barrel)) {
			return empty($model->ubReadyTime) ? NULL : $model->ubReadyTime;
		}

		if ($model->calc_ready === 'undefined') {
			$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);
			$length = Core_Weapon::get_ready_length($model, $weight_front_percent);
			$weight = Core_Weapon::get_ready_weight($model, $weight_front_percent);
			$weight_front = Core_Weapon::get_ready_weight_front($model, $weight_front_percent);

			$ready = $length + $weight + $weight_front;

			$bonus = Core_Weapon::generate_ready_bonus($model);
			$bonus->apply($ready);

			$model->calc_ready = $ready;
		}

		$ready = $model->calc_ready;

		if ($round) {
			$ready = floor($ready);
		}

		$ready = ($ready > 0) ? $ready : NULL;

		return $ready;
	}

	/*
	 * BONUS
	 */

	public static function generate_ready_bonus(Model_Weapon_Group $model) {
		$field = 'ubReadyTime';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->ready_bonus) {
			$bonus->set_bonus($model->ready_bonus, 'Weapon Bonus');
		}
		if ($model->ready_bonus_percent) {
			$bonus->set_bonus_percent($model->ready_bonus_percent, 'Weapon Bonus');
		}

		if (!Core_Weapon_Data::has_stock($model)) {
			$bonus->set_bonus_percent(-30, 'No Stock');
		}

		if ($model->has_adjustable_butt_stock) {
			$bonus->set_bonus_percent(-5, 'Adjustable Butt-stock');
		}

		if ($model->has_adjustable_cheek_piece) {
			$bonus->set_bonus_percent(-5, 'Adjustable Cheek-piece');
		}

		if ($model->has_adjustable_grip) {
			$bonus->set_bonus_percent(-5, 'Adjustable Grip');
		}

		if ($model->has_drum_mag) {
			$bonus->set_bonus_percent(10, 'Drum Mag');
		}

		return $bonus;
	}

	/*
	 * HELPERS
	 */

	public static function get_ready_weight_front(Model_Weapon_Group $model, $weight_front_percent) {
		$weight = Core_Weapon_Data::get_weight($model);
		$weight *= 10;
		$weight_front = $weight * $weight_front_percent / 100;

		$weight_penalty = pow($weight_front, 0.5) * 1.3 - 2.7;

		return $weight_penalty;
	}

	public static function get_ready_weight(Model_Weapon_Group $model, $weight_front_percent) {
		$weight = Core_Weapon_Data::get_weight($model);
		$weight *= 10;

//		$weight_penalty = pow($weight_front, 0.5) * 3 - 5.3;

		$weight_penalty = pow($weight, 0.5) * 1.3 - 2.7;

		return $weight_penalty;
	}

	public static function get_ready_length(Model_Weapon_Group $model, $weight_front_percent) {
		$length_penalty = $model->length_front_to_trigger;

//		$length_penalty = pow($length_penalty, 1.4) / 2400 - 0.5;
		$length_penalty = pow($length_penalty, 1) / 100 - 0.5;

		return $length_penalty;
	}

} // End Core_Weapon_Calculate_Ready
