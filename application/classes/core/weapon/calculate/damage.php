<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:28
 */
trait Core_Weapon_Calculate_Damage {

	public static function calculate_damage(Model_Weapon_Group $model) {
		if (empty($model->length_barrel) || !$model->calibre_damage) {
			return empty($model->ubImpact) ? NULL : $model->ubImpact;
		}

		if ($model->calc_damage !== 'undefined') {
			return $model->calc_damage;
		}

		$energy = Core_Weapon::calculate_bullet_energy($model);
		$damage = Core_Calibre::calculate_damage($model, $energy);

		$damage = round($damage);

		if ($damage == 0) {
			$damage = NULL;
		}

		return $model->calc_damage = $damage;
	}

} // End Core_Weapon_Calculate
