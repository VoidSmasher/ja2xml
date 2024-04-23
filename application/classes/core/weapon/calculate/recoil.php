<?php

/**
 * User: legion
 * Date: 02.03.2020
 * Time: 20:02
 */
trait Core_Weapon_Calculate_Recoil {

	public static function calculate_recoil(Model_Weapon_Group $model) {
		if ($model->calc_recoil !== 'undefined') {
			return $model->calc_recoil;
		}

//		$length_front = Core_Weapon_Data::get_length_front($model);

		$weight = Core_Weapon_Data::get_weight($model);
		$energy = Core_Weapon::calculate_bullet_energy($model);

		$weight *= 10;

		if ($weight <= 0) {
			$weight = 0.1;
		}

//		$weight = pow($weight, 0.5);

		$weight = pow($weight, 0.3);
		// Общий показатель отдачи. Энергия в первую очередь компенсируется массой оружия.
		$recoil = $energy / $weight;

		$recoil = pow($recoil, 0.5);

		switch ($model->bullet_type) {
			case Core_Calibre::TYPE_SHOTGUN:
				$recoil /= 1.1;
				break;
			default:
				$recoil /= 1.8;
				break;
		}

		/*
		 * calibre_auto_recoil рассчитывается автоматически в таблице calibres.
		 * Служит в роли постоянной отдачи вызываемой детонацией пороха в патроне.
		 * Не зависит от ствола и типа оружия.
		 */
//		$calibre_recoil = pow($model->calibre_auto_recoil, 2) / 472.32;
//		$calibre_recoil = $model->calibre_auto_recoil;

		/*
		 * Плечо отдачи
		 * Здесь рассчитываем плечо отдачи, которое учитывает как длинну ствола,
		 * так и в целом часть оружия находящуюся перед рукоятью.
		 */
//		$barrel_penalty = pow($model->length_barrel + $length_front, 1.2) / 450;
//		$barrel_penalty = pow($model->length_barrel + ($length_front / 2), 0.8) / 25;
//		return $barrel_penalty;

		/*
		 * Отдача
		 */
//		$recoil = ($barrel_penalty * $calibre_recoil);

//		if ($model->ubImpact < 40) {
//			return false;
//		}
//		var_dump($model->szWeaponName);
//		Helper_Error::var_dump($model->calibre_auto_recoil, 'calibre_auto_recoil');
//		Helper_Error::var_dump($calibre_recoil, 'calibre_recoil');
//		Helper_Error::var_dump($barrel_penalty, 'barrel_penalty_old');
//		Helper_Error::var_dump($recoil, 'recoil_old');

//		Helper_Error::var_dump($barrel_penalty, 'barrel_penalty_new');
//		Helper_Error::var_dump($recoil, 'recoil_new');

		/*
		 * Гашение/усиление отдачи от приклада/точки удержания оружия
		 */
//		$height_diff_stock_barrel = Core_Weapon_Data::get_height_diff_stock_barrel($model);
//		if ($height_diff_stock_barrel) {
//			$stock_penalty = $recoil * ($height_diff_stock_barrel / 10) / 100;
//			$recoil += $stock_penalty;
//		}

		$model->calc_recoil = $recoil;

		return $model->calc_recoil;
	}

	public static function calculate_recoil2(Model_Weapon_Group $model) {
		$weight = Core_Weapon_Data::get_weight($model);
		$energy = Core_Weapon::calculate_bullet_energy($model);

		$weight *= 10;

		if ($weight <= 0) {
			$weight = 0.1;
		}

//		$weight = pow($weight, 0.5);

		$weight = pow($weight, 0.5);
		// Общий показатель отдачи. Энергия в первую очередь компенсируется массой оружия.
		$recoil = $energy / $weight;

		$recoil = pow($recoil, 0.4);
		return $recoil;
	}

	/*
	 * RECOIL X
	 */

