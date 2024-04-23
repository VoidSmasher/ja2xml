<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 19:59
 */
trait Core_Weapon_Calculate_Burst {

	public static function calculate_burst_ap(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->bBurstAP) ? NULL : $model->bBurstAP;
		}

		if ($model->calc_burst_ap !== 'undefined') {
			return $model->calc_burst_ap;
		}

		/*
		 * Нельзя ориентироваться на ubShotsPerBurst,
		 * потому что для многих стволов очередь посчитана заранее
		 * в надежде на установку УСМ с режимом стрельбы очередями.
		 */
		$fire_rate = Core_Weapon_Data::get_fire_rate_burst($model);

		/*
		 * Может забить уже на эти 20 AP?
		 */
		if (empty($fire_rate)) {
			return 20;
		}
//		if (($model->bBurstAP == 20) && empty($fire_rate)) {
//			return $model->bBurstAP;
//		}

		if ($model->ubShotsPerBurst) {
			$burst_length = $model->ubShotsPerBurst;
		} else {
			$burst_length = 3;
		}

//		$burst_ap = $model->calibre_burst_recoil * ($burst_length * 59 + 2) / $fire_rate;
//		$burst_ap = 45 * ($burst_length * 59 + 2) / $fire_rate;

//		$burst_ap = $fire_rate / ((pow($fire_rate, 1.9) / ($burst_length * 1500)) + 4);
		$burst_ap = $fire_rate / ((pow($fire_rate, 1.8) / ($burst_length * 700)));

		$bonus = Core_Weapon::generate_burst_ap_bonus($model);
		$bonus->apply($burst_ap);

		$burst_ap_new = round($burst_ap);

		if ($burst_ap_new < 4) {
			$burst_ap_new = 4;
		}

		if ($burst_ap_new > 17) {
			$burst_ap_new = 17;
		}

		/*
		 * Хрен его знает почему, но
		 * всячески избегаем значения в 20 AP
		 */
//		if ($burst_ap_new == 20) {
//			if ($burst_ap > 20) {
//				$burst_ap_new = 21;
//			} else {
//				$burst_ap_new = 19;
//			}
//		}

		return $model->calc_burst_ap = $burst_ap_new;
	}

	/*
	 * BONUS
	 */

	public static function generate_burst_ap_bonus(Model_Weapon_Group $model) {
		$field = 'bBurstAP';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if (!empty($model->burst_ap_bonus)) {
			$bonus->set_bonus($model->burst_ap_bonus, 'Weapon Bonus');
		}
		if (!empty($model->burst_ap_bonus_percent)) {
			$bonus->set_bonus_percent($model->burst_ap_bonus_percent, 'Weapon Bonus');
		}

		return $bonus;
	}

} // End Core_Weapon_Calculate_Burst
