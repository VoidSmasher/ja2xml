<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 20:07
 */
trait Core_Weapon_Calculate_Deadliness {

	/*
	 * Устрашающее свойство оружия. Мерки любят пострашнее.
	 * min: 11
	 * max: 83
	 */

	public static function calculate_deadliness(Model_Weapon_Group $model, array &$attachment_models) {
		if (empty($model->length_barrel)) {
			return empty($model->ubDeadliness) ? NULL : $model->ubDeadliness;
		}

		if ($model->calc_deadliness !== 'undefined') {
			return $model->calc_deadliness;
		}

		$value = 0;

		$field = 'ubDeadliness';
//		if (Bonus::check($field)) {
//			return Bonus::instance($field);
//		}
		$bonus = Bonus::instance($field);

		self::deadliness_mechanic_bonuses($model, $bonus);
		self::deadliness_attachment_bonuses($model, $bonus, $attachment_models);

		$bonus->apply($value, false);

		$value = round($value);

		if ($value < 0) {
			$value = 0;
		}

		return $model->calc_deadliness = $value;
	}

	public static function deadliness_mechanic_bonuses(Model_Weapon_Group $model, Bonus $bonus) {
		$damage = Core_Weapon::calculate_damage($model);
		$range = Core_Weapon::calculate_range($model);
		$accuracy = Core_Weapon::calculate_accuracy($model);
//		$rof = Core_Weapon_Data::calculate_br_rof($model);
		$sp4t = Core_Weapon::calculate_sp4t($model);
		$ready = Core_Weapon::calculate_ready($model);
		$mag_size = Core_Weapon_Data::get_mag_size($model);
		$reload = Core_Weapon::calculate_reload($model);


		/*
		 * Объективные показатели
		 */
//		$deadliness = $model->calibre_damage;
		$divider = 5.9;

		$bonus->set_bonus(round($damage / $divider, 2), 'Damage');
		$bonus->set_bonus(round(pow($accuracy, 0.825) / $divider, 2), 'Accuracy');
		$bonus->set_bonus(round(pow($range, 0.825) / $divider, 2), 'Range');
		$bonus->set_bonus(round(pow($sp4t, 0.825) / $divider, 2), 'SP4T');
		$bonus->set_bonus(round(pow($mag_size, 0.825) / $divider, 2), 'Mag Size');
		$bonus->set_bonus(-round(pow($ready, 0.825) / $divider, 2), 'Ready');
		$bonus->set_bonus(-round(pow($reload, 0.825) / $divider, 2), 'Reload');
//		$deadliness /= 7.4;
//		$deadliness /= 5.157;
//		$deadliness /= 5.7; // +Coolness

//		$deadliness = round($deadliness, 2);

//		$bonus->set_bonus($deadliness, 'Deadliness');

		/*
		 * Coolness
		 */
//		$bonus->set_bonus_percent($model->ubCoolness, 'Coolness');

		/*
		 * Weapon Type
		 */
		$weapon_class = Core_Weapon::get_weapon_class($model);
//		$weapon_type = Core_Weapon::get_weapon_type($model);

		switch ($model->bullet_type) {
			case Core_Calibre::TYPE_SHOTGUN:
				$bonus->set_bonus(11, 'Shotgun');
				break;
		}

//		switch ($weapon_class) {
//			case Core_Weapon::CLASS_MACHINEGUN:
//				$bonus->set_bonus(2, 'Sustain Fire');
//				break;
//		}

		//		switch ($weapon_type) {
//			case Core_Weapon::TYPE_PISTOL:
//			case Core_Weapon::TYPE_MP:
//				$bonus->set_bonus_percent(30, 'Pistol');
//				break;
//			case Core_Weapon::TYPE_SNIPER:
//				$bonus->set_bonus_percent(5, 'Sniper Rifle');
//				break;
//			case Core_Weapon::TYPE_MACHINEGUN:
//				$bonus->set_bonus_percent(25, 'Machinegun');
//				$recoil_x = Core_Weapon::calculate_recoil_x($model);
//				$recoil_y = Core_Weapon::calculate_recoil_y($model);
//
//				$recoil = 31 - ($recoil_x + $recoil_y);
//
//				if ($recoil < 1) {
//					$recoil = 1;
//				}
//
//				$bonus->set_bonus_percent($recoil, 'Recoil Bonus');

//				$bonus->set_bonus_percent(20, 'Machinegun');
//				break;
//		}

		/*
		 * Action
		 */
		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_BOLT:
			case Core_Weapon_Data::ACTION_LEVER:
				$bonus->set_bonus(-3, Core_Weapon_Data::ACTION_BOLT);
				break;
			case Core_Weapon_Data::ACTION_PUMP:
				$bonus->set_bonus(-3, Core_Weapon_Data::ACTION_PUMP);
				break;
		}

		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_BOLT:
				/*
				 * The straight-pull action design was introduced in the Blaser R93 hunting rifle line.
				 * These actions allows for faster follow-up shots compared to traditional turn bolt actions.
				 */
				switch ($model->mechanism_feature) {
					case Core_Weapon_Data::FEATURE_STRAIGHT_PULL_BOLT:
						$bonus->set_bonus(1, Core_Weapon_Data::FEATURE_STRAIGHT_PULL_BOLT);
						break;
					case Core_Weapon_Data::FEATURE_STRAIGHT_PULL_GRIP:
						$bonus->set_bonus(0.5, Core_Weapon_Data::FEATURE_STRAIGHT_PULL_GRIP);
						break;
				}
				break;
		}

		if (Weapon::has_quality($model, Weapon::QL_HIGH_QUALITY)) {
			$bonus->set_bonus(1, 'High Quality');
		}

		if (Weapon::has_quality($model, Weapon::QL_LOW_QUALITY)) {
			$bonus->set_bonus(-1, 'Low Quality');
		}

		/*
		 * Advanced Ammo by default
		 */
