<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:39
 */
trait Core_Weapon_Calculate_Accuracy {

	public static function calculate_accuracy(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->nAccuracy) ? NULL : $model->nAccuracy;
		}

		if ($model->calc_accuracy !== 'undefined') {
			return $model->calc_accuracy;
		}

		$range = Core_Weapon::calculate_range($model);
		$accuracy = Core_Weapon::get_accuracy_unmodified($range);

//		$accuracy += $model->accuracy_delta;

		$bonus = Core_Weapon::generate_accuracy_bonus($model);
		$bonus->apply($accuracy);

		$accuracy = round($accuracy);

		if ($accuracy < 1) {
			$accuracy = NULL;
		}

		return $model->calc_accuracy = $accuracy;
	}

	public static function get_clean_accuracy(Model_Weapon_Group $model, $accuracy) {
		$bonus = Core_Weapon::generate_accuracy_bonus($model);
		$bonus->remove($accuracy);

		$accuracy = round($accuracy);

		return $accuracy;
	}

	public static function get_accuracy_unmodified($range) {
		$x = $range;

		/*
		 * В основном пистолеты, MP и начало SMG.
		 */
		$key_point_1 = 170;
		/*
		 * В этом диапазоне находится основная масса оружия.
		 * 7.62x51 - первые пять стволов
		 */
		$key_point_2 = 500;
		/*
		 * 5.56x45 SCF
		 * 7.62x51
		 * 7.62x54R
		 * 7.92x57 Mauser
		 * 12.7x97 Subsonic
		 * .30-06
		 * .300 WinMag
		 */
		$key_point_3 = 850;
		/*
		 * .50 BMG
		 * 12.7x108
		 * .338 Lapua Magnum
		 */

		if ($x < $key_point_1) {
			$y = atan($x / 50 - 3.53) * 21 + 32;
//			$y = atan($x / 50 - 3.45) * 21 + 30;
		} elseif ($x >= $key_point_1 && $x < $key_point_2) {
			$y = atan($x / 80 - 0.5) * 122 - 95;
//			$y = atan($x / 80 - 0.5) * 120 - 93.4;
//			$y = atan($x / 80 - 0.5) * 118 - 90.5;
		} elseif ($x >= $key_point_2 && $x < $key_point_3) {
			$y = atan($x / 110 - 0.5) * 95 - 50.6;
		} else {
			$y = atan($x / 110 - 0.5) * 95 - 50.6;
//			$y = atan($x / 110 - 0.5) * 90 - 44;
		}

		if ($y < 0) $y = 0;

		return $y;
	}

	public static function get_accuracy_delta(Model_Weapon_Group $model, $accuracy, $range = null) {
		$accuracy = Core_Weapon::get_clean_accuracy($model, $accuracy);

		if (is_null($range)) {
			$range = Core_Weapon::calculate_range($model);
		}

		$accuracy_unmodified = Core_Weapon::get_accuracy_unmodified($range);

		$accuracy_delta = $accuracy - $accuracy_unmodified;

		return $accuracy_delta;
	}

	/*
	 * BONUS
	 */

	public static function generate_accuracy_bonus(Model_Weapon_Group $model) {
		$field = 'nAccuracy';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->accuracy_bonus) {
			$bonus->set_bonus($model->accuracy_bonus, 'Weapon Bonus');
		}
		if ($model->accuracy_bonus_percent) {
			$bonus->set_bonus_percent($model->accuracy_bonus_percent, 'Weapon Bonus');
		}

		$weapon_type = Core_Weapon::get_weapon_type($model);

		switch ($weapon_type) {
			case Core_Weapon::TYPE_SNIPER:
				if ($model->sniper_accuracy_bonus_percent) {
					$bonus->set_bonus_percent($model->sniper_accuracy_bonus_percent, 'Sniper Bonus');
				} elseif ($model->sniper_accuracy_bonus) {
					$bonus->set_bonus($model->sniper_accuracy_bonus, 'Sniper Bonus');
				}
				break;
		}

		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_BOLT:
			case Core_Weapon_Data::ACTION_LEVER:
				$bonus->set_bonus(2, Core_Weapon_Data::ACTION_BOLT);
				break;