	public static function calculate_recoil_x(Model_Weapon_Group $model, $round = true) {
		if (empty($model->length_barrel)) {
			return empty($model->bRecoilX) ? NULL : $model->bRecoilX;
		}

		if ($model->calc_recoil_x === 'undefined') {
			$recoil = Core_Weapon::calculate_recoil($model);
			$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);

			if (($weight_front_percent < 1) || ($model->calibre_auto_recoil < 1)) {
				return empty($model->bRecoilX) ? NULL : $model->bRecoilX;
			}

//		if (empty($model->bRecoilY)) {
//			return NULL;
//		}

			/*
			 * Рычаг отдачи
			 */
//		if ($model->length_min > 0) {
//			$length = $model->length_min;
//		} else {
//		$length = $model->length_max;
//		}

//		$length_front = Core_Weapon_Data::get_length_front($model);
//		$recoil_coef = $length / $length_front;

//		$recoil_coef = pow($recoil_coef * 10, 0.4) / 2;

//		$recoil_coef /= 2;

//		$recoil *= $recoil_coef;

//		$recoil /= 1.5;

//		return $recoil_coef;

			$recoil_x = $recoil;

			/*
			 * Гашение вертикальной отдачи только массой в передней части оружия
			 */
			$weight = Core_Weapon_Data::get_weight($model);
			$weight *= 1000;

			if ($weight < 1) {
				$weight = 1;
			}

			$weight = $weight * $weight_front_percent / 100;

			$weight = pow($weight, 0.31);

			$recoil_x /= $weight;

//		$recoil_x *= 1.1;
//		$recoil_x -= 0.4;

			$bonus = Core_Weapon::generate_recoil_x_bonus($model);
			$recoil_x = $bonus->apply($recoil_x);

			$model->calc_recoil_x = $recoil_x;
		}

		$recoil_x = $model->calc_recoil_x;

		if ($round) {
			$recoil_x = floor($recoil_x);
		}

		$recoil_x = ($recoil_x > 0) ? $recoil_x : NULL;

