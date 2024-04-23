<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:51
 */
trait Core_Weapon_Calculate_APTRM {

	public static function calculate_aptrm(Model_Weapon_Group $weapon) {
		if (empty($weapon->length_barrel)) {
			return empty($weapon->APsToReloadManually) ? NULL : $weapon->APsToReloadManually;
		}

		if ($weapon->calc_aptrm !== 'undefined') {
			return $weapon->calc_aptrm;
		}

		$value = 0;

		$field = 'APsToReloadManually';
//		if (Bonus::check($field)) {
//			return Bonus::instance($field);
//		}
		$bonus = Bonus::instance($field);

		self::aptrm_process_bonuses($weapon, $bonus);

		$bonus->apply($value, false);

		$value = round($value);

		if ($value <= 0) {
			$value = NULL;
		}

		return $weapon->calc_aptrm = $value;
	}

	public static function aptrm_process_bonuses(Model_Weapon_Group $weapon, Bonus $bonus) {
		if ($weapon->aptrm_bonus) {
			$bonus->set_bonus($weapon->aptrm_bonus, 'Weapon Bonus');
		}
		if ($weapon->aptrm_bonus_percent) {
			$bonus->set_bonus_percent($weapon->aptrm_bonus_percent, 'Weapon Bonus');
		}

		$weight = Core_Weapon_Data::get_weight($weapon);

		$calibre_info = Calibre::get_info_by_weapon($weapon);

		$pump_weight = $weight * 0.05 + $calibre_info->bolt_weight;
		$pump_action = pow($pump_weight, 0.3);
		$move_pump = round($pump_action, 2);

		$lever_weight = $weight * 0.05 + $calibre_info->bolt_weight;
		$lever_action = pow($lever_weight, 0.3) * 2;
		$move_lever = round($lever_action, 2);

		$grip_weight = $weight * 0.3 + $calibre_info->bolt_weight;
		$grip_action = pow($grip_weight, 0.3) * 3;
		$move_grip = round($grip_action, 2);

		switch ($weapon->mechanism_action) {
			case Core_Weapon_Data::ACTION_BOLT:
				switch ($weapon->mechanism_feature) {
					case Core_Weapon_Data::FEATURE_STRAIGHT_PULL_BOLT:
						$bonus->set_bonus($calibre_info->move_bolt, 'Open bolt');
						$bonus->set_bonus($calibre_info->extract_empty_case, 'Extract empty case');
						$bonus->set_bonus($calibre_info->load_new_cartridge, 'Load new cartridge');
						$bonus->set_bonus($calibre_info->move_bolt, 'Close bolt');
						break;
					case Core_Weapon_Data::FEATURE_STRAIGHT_PULL_GRIP:
						$bonus->set_bonus($move_grip, 'Open grip');
						$bonus->set_bonus($calibre_info->extract_empty_case, 'Extract empty case');
						$bonus->set_bonus($calibre_info->load_new_cartridge, 'Load new cartridge');
						$bonus->set_bonus($move_grip, 'Close grip');
						break;
					default:
						$bonus->set_bonus($calibre_info->rotate_bolt_up, 'Unlock bolt');
						$bonus->set_bonus($calibre_info->move_bolt, 'Open bolt');
						$bonus->set_bonus($calibre_info->extract_empty_case, 'Extract empty case');
						$bonus->set_bonus($calibre_info->load_new_cartridge, 'Load new cartridge');
						$bonus->set_bonus($calibre_info->move_bolt, 'Close bolt');
						$bonus->set_bonus($calibre_info->rotate_bolt_down, 'Lock bolt');
						break;
				}
				break;
			case Core_Weapon_Data::ACTION_LEVER:
				$bonus->set_bonus($move_lever, 'Open lever');
				$bonus->set_bonus($calibre_info->extract_empty_case, 'Extract empty case');
				$bonus->set_bonus($calibre_info->load_new_cartridge, 'Load new cartridge');
				$bonus->set_bonus($move_lever, 'Close lever');
				break;
			case Core_Weapon_Data::ACTION_PUMP:
				$bonus->set_bonus($move_pump, 'Open pump');
				$bonus->set_bonus($calibre_info->extract_empty_case, 'Extract empty case');
				$bonus->set_bonus($calibre_info->load_new_cartridge, 'Load new cartridge');
				$bonus->set_bonus($move_pump, 'Close pump');
				break;
		}
	}

} // End Core_Weapon_Calculate_APTRM
