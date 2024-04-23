<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 11.05.2021
 * Time: 4:07
 */
trait Core_Weapon_Calculate_Energy {

	public static function calculate_bullet_energy(Model_Weapon_Group $model, $bullet_speed = null) {
		// Рассчёты выполняются только для пуль
		if ($model->test_barrel_length < 1) {
			return NULL;
		}

		if (is_null($bullet_speed)) {
			$bullet_speed = Core_Calibre::calculate_bullet_speed($model, $model->length_barrel);
		}

		return Core_Calibre::calculate_bullet_energy($model, $bullet_speed);
	}

}