		return $recoil_x;
	}

	/*
	 * RECOIL Y
	 */

	public static function calculate_recoil_y(Model_Weapon_Group $model, $round = true) {
		if (empty($model->length_barrel)) {
			return empty($model->bRecoilY) ? NULL : $model->bRecoilY;
		}

		if ($model->calc_recoil_y === 'undefined') {
			$recoil = Core_Weapon::calculate_recoil($model);
			$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);

			if (($weight_front_percent < 1) || ($model->calibre_auto_recoil < 1)) {
				return empty($model->bRecoilY) ? NULL : $model->bRecoilY;
			}

//		if (empty($model->bRecoilY)) {
//			return NULL;
//		}

			/*
			 * Рычаг отдачи
			 */
			$length_front = Core_Weapon_Data::get_length_front($model);

			$recoil *= pow($length_front, 0.4);

			$recoil_y = $recoil;

//		$recoil_y = pow($recoil_y, 1.5);
			$recoil_y /= 3.4;

			/*
			 * Гашение вертикальной отдачи только массой в передней части оружия
			 */
			$weight = Core_Weapon_Data::get_weight($model);
			$weight *= 1000;

			if ($weight < 1) {
				$weight = 1;
			}

			$weight = $weight * $weight_front_percent / 100;

			$weight = pow($weight, 0.28);

			$recoil_y /= $weight;

			$bonus = Core_Weapon::generate_recoil_y_bonus($model);
			$bonus->apply($recoil_y);

			$model->calc_recoil_y = $recoil_y;
		}

		$recoil_y = $model->calc_recoil_y;

		if ($round) {
			$recoil_y = ceil($recoil_y);
		}

		if ($recoil_y < 3) {
			$recoil_y = 3;
		}

		return $recoil_y;
	}

	/*
	 * RECOIL BONUS
	 */

	protected static function generate_recoil_mechanism_action_bonus(Model_Weapon_Group $model, Bonus $bonus) {
		/*
		 * Штрафы и бонусы от mechanism_action влияют на общий импульс отдачи.
		 */
		switch ($model->mechanism_action) {
			case Core_Weapon_Data::ACTION_BOLT:
			case Core_Weapon_Data::ACTION_LEVER:
				/*
				 * Отсутствуют какие-либо механизмы борющиеся с отдачей,
				 * именно по этой причине распространено использование компенсаторов в прикладах.
				 */
				$bonus->set_bonus_percent(80, Core_Weapon_Data::ACTION_BOLT);
				break;
			case Core_Weapon_Data::ACTION_BLOWBACK_SIMPLE:
				/*
				 * Свободный затвор конструктивно проще любого другого типа запирания ствола.
				 * Однако для него характерны такие существенные недостатки, как излишняя масса оружия,
				 * склонность к высокому темпу стрельбы и увеличение колебаний оружия при стрельбе очередями
				 * за счёт быстрого возвратно-поступательного движения массивного затвора и его ударов в
				 * крайних положениях, что также способствует ускоренному износу оружия.
				 */
				$bonus->set_bonus_percent(60, Core_Weapon_Data::ACTION_BLOWBACK_SIMPLE);
				break;
			case Core_Weapon_Data::ACTION_SHORT_RECOIL_OPERATION:
				/*
				 * Отдача ствола с коротким ходом
				 */
				$bonus->set_bonus_percent(40, Core_Weapon_Data::ACTION_SHORT_RECOIL_OPERATION);
				break;
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_GAZ:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_ROLLER:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_LEVER:
			case Core_Weapon_Data::ACTION_BLOWBACK_DELAYED_CHAMBER_RING:
				/*
				 * Аналогично свободному затвору, но движение затвора замедлено различными приспособлениями.
				 */
				$bonus->set_bonus_percent(20, Core_Weapon_Data::ACTION_BLOWBACK_DELAYED);
				break;
			case Core_Weapon_Data::ACTION_LONG_RECOIL_OPERATION:
				/*
				 * Отдача ствола с длинным ходом
				 */
				$bonus->set_bonus_percent(-10, Core_Weapon_Data::ACTION_LONG_RECOIL_OPERATION);
				break;
			case Core_Weapon_Data::ACTION_GAZ_OPERATED_GUN_CARRIAGE:
				/*
				 * Лафет
				 */
				$bonus->set_bonus_percent(-30, Core_Weapon_Data::ACTION_GAZ_OPERATED_GUN_CARRIAGE);
				break;
			case Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT:
				/*
				 * Система прямого давления газами на затворную группу
				 * Ввиду отстутствия подвижных частей в виде газовых поршней имеет меньше колебаний
				 * при стрельбе.
				 */
				$bonus->set_bonus_percent(-15, Core_Weapon_Data::ACTION_DIRECT_GAZ_IMPINGEMENT);
				break;
		}

		return $bonus;
	}

	/*
	 * RECOIL X BONUS
	 */

	public static function generate_recoil_x_bonus(Model_Weapon_Group $model) {
		$field = 'bRecoilX';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->recoil_x_bonus) {
			$bonus->set_bonus($model->recoil_x_bonus, 'Weapon Bonus');
		}
		if ($model->recoil_x_bonus_percent) {
			$bonus->set_bonus_percent($model->recoil_x_bonus_percent, 'Weapon Bonus');
		}

		/*
		 * Гашение/усиление отдачи от приклада/точки удержания оружия
		 */
		$height_diff_stock_barrel = is_null($model->height_diff_stock_barrel) ? 50 : abs($model->height_diff_stock_barrel);
		if ($height_diff_stock_barrel) {
			$bonus->set_bonus_percent(round($height_diff_stock_barrel * 2.5), 'Height Diff Stock Barrel');
		}

