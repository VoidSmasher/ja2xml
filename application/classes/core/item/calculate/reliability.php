<?php
/**
 * User: legion
 * Date: 14.03.2020
 * Time: 19:56
 */
trait Core_Item_Calculate_Reliability {

	public static function calculate_reliability(Jelly_Model $model) {
		if (empty($model->length_max)) {
			return $model->bReliability;
		}

		$bonus = Bonus::instance('bReliability');

		$value = 0;

		if ($model->has_heavy_barrel) {
			$bonus->set_bonus(1, 'Heavy Barrel');
		}

		if ($model->has_sniper_barrel) {
			$bonus->set_bonus(-1, 'Matching Barrel');
		}

		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT:
				$bonus->set_bonus(-1, Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT);
				break;
		}

		$bonus->apply($value);

		return !empty($value) ? round($value) : NULL;
	}

}