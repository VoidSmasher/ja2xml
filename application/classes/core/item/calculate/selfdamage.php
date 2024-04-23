<?php
/**
 * User: legion
 * Date: 14.03.2020
 * Time: 19:58
 */
trait Core_Item_Calculate_Selfdamage {

	public static function calculate_damage_chance(Jelly_Model $model) {
		if (empty($model->length_max)) {
			return $model->DamageChance;
		}

		$bonus = Bonus::instance('DamageChance');

		$value = 10;

		if ($model->has_heavy_barrel) {
			$bonus->set_bonus_percent(-10, 'Heavy Barrel');
		}

		if ($model->has_sniper_barrel) {
			$bonus->set_bonus_percent(10, 'Matching Barrel');
		}

		$bonus->apply($value);

		return !empty($value) ? round($value) : NULL;
	}

}