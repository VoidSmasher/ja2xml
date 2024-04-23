<?php

/**
 * User: legion
 * Date: 14.03.2020
 * Time: 19:53
 */
trait Core_Item_Calculate_Coolness {

	private static $years = array(
		2020 => array(
			'bonus' => +0.75,
			'title' => 'New Era 20\'',
		),
		2010 => array(
			'bonus' => +0.65,
			'title' => 'New Era 10\'',
		),
		2000 => array(
			'bonus' => +0.55,
			'title' => 'Modern Era 00\'',
		),
		1990 => array(
			'bonus' => +0.45,
			'title' => 'USSR Fall Era 90\'',
		),
		1980 => array(
			'bonus' => +0.35,
			'title' => 'Cold-War Era 80\'',
		),
		1973 => array(
			'bonus' => +0.25,
			'title' => 'Cold-War Era 70\'',
		),
		1965 => array(
			'bonus' => +0.15,
			'title' => 'Vietnam War 65-73',
		),
		1953 => array(
			'bonus' => +0.05,
			'title' => 'Escalation Era 53-65',
		),
		1950 => array(
			'bonus' => -0.05,
			'title' => 'Korean War 50-53',
		),
		1945 => array(
			'bonus' => -0.05,
			'title' => 'Atom Era 45-50',
		),
		1940 => array(
			'bonus' => -0.15,
			'title' => 'WW2 40-45',
		),
		0 => array(
			'bonus' => -0.25,
			'title' => 'Old Era',
		),
	);

	public static function calculate_coolness(Model_Weapon_Group $model, array &$attachment_models) {
		if (empty($model->length_max) || empty($model->length_barrel)) {
			return ($model->ubCoolness) ? $model->ubCoolness : NULL;
		}

		if ($model->calc_coolness !== 'undefined') {
			return $model->calc_coolness;
		}

		$value = 0;

		$field = 'ubCoolness';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->is_secondary_weapon) {
			$value = $model->ubCoolness;
			$bonus->set_bonus($value, 'Calculated by Attachments');
			return $model->calc_coolness = $value;
		}

		/*
		 * Deadliness
		 */
//		self::set_damage_bonus($model, $bonus);
		self::set_deadliness_bonus($model, $bonus, $attachment_models);

		/*
		 * YEARS
		 */
		foreach (self::$years as $year => $_year) {
			if ($model->year_of_adoption > $year) {
				$bonus->set_bonus($_year['bonus'], $_year['title']);
				break;
			}
		}

		/*
		 * Bullet Type
		 */
		switch ($model->bullet_type) {
//			case Core_Calibre::TYPE_PISTOL:
//				$bonus->set_bonus(-1, 'Pistol Ammo');
//				break;
//			case Core_Calibre::TYPE_SHOTGUN:
//				$bonus->set_bonus(-0.5, 'Shotgun Ammo');
//				break;
//			case Core_Calibre::TYPE_RIFLE_ADVANCED:
//				$bonus->set_bonus(+1, 'Advanced Ammo');
//				break;
//			case Core_Calibre::TYPE_SNIPER:
//				$bonus->set_bonus(+1, 'Sniper Ammo');
//				break;
			case Core_Calibre::TYPE_ROCKET:
				$bonus->set_bonus(+2, 'Rocket');
				break;
		}

		$weapon_type = Core_Weapon::get_weapon_type($model);
		switch ($weapon_type) {
			case Core_Weapon::TYPE_SHOTGUN:
//				if ($model->APsToReloadManually < 1 && $model->ubMagSize > 2) {
//					$bonus->set_bonus(+0.75, 'Autoload');
//				}
				if (Attachment::has_mount($model, Attachment::MOUNT_CHOKE_SHORT)) {
					$bonus->set_bonus(+0.25, 'Short Choke');
				}
				if (Attachment::has_mount($model, Attachment::MOUNT_CHOKE_LONG)) {
					$bonus->set_bonus(+0.75, 'Long Choke');
				}
				if ($model->is_secondary_weapon) {
					$bonus->set_bonus(+2, 'Secondary Weapon');
				}
				break;
//			case Core_Weapon::TYPE_SNIPER:
//				$bonus->set_bonus(+1, 'Sniper Rifle');
//				break;
//			case Core_Weapon::TYPE_MACHINEGUN:
//				if ($model->weight > 5) {
//					$bonus->set_bonus(+1, 'Heavy Machinegun');
//				}
//				break;
			default:
				if ($model->is_secondary_weapon) {
					$bonus->set_bonus(+5, 'Secondary Weapon');
				}
		}

//		switch ($model->integrated_stock_index) {
//			case Core_Attachment_Data::INDEX_STOCK_PISTOL:
//				$bonus->set_bonus(-0.5, 'Pistol');
//				break;
//		}

//		if ($model->is_automatic() || $model->is_burst_fire_possible()) {
//			if ($model->has_full_auto()) {
//				$bonus->set_bonus(+1.25, 'Automatic');
//			} else {
//				$bonus->set_bonus(+0.75, 'Burst Fire');
//			}
//		}

		// Продвинутая автоматика
		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_GAZ_OPERATED_GUN_CARRIAGE:
				$bonus->set_bonus(+0.25, 'Gun Carriage');
				break;
			default:
				if ($model->has_balanced_automatic) {
					$bonus->set_bonus(+0.5, 'Balanced Automatic');
				}
		}

//		if (Weapon::has_quality($model, Weapon::QL_HIGH_QUALITY)) {
//			$bonus->set_bonus(+0.25, 'High Quality');
//		}
//
//		if (Weapon::has_quality($model, Weapon::QL_LOW_QUALITY)) {
//			$bonus->set_bonus(-0.25, 'Low Quality');
//		}