//		$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);
//		if ($weight_front_percent) {
//			$weight_rear_percent = 100 - $weight_front_percent;
//			$bonus->set_bonus_percent(ceil($weight_rear_percent / 2), 'Weight Balance');
//		}

		$weight = Core_Weapon_Data::get_weight($model);
		if ($weight) {
			$bonus->set_bonus_percent(-round($weight * 2), 'Weight Bonus');
		}

		self::generate_recoil_mechanism_action_bonus($model, $bonus);

		if ($model->has_heavy_barrel) {
			$bonus->set_bonus_percent_after(-15, 'Heavy Barrel');
		}

		if ($model->has_balanced_automatic) {
			$bonus->set_bonus_percent_after(-20, 'Balanced Automatic');
		}

		if ($model->has_recoil_reducing_stock) {
			$bonus->set_bonus_percent_after(-15, 'Recoil-reducing Stock');
		}

		if ($model->has_recoil_buffer_in_stock) {
			$bonus->set_bonus_percent_after(-10, 'Recoil Buffer in Stock');
		}

		if ($model->has_ported_barrel) {
			$bonus->set_bonus_percent_after(-5, 'Ported Barrel');
		}

		if ($model->has_compensator) {
			$bonus->set_bonus_percent_after(-15, 'Compensator');
		}

		if ($model->has_muzzle_break) {
			$bonus->set_bonus_percent_after(-30, 'Muzzle Break');
		}

		if (!Core_Weapon_Data::has_stock($model)) {
			$bonus->set_bonus_percent(15, 'No Stock');
		}

		if (!Core_Weapon_Data::is_two_handed($model)) {
			$bonus->set_bonus_percent(15, 'One Handed');
		}

		return $bonus;
	}

	/*
	 * RECOIL Y BONUS
	 */

	public static function generate_recoil_y_bonus(Model_Weapon_Group $model) {
		$field = 'bRecoilY';
		if (Bonus::check($field)) {
			return Bonus::instance($field);
		}
		$bonus = Bonus::instance($field);

		if ($model->recoil_y_bonus) {
			$bonus->set_bonus($model->recoil_y_bonus, 'Weapon Bonus');
		}
		if ($model->recoil_y_bonus_percent) {
			$bonus->set_bonus_percent($model->recoil_y_bonus_percent, 'Weapon Bonus');
		}

		/*
		 * Гашение/усиление отдачи от приклада/точки удержания оружия
		 */
		$height_diff_stock_barrel = is_null($model->height_diff_stock_barrel) ? 50 : abs($model->height_diff_stock_barrel);
		if ($height_diff_stock_barrel) {
			$bonus->set_bonus_percent(round($height_diff_stock_barrel / 4), 'Height Diff Stock Barrel');
		}

		$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);
		if ($weight_front_percent) {
			$bonus->set_bonus_percent(floor($weight_front_percent / 1), 'Weight Balance');
		}

		$weight = Core_Weapon_Data::get_weight($model);
		if ($weight) {
			$bonus->set_bonus_percent(-round($weight * 4), 'Weight Bonus');
		}

		self::generate_recoil_mechanism_action_bonus($model, $bonus);

		if ($model->has_heavy_barrel) {
			$bonus->set_bonus_percent_after(-15, 'Heavy Barrel');
		}

		if ($model->has_balanced_automatic) {
			$bonus->set_bonus_percent_after(-20, 'Balanced Automatic');
		}

		if ($model->has_recoil_buffer_in_stock) {
			$bonus->set_bonus_percent_after(-10, 'Recoil Buffer in Stock');
		}

		if ($model->has_ported_barrel) {
			$bonus->set_bonus_percent_after(-15, 'Ported Barrel');
		}

		if ($model->has_compensator) {
			$bonus->set_bonus_percent_after(-15, 'Compensator');
		}

		if ($model->has_muzzle_break) {
			$bonus->set_bonus_percent_after(-30, 'Muzzle Break');
		}

		if (!Core_Weapon_Data::has_stock($model)) {
			$bonus->set_bonus_percent(50, 'No Stock');
		}

		if (!Core_Weapon_Data::is_two_handed($model)) {
			$bonus->set_bonus_percent(50, 'One Handed');
		}

		return $bonus;
	}

} // End Core_Weapon_Calculate_Recoil