//		switch ($model->bullet_type) {
//			case Core_Calibre::TYPE_RIFLE_ADVANCED:
//				$bonus->set_bonus_percent(10, 'Special Ammo');
//				break;
//		}

		/*
		 * Ready Time
		 */
//		$ready_max = 40;
//		$ready = Core_Weapon::calculate_ready($model);
//		if ($ready > $ready_max) {
//			$ready = $ready_max;
//		}
//
//		$ready = $ready_max - $ready;
//		if ($ready) {
//			$bonus->set_bonus_percent(round($ready / 1.5, 2), 'Ready Time');
//		}

		/*
		 * ROF
		 */
//		if ($rof) {
//			$rof_bonus = pow($rof, 0.9);
//			if ($model->is_automatic() && $rof > 100) {
//				$rof_bonus /= 2;
//			} else {
//				$rof_bonus *= 4;
//			}
//			$bonus->set_bonus_percent(round($rof_bonus / 10, 2), 'Rate of Fire');
//		}
//
//		if ($model->is_automatic() && $rof > 100) {
//			$recoil_x = Core_Weapon::calculate_recoil_x($model);
//			$recoil_y = Core_Weapon::calculate_recoil_y($model);
//
//			$recoil = (abs($recoil_x) + abs($recoil_y)) * 2;
//			$recoil = pow($recoil, 0.7);
//
//			switch ($weapon_type) {
//				case Core_Weapon::TYPE_SHOTGUN:
//					$recoil /= 2.5;
//					break;
//			}
//
//			if ($recoil < 1) {
//				$recoil = 1;
//			}
//
//			$bonus->set_bonus_percent(-round($recoil, 2), 'Recoil Penalty');
//		}

		/*
		 * Mag Size
		 */
//		$mag_size = Core_Weapon_Data::get_mag_size($model);
//		$mag_size_bonus = pow($mag_size, 0.625)/100;
//
//		$mag_size_bonus *= $bonus->get_bonus_value();
//		$bonus->set_bonus(round($mag_size_bonus, 2), 'Mag Size');

		/*
		 * Comfort
		 */
//		if ($model->has_adjustable_cheek_piece) {
//			$bonus->set_bonus(+1, 'Adjustable Cheek-piece');
//		} elseif ($model->has_cheek_piece) {
//			$bonus->set_bonus(+0.5, 'Fixed Cheek-piece');
//		}

//		if ($model->has_adjustable_butt_stock) {
//			$bonus->set_bonus(+0.5, 'Adjustable Butt-stock');
//		}

//		if ($model->has_adjustable_grip) {
//			$bonus->set_bonus(+0.5, 'Adjustable Grip');
//		}
	}

	public static function deadliness_calculate_suppressor(Model_Weapon_Group $item, Bonus $bonus, array &$attachment_models) {
		$attachments_info = Attachment::get_info($item, $attachment_models);

		$range = Core_Weapon::calculate_range($item);
		$range_in_tiles = $range / 10;
		if ($range_in_tiles > 100) {
			$range_in_tiles = 100;
		}
		$attack_volume = $item->get_attack_volume();
		if ($attack_volume < $range_in_tiles) {
			$percent_volume_reduction = ($range_in_tiles - $attack_volume) * 100 / $range_in_tiles;
			$bonus->set_bonus_percent(round($percent_volume_reduction / 3, 2), 'Silenced');
		}
	}

	public static function deadliness_attachment_bonuses(Model_Weapon_Group $item, Bonus $bonus, array &$attachment_models) {
		$attachments_info = Attachment::get_info($item, $attachment_models);

		if ($attachments_info->has_integral_suppressor) {
			$bonus->set_bonus_percent(20, 'Integral Suppressor');
		}

		/*
		 * Дополнительное оружие и подствольные приспособления
		 */
		if ($attachments_info->has_integral_secondary_weapon) {
			if (Attachment::has_mount($item, [
				Attachment::MOUNT_GL_AICW,
				Attachment::MOUNT_GL_OICW,
			])) {
				$bonus->set_bonus(+10, 'Automatic Grenade Launcher');
			} else {
				$bonus->set_bonus(+5, 'Integral Secondary Weapon');
			}
		}
	}

} // End Core_Weapon_Calculate_Deadliness
