<?php
/**
 * User: legion
 * Date: 15.01.2020
 * Time: 19:12
 */
trait Core_Item_Calculate_Dirt {

	public static function calculate_dirt_increase_factor(Jelly_Model $model) {
		if (empty($model->length_max)) {
			return $model->DirtIncreaseFactor;
		}

		$bonus = Bonus::instance('DirtIncreaseFactor');

		$value = 20;

		switch ($model->mechanism_feature) {
			/*
			 * More accurate for the first round and for semi automatic fire:
			 * - No movement of heavy working parts prior to firing to potentially inhibit accuracy.
			 * - Round sits consistently in the chamber.
			 * - Potentially shorter delay between operator pulling the trigger and round being fired
			 * (also known as lock time).
			 */
			case Core_Weapon_Data::FEATURE_OPENED_BOLT:
				$bonus->set_bonus_percent(30, Core_Weapon_Data::FEATURE_OPENED_BOLT);
				break;
		}

		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT:
				$bonus->set_bonus(40, Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT);
				break;
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_ROLLER:
				$bonus->set_bonus(10, Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_ROLLER);
				break;
		}

		$bonus->apply($value, false);

		return !empty($value) ? round($value) : NULL;
	}

}