//			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_GAZ:
//				$bonus->set_bonus(1, Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_GAZ);
//				break;
			case Core_Weapon_Data::ACTION_SHORT_RECOIL_OPERATION:
			case Core_Weapon_Data::ACTION_LONG_RECOIL_OPERATION:
				$bonus->set_bonus(-1, 'Barrel Recoil Operation');
				break;
		}

//		В категорию попадают как bolt-action винтовки так и ряд дробовиков и даже револьвер U-94 Udar.
//		if ($model->APsToReloadManually > 0) {
//			2;
//		}

		/*
		 * УСМ одинарного действия необходимо каждый раз перед выстрелом взводить в ручную,
		 * если оружие не имеет никакой автоматики.
		 * УСМ двойного действия перед выстрелом взводит курок, что повышает оперативность стрельбы,
		 * но из-за дополнительных усилий со стороны стрелка понижает точность огня.
		 * Наличие автоматики снимает минус по точности.
		 */
		switch ($model->mechanism_trigger) {
			case Core_Weapon_Data::TRIGGER_SINGLE_ACTION:
				if (empty($model->mechanism_action)) {
					$bonus->set_bonus(1, Core_Weapon_Data::TRIGGER_SINGLE_ACTION);
				}
				break;
			case Core_Weapon_Data::TRIGGER_DOUBLE_ACTION:
				if (empty($model->mechanism_action)) {
					$bonus->set_bonus(-1, Core_Weapon_Data::TRIGGER_DOUBLE_ACTION);
				}
				break;
		}

		switch ($model->mechanism_feature) {
			/*
			 * More accurate for the first round and for semi automatic fire:
			 * - No movement of heavy working parts prior to firing to potentially inhibit accuracy.
			 * - Round sits consistently in the chamber.
			 * - Potentially shorter delay between operator pulling the trigger and round being fired
			 * (also known as lock time).
			 */
			case Core_Weapon_Data::FEATURE_OPENED_BOLT:
				$bonus->set_bonus(-1, Core_Weapon_Data::FEATURE_OPENED_BOLT);
				break;
		}

		switch ($weapon_type) {
			case Core_Weapon::TYPE_SNIPER:
				if ($model->sniper_accuracy_bonus) {
					$bonus->set_bonus($model->sniper_accuracy_bonus, 'Sniper Bonus');
				}
				if ($model->sniper_accuracy_bonus_percent) {
					$bonus->set_bonus_percent($model->sniper_accuracy_bonus_percent, 'Sniper Bonus');
				}
				break;
		}

//		switch ($model->ubCalibre) {
//			case Core_Calibre::CALIBRE_556_45_SCF:
//				$bonus->set_bonus(1, 'High Speed Projectile');
//				break;
//		}

		if (Weapon::has_quality($model, Weapon::QL_VERY_HIGH_QUALITY)) {
			$bonus->set_bonus(2, 'Very High Quality');
		}

		if (Weapon::has_quality($model, Weapon::QL_HIGH_QUALITY)) {
			$bonus->set_bonus(1, 'High Quality');
		}

		if (Weapon::has_quality($model, Weapon::QL_LOW_QUALITY)) {
			$bonus->set_bonus(-1, 'Low Quality');
		}

		if (Weapon::has_quality($model, Weapon::QL_VERY_LOW_QUALITY)) {
			$bonus->set_bonus(-2, 'Very Low Quality');
		}

		if ($model->has_hp_scope_mount) {
			$bonus->set_bonus(-1, 'High Profile Scope Mount');
		}

		if ($model->has_adjustable_cheek_piece || $model->has_cheek_piece) {
			$bonus->set_bonus(1, 'Cheek-piece');
		}

		if ($model->has_floating_barrel) {
			$bonus->set_bonus(1, 'Free-floating Barrel');
		}

		if (!Core_Weapon_Data::has_stock($model)) {
			$bonus->set_bonus_percent(-10, 'No Stock');
		}

		if (!Core_Weapon_Data::is_two_handed($model)) {
			$bonus->set_bonus_percent(-30, 'One Handed');
		}

		return $bonus;
	}

} // End Core_Weapon_Calculate_Accuracy