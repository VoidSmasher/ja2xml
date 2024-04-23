<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 03.05.2021
 * Time: 13:25
 */
trait Core_Weapon_Calculate_Reload {

	public static function calculate_reload(Model_Weapon_Group $weapon) {
		if (empty($weapon->length_barrel)) {
			return empty($weapon->APsToReload) ? NULL : $weapon->APsToReload;
		}

		if ($weapon->calc_reload !== 'undefined') {
			return $weapon->calc_reload;
		}

		$value = 0;

		$field = 'APsToReload';
//		if (Bonus::check($field)) {
//			return Bonus::instance($field);
//		}
		$bonus = Bonus::instance($field);

		self::reload_process_bonuses($weapon, $bonus);

		$bonus->apply($value, false);

		$value = round($value);

		if ($value < 0) {
			$value = 0;
		}

		return $weapon->calc_reload = $value;
	}

	public static function reload_process_bonuses(Model_Weapon_Group $weapon, Bonus $bonus) {
		$mag_size = Core_Weapon_Data::get_mag_size($weapon);
		$weight = Core_Weapon_Data::get_weight($weapon);
		$aptrm = Core_Weapon::calculate_aptrm($weapon);

		$calibre_info = Calibre::get_info_by_weapon($weapon);

		$ammo_weight = round($mag_size * $calibre_info->cartridge_weight, 2);
		$mag_weight_empty = round($mag_size * 0.02, 2);
		$mag_weight = $ammo_weight + $mag_weight_empty;

		$manual_pre_wind = $weight * 0.5 / 20;
		$manual_pre_wind = round($manual_pre_wind * $mag_size, 2);

		$mag_action_multiplier = 5.3;
		$mag_remove = round(sqrt($mag_weight_empty) * $mag_action_multiplier, 2);
		$mag_attach = round(sqrt($mag_weight) * $mag_action_multiplier, 2);

		$open_revolver_cylinder = 5;
		$close_revolver_cylinder = 3;

		$load_new_cartridges = $mag_size * $calibre_info->load_new_cartridge;
		$extract_empty_cases = $mag_size * $calibre_info->extract_empty_case;

		$loader_weight = round($calibre_info->cartridge_weight * 2, 2);
		$load_new_cartridges_revolver = 3 + $loader_weight + $calibre_info->load_new_cartridge;
		$extract_empty_cases_revolver = 3 + $calibre_info->extract_empty_case;

		$is_bullpup_reload = false;
		$bolt_hold_open = $weapon->has_bolt_hold_open;

		$bonus->set_bonus($weight, 'Weapon weight');
		if ($weapon->integrated_stock_index == Core_Attachment_Data::INDEX_STOCK_BULLPUP) {
			$is_bullpup_reload = true;
		}

		switch ($weapon->mechanism_action) {
			case Core_Weapon_Data::ACTION_BREAK:
				$bonus->set_bonus(5, 'Break-Action Open');
				break;
		}

		switch ($weapon->mechanism_reload) {
			case Core_Weapon_Data::RELOAD_MANUAL_AUTO_EXTRACT:
			case Core_Weapon_Data::RELOAD_TUBE:
				$is_bullpup_reload = false;
				$bonus->set_bonus($load_new_cartridges, 'Load new cartridges');
				break;
			case Core_Weapon_Data::RELOAD_MANUAL:
				$bonus->set_bonus($extract_empty_cases, 'Extract old cartridges');
				$bonus->set_bonus($load_new_cartridges, 'Load new cartridges');
				break;
			case Core_Weapon_Data::RELOAD_REVOLVER_PRE_WOUND:
				$bonus->set_bonus($load_new_cartridges, 'Load new cartridges');
				$bonus->set_bonus($mag_size * $manual_pre_wind, 'Pre-wind');
				break;
			case Core_Weapon_Data::RELOAD_REVOLVER:
				$bonus->set_bonus($open_revolver_cylinder, 'Open');
				$bonus->set_bonus($extract_empty_cases_revolver, 'Extract empty cases');
				$bonus->set_bonus($load_new_cartridges_revolver, 'Load new cartridges');
				$bonus->set_bonus($close_revolver_cylinder, 'Close');
				break;
			case Core_Weapon_Data::RELOAD_REVOLVER_AUTO_EXTRACT:
				$bonus->set_bonus($open_revolver_cylinder, 'Open');
				$bonus->set_bonus($load_new_cartridges_revolver, 'Load new cartridges');
				$bonus->set_bonus($close_revolver_cylinder, 'Close');
				break;
			case Core_Weapon_Data::RELOAD_BELT_HK21:
				$bonus->set_bonus($calibre_info->move_bolt, 'Move bolt');
				$bonus->set_bonus($calibre_info->rotate_bolt, 'Lock bolt');
				$bonus->set_bonus($mag_remove, 'Magazine remove');
				$bonus->set_bonus($mag_attach, 'Magazine attach');
				$bonus->set_bonus(4, 'Prepare belt');
				$bolt_hold_open = true;
				break;
			case Core_Weapon_Data::RELOAD_BELT:
				$bonus->set_bonus(3, 'Open belt cover');
				$bonus->set_bonus($mag_remove, 'Magazine remove');
				$bonus->set_bonus($mag_attach, 'Magazine attach');
				$bonus->set_bonus(2, 'Prepare belt');
				$bonus->set_bonus(2, 'Close belt cover');
				break;
			case Core_Weapon_Data::RELOAD_MAGAZINE_P90:
			case Core_Weapon_Data::RELOAD_MAGAZINE_G11:
				$is_bullpup_reload = false;
			case Core_Weapon_Data::RELOAD_MAGAZINE_DESERT_EAGLE:
				$bonus->set_bonus($mag_remove, 'Magazine remove');
				$bonus->set_bonus($mag_attach, 'Magazine attach');
				$bonus->set_bonus(round($calibre_info->move_bolt / 2, 2), 'Pull bolt slightly');
				break;
			case Core_Weapon_Data::RELOAD_MAGAZINE_PISTOL:
			case Core_Weapon_Data::RELOAD_MAGAZINE:
				$bonus->set_bonus($mag_remove, 'Magazine remove');
				$bonus->set_bonus($mag_attach, 'Magazine attach');
				break;
			case Core_Weapon_Data::RELOAD_MAGAZINE_G3:
				$bonus->set_bonus($calibre_info->move_bolt, 'Move bolt');
				$bonus->set_bonus($calibre_info->rotate_bolt, 'Lock bolt');
				$bonus->set_bonus($mag_remove, 'Magazine remove');
				$bonus->set_bonus($mag_attach, 'Magazine attach');
				$bolt_hold_open = true;
				break;
			case Core_Weapon_Data::RELOAD_EN_BLOC_CLIP:
				$bonus->set_bonus(round(sqrt($ammo_weight) * 6, 2), 'Inserting en bloc clip');
				break;
		}

		switch ($weapon->mechanism_action) {
			case Core_Weapon_Data::ACTION_BREAK:
				$bonus->set_bonus(3, 'Break-Action Close');
				break;
			case Core_Weapon_Data::ACTION_PUMP:
				$bonus->set_bonus($aptrm, 'Pump-Action recharge');
				break;
			case Core_Weapon_Data::ACTION_LEVER:
				$bonus->set_bonus($aptrm, 'Lever-Action recharge');
				break;
			case Core_Weapon_Data::ACTION_BOLT:
				$bonus->set_bonus($aptrm, 'Bolt-Action recharge');
				break;
			case Core_Weapon_Data::ACTION_BLOWBACK_SIMPLE:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_CHAMBER_RING:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_LEVER:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_GAZ:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_ROLLER:
			case Core_Weapon_Data::ACTION_SHORT_RECOIL_OPERATION:
			case Core_Weapon_Data::ACTION_LONG_RECOIL_OPERATION:
			case Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT:
			case Core_Weapon_Data::ACTION_GAZ_OPERATED_GUN_CARRIAGE:
			case Core_Weapon_Data::ACTION_GAZ_OPERATED_SHORT_STROKE:
			case Core_Weapon_Data::ACTION_GAZ_OPERATED_LONG_STROKE:
			case Core_Weapon_Data::ACTION_GAZ_OPERATED:
				if ($bolt_hold_open) {
					$bonus->set_bonus($calibre_info->drop_bolt, 'Drop bolt from hold');
				} else {
					$bonus->set_bonus($calibre_info->charge_bolt, 'Charge bolt');
				}
				break;
		}

		if ($is_bullpup_reload) {
			$bonus->set_bonus(6, 'Bullpup');
		}
	}

} // End Core_Weapon_Calculate_Reload
