<?php
/**
 * User: legion
 * Date: 14.03.2020
 * Time: 19:57
 */
trait Core_Item_Calculate_Repair {

	public static function calculate_repair_ease(Jelly_Model $model) {
		if (empty($model->length_max)) {
			return $model->bRepairEase;
		}

		$bonus = Bonus::instance('bRepairEase');

		$value = 0;

		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT:
				$bonus->set_bonus(-1, Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT);
				break;
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_ROLLER:
				$bonus->set_bonus(-1, Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_ROLLER);
				break;
		}

		$bonus->apply($value);

		return !empty($value) ? round($value) : NULL;
	}

}