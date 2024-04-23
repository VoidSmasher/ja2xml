<?php
/**
 * User: legion
 * Date: 14.03.2020
 * Time: 19:57
 */
trait Core_Item_Calculate_Overheating {

	public static function calculate_overheating_cooldown(Jelly_Model $model) {
		if (empty($model->length_max)) {
			return $model->usOverheatingCooldownFactor;
		}

		$value = 100;

		return !empty($value) ? round($value) : NULL;
	}

}