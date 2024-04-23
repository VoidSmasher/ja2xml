<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:49
 */
trait Core_Weapon_Calculate_SP4T {

	public static function calculate_sp4t(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->ubShotsPer4Turns) ? NULL : number_format($model->ubShotsPer4Turns, 2);
		}

		if ($model->calc_sp4t !== 'undefined') {
			return $model->calc_sp4t;
		}

		$ready = Core_Weapon::calculate_ready($model, false);
		$recoil = Core_Weapon::calculate_recoil($model);
		$recoil_x = Core_Weapon::calculate_recoil_x($model, false);
		$recoil_y = Core_Weapon::calculate_recoil_y($model, false);

//		$weight = Core_Weapon_Data::get_weight($model);

		$recoil = ($recoil + $recoil_x + $recoil_y) / 2;

		$ready /= 10;

		$recoil = pow($recoil, 0.38);
		$ready = pow($ready, 0.38);

		$penalty = $recoil + $ready;
//		$penalty = $recoil;

		if ($penalty < 2) {
			$penalty = 2;
		}

//		$weight *= 10;
		// Чем больше масса, тем больше инерция, тем дольше боец тратит времени на стабилизацию оружия
//		$recoil *= (pow($weight, 0.2) / 1.6);

//		$sp4t = 37 / $recoil;
		$sp4t = 53 / $penalty;

		$bonus = Core_Weapon::generate_sp4t_bonus($model);
		$bonus->apply($sp4t);

		return $model->calc_sp4t = number_format($sp4t, 2);
	}

	/*
	 * BONUS
	 */

	public static function generate_sp4t_bonus(Model_Weapon_Group $model) {
		$field = 'ubShotsPer4Turns';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->sp4t_bonus) {
			$bonus->set_bonus($model->sp4t_bonus, 'Weapon Bonus');
		}
		if ($model->sp4t_bonus_percent) {
			$bonus->set_bonus_percent($model->sp4t_bonus_percent, 'Weapon Bonus');
		}

		$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);
		$weight_balance = 50 - $weight_front_percent;
		$bonus->set_bonus_percent(round($weight_balance / 7), 'Weight Balance');

		$weight = Core_Weapon_Data::get_weight($model);
		if ($weight) {
			$bonus->set_bonus_percent(+ceil($weight / 2), 'Weight Bonus');
		}

		$bonus->set_bonus_percent(-ceil($model->length_barrel / 100), 'Barrel Penalty');

		$weapon_type = Core_Weapon::get_weapon_type($model);

		switch ($weapon_type) {
			case Core_Weapon::TYPE_PISTOL:
				if (!empty($model->sp4t_pistol_bonus)) {
					$bonus->set_bonus($model->sp4t_pistol_bonus, 'Pistol Bonus');
				}
				break;
			case Core_Weapon::TYPE_MP:
				if (!empty($model->sp4t_mp_bonus)) {
					$bonus->set_bonus($model->sp4t_mp_bonus, 'Machine-Pistol Bonus');
				}
				break;
			case Core_Weapon::TYPE_RIFLE:
				if (!empty($model->sp4t_rifle_bonus)) {
					$bonus->set_bonus($model->sp4t_rifle_bonus, 'Rifle Bonus');
				}
				break;
		}

		switch ($model->mechanism_action) {
			/*
			 * Схема отличается большой массой подвижных частей и конструктивной сложностью,
			 * не позволяет развивать большой темп стрельбы.
			 */
			case Core_Weapon_Data::ACTION_LONG_RECOIL_OPERATION:
				$bonus->set_bonus_percent(-10, Core_Weapon_Data::ACTION_LONG_RECOIL_OPERATION);
				break;
			case Core_Weapon_Data::ACTION_GAZ_OPERATED_GUN_CARRIAGE:
				/*
				 * Лафет
				 */
				$bonus->set_bonus_percent(-5, Core_Weapon_Data::ACTION_GAZ_OPERATED_GUN_CARRIAGE);
				break;
		}

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
					$bonus->set_bonus_percent(-20, Core_Weapon_Data::TRIGGER_SINGLE_ACTION);
				}
				break;
			case Core_Weapon_Data::TRIGGER_DOUBLE_ACTION:
				if (empty($model->mechanism_action)) {
					$bonus->set_bonus_percent(-10, Core_Weapon_Data::TRIGGER_DOUBLE_ACTION);
				}
				break;
		}

		if ($model->has_adjustable_grip) {
			$bonus->set_bonus_percent(5, 'Adjustable Grip');
		}

//		if (!Core_Weapon_Data::has_stock($model)) {
//			$bonus->set_bonus_percent(10, 'No Stock');
//		}

//		if (!Core_Weapon_Data::is_two_handed($model)) {
//			$bonus->set_bonus_percent(20, 'One Handed');
//		}

		return $bonus;
	}

	/*
	 * UNUSED
	 */

	public static function calculate_sp4t_old(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->ubShotsPer4Turns) ? NULL : number_format($model->ubShotsPer4Turns, 2);
		}

		$recoil_x = Core_Weapon::calculate_recoil_x($model, false);
		$recoil_y = Core_Weapon::calculate_recoil_y($model, false);
		$weight = Core_Weapon_Data::get_weight($model);

		$recoil = $recoil_x + $recoil_y;

		$recoil = pow($recoil, 0.4);

		$weight *= 10;
		// Чем больше масса, тем больше инерция, тем дольше боец тратит времени на стабилизацию оружия
		$recoil *= (pow($weight, 0.2) / 1.6);

		$sp4t = 49 / $recoil;

		$bonus = Core_Weapon::generate_sp4t_bonus($model);
		$bonus->apply($sp4t);

		return number_format($sp4t, 2);
	}

} // End Core_Weapon_Calculate_SP4T