//		if ($model->has_compensator) {
//			$bonus->set_bonus(+1, 'Compensator');
//		}

//		if ($model->has_adjustable_butt_stock) {
//			$bonus->set_bonus(+1, 'Adjustable Butt-stock');
//		}

//		if ($model->has_adjustable_cheek_piece) {
//			$bonus->set_bonus(+1, 'Adjustable Cheek-piece');
//		} elseif ($model->has_cheek_piece) {
//			$bonus->set_bonus(+0.5, 'Fixed Cheek-piece');
//		}

		$attachments_info = Attachment::get_info($model, $attachment_models);

		if ($attachments_info->has_integral_laser) {
			$bonus->set_bonus(+1, 'Integral Laser');
		} elseif ($attachments_info->has_rifle_laser) {
			$bonus->set_bonus(+1, 'Rifle Laser');
		} elseif ($attachments_info->has_laser) {
			$bonus->set_bonus(+0.5, 'Laser');
		}

//		switch ($weapon_type) {
//			case Core_Weapon::TYPE_PISTOL:
//			case Core_Weapon::TYPE_MP:
//			case Core_Weapon::TYPE_SMG:
//			case Core_Weapon::TYPE_SHOTGUN:
//				$suppressor_coolness = 1;
//				break;
//			default:
//				$suppressor_coolness = 0.5;
//		}

		$suppressor_coolness = 1;
		$flash_hider_coolness = 0.5;

		if ($attachments_info->has_integral_suppressor) {
//			$bonus->set_bonus(+$suppressor_coolness, 'Integral Suppressor');
		} elseif ($attachments_info->has_suppressor) {
			$bonus->set_bonus(+$suppressor_coolness, 'Suppressor');
		} elseif ($attachments_info->has_integral_flash_hider) {
			$bonus->set_bonus(+$flash_hider_coolness, 'Integral Flash Hider');
		} elseif ($attachments_info->has_flash_hider) {
			$bonus->set_bonus(+$flash_hider_coolness, 'Flash Hider');
		}

		/*
		 * Прицельные приспособления
		 */
		if ($attachments_info->has_sight || $attachments_info->has_scope) {
			$bonus->set_bonus(+0.5, 'Advanced Targeting');
			if ($attachments_info->max_scope_magnitude > 1) {
				$bonus->set_bonus(round($attachments_info->max_scope_magnitude * 0.05, 2), 'Scope Magnification');
			}
		}
//		if (!$attachments_info->max_scope_magnitude && $attachments_info->has_sight) {
//			$bonus->set_bonus(+0.5, 'Advanced Sight');
//		}

		if ($attachments_info->scope_has_sight || $attachments_info->sight_has_scope
			|| ($attachments_info->has_sight && $attachments_info->has_integral_scope)) {
			if ($weapon_type != Core_Weapon::TYPE_SNIPER) {
				$bonus->set_bonus(+1, 'Combined Targeting');
			}
		}

		/*
		 * Дополнительное оружие и подствольные приспособления
		 */
		if ($attachments_info->has_bipod && $attachments_info->has_foregrip) {
			$bonus->set_bonus(+1, 'Bipod with Foregrip');
		} elseif (!$attachments_info->has_integral_secondary_weapon && $attachments_info->has_under_barrel_weapon) {
			$bonus->set_bonus(+1, 'Underbarrel Weapons');
		} elseif ($attachments_info->has_grippod || $attachments_info->has_foregrip) {
			$bonus->set_bonus(+0.7, 'Foregrip');
		} elseif ($attachments_info->has_bipod) {
			$bonus->set_bonus(+0.5, 'Bipod');
		}
		if ($attachments_info->has_integral_secondary_weapon) {
			$bonus->set_bonus(+0.5, 'Integral Secondary');
		}
		if ($attachments_info->has_multi_charge_gl) {
			$bonus->set_bonus(+0.5, 'Multi Charge GL');
		}

		$bonus->apply($value, false);

		$value = round($value);

		/*
		 * MIN VALUE
		 */
		switch ($weapon_type) {
			case Core_Weapon::TYPE_SNIPER:
				$min_value = 5;
				break;
			case Core_Weapon::TYPE_MACHINEGUN:
			case Core_Weapon::TYPE_AR:
				$min_value = 4;
				break;
			case Core_Weapon::TYPE_RIFLE:
			case Core_Weapon::TYPE_SMG:
			case Core_Weapon::TYPE_MP:
				$min_value = 2;
				break;
			default:
				$min_value = 1;
		}

		if ($value < $min_value) {
			$value = $min_value;
		}
		/*
		 * MAX VALUE
		 */
		if ($value > 10) {
			$value = 10;
		}

		return $model->calc_coolness = $value;
	}

	private static function set_damage_bonus(Model_Weapon_Group $model, Bonus $bonus) {
		$damage = Core_Weapon::calculate_damage($model);
		$range = Core_Weapon::calculate_range($model);

		$damage -= 10;

		$damage_bonus = round(pow($damage, 2) / 370, 1);
		if ($damage_bonus < 0) {
			$damage_bonus = 0;
		}

		if ($damage_bonus) {
			$bonus->set_bonus($damage_bonus, 'Damage');
		}

		if ($range > 999) {
			$bonus->set_bonus(+1, 'Long Range');
		}
	}

	private static function set_deadliness_bonus(Model_Weapon_Group $model, Bonus $bonus, array $attachment_models) {
		$deadliness = Core_Weapon::calculate_deadliness($model, $attachment_models);
		$deadliness /= 10;

		$deadliness = round($deadliness, 2);

		$bonus->set_bonus($deadliness, 'Deadliness');
	}

} // End Core_Item_Calculate_Coolness
