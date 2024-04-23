<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Calibre
 * User: legion
 * Date: 05.05.18
 * Time: 8:20
 */
class Core_Calibre extends Core_Calibre_Type {
//class Core_Calibre extends Core_Common {

	const CALIBRE_9_19 = 2;
	const CALIBRE_545_39 = 7;
	const CALIBRE_556_45 = 8;
	const CALIBRE_556_45_SCF = 43;
	const CALIBRE_762_39 = 10;

	use Core_Common_Static;

	protected static $mag_sizes = array();

	protected static $model_class = 'Model_Calibre';
	protected $model_name = 'calibre';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function calculate_coolness(Jelly_Model $model, $damage) {
		$value = 0;

		$bonus = Bonus::instance('coolness');

		$bonus->set_bonus(round(($damage / 7) - 2), 'Damage');

		switch ($model->bullet_type) {
			case Core_Calibre::TYPE_PISTOL:
				$bonus->set_bonus(-1, 'Pistol Ammo');
				break;
			case Core_Calibre::TYPE_SNIPER:
				$bonus->set_bonus(+2, 'Sniper Ammo');
				break;
		}

		$bonus->apply($value);

		if ($value < 0) {
			$value = 0;
		}

		if ($value > 8) {
			$value = 8;
		}

		return $value;
	}

	/*
	 * BULLET SPEED
	 */

	public static function calculate_bullet_speed(Model_Calibre $model, $barrel = 400) {
		// Рассчёты выполняются только для пуль
		if ($model->test_barrel_length < 1) {
			return $model->bullet_start_speed;
		}

		if (isset($model->calc_bullet_speed[$barrel])) {
			return $model->calc_bullet_speed[$barrel];
		}

		$multiplier = $model->bullet_start_speed / pow($model->test_barrel_length, $model->velocity_mult);

		$bullet_start_speed = pow($barrel, $model->velocity_mult) * $multiplier;

		return $model->calc_bullet_speed[$barrel] = round($bullet_start_speed);
	}

	/*
	 * ENERGY
	 */

	public static function calculate_bullet_energy(Model_Calibre $model, $bullet_speed) {
		// Рассчёты выполняются только для пуль
		if ($model->test_barrel_length < 1) {
			return NULL;
		}

		if ($bullet_speed < 1) {
			return NULL;
		}

		if (isset($model->calc_energy[$bullet_speed])) {
			return $model->calc_energy[$bullet_speed];
		}

		$bullet_weight = $model->bullet_weight;

		$energy = Core_Bullet::get_bullet_energy($bullet_speed, $bullet_weight);

		return $model->calc_energy[$bullet_speed] = $energy;
	}

	/*
	 * DAMAGE
	 */

	public static function calculate_damage(Model_Calibre $model, $energy) {
		if ($model->test_barrel_length < 1) {
			return $model->damage;
		}

		if ($model->bullet_start_energy < 1) {
			return $model->damage;
		}

//		$damage = pow($energy, 0.255) * 8;
//		$damage = pow($energy, 0.230) * 7 - 7;
//		$damage = pow($energy, 0.235) * 7 - 8;
		$damage = pow($energy, 0.235) * 5.5 + 1.1;

		if ($model->bullet_type == Core_Calibre::TYPE_SHOTGUN) {
			$damage *= 1.08;
		}

		$damage = round($damage, 2);

		return $damage;
	}

	/*
	 * SEMI SPEED
	 */

	public static function calculate_semi_speed(Jelly_Model $model, $auto_recoil) {
//		return (96 - 53) / pow($auto_recoil, 0.53);
		switch ($model->bullet_type) {
			case Core_Calibre::TYPE_PISTOL:
			case Core_Calibre::TYPE_PISTOL_LONG:
//				$semi_speed = 96 - pow($auto_recoil, 0.6) * 4.6;
//				$semi_speed = 96 - pow($auto_recoil, 0.7) * 3;
				$semi_speed = 96 - pow($auto_recoil, 0.53) * 6.0866;
				break;
			case Core_Calibre::TYPE_SHOTGUN:
			case Core_Calibre::TYPE_RIFLE:
			case Core_Calibre::TYPE_RIFLE_ADVANCED:
			case Core_Calibre::TYPE_SNIPER:
				$semi_speed = 96 - pow($auto_recoil, 0.53) * 6.0866;
				break;
			default:
				return $model->semi_speed;
		}

		$semi_speed = number_format($semi_speed, 2);

		return $semi_speed;
	}

	/*
	 * BURST RECOIL
	 */

	public static function calculate_burst_recoil(Jelly_Model $model, $damage) {
//		$auto_recoil = pow($damage, 0.64) * 5.1;
//		$auto_recoil = pow($damage, 0.5);
//		return pow($auto_recoil, 0.5) / 49;
		switch ($model->bullet_type) {
			case Core_Calibre::TYPE_PISTOL:
			case Core_Calibre::TYPE_PISTOL_LONG:
//				$auto_recoil = pow($damage, 1.15) * 1.02;
//				$burst_recoil = pow($auto_recoil, 1.2) / 2.1867 + 4;
				$burst_recoil = 48;
				break;
			case Core_Calibre::TYPE_SHOTGUN:
			case Core_Calibre::TYPE_RIFLE:
			case Core_Calibre::TYPE_RIFLE_ADVANCED:
			case Core_Calibre::TYPE_SNIPER:
//				$auto_recoil = pow($damage, 0.5);
//				$burst_recoil = pow($auto_recoil, 0.5) / 0.0482;
				$burst_recoil = 48;
				break;
			default:
				$burst_recoil = 48;
//				return $model->semi_speed;
		}

		$burst_recoil = round($burst_recoil);

		return $burst_recoil;
	}

	/*
	 * AUTO RECOIL
	 */

	public static function calculate_auto_recoil(Jelly_Model $model, $damage) {
//		return 48 / pow($damage, 0.9);
		switch ($model->bullet_type) {
			case Core_Calibre::TYPE_PISTOL:
			case Core_Calibre::TYPE_PISTOL_LONG:
//				$auto_recoil = pow($damage, 1.15) * 1.02 + 1;
				$auto_recoil = atan($damage / 5 - 8) * 25 + 82;
				break;
			case Core_Calibre::TYPE_SHOTGUN:
			case Core_Calibre::TYPE_RIFLE:
			case Core_Calibre::TYPE_RIFLE_ADVANCED:
			case Core_Calibre::TYPE_SNIPER:
//				$auto_recoil = pow($damage, 0.64) * 5.1;
				$auto_recoil = atan($damage / 5 - 8) * 25 + 73;
				break;
			default:
				return $model->auto_recoil;
		}

		if ($auto_recoil < 0) {
			$auto_recoil = 0;
		}

		$auto_recoil = round($auto_recoil);

		return $auto_recoil;
	}

	/**
	 * @return Force_Filter_Select
	 */
	public static function get_filter_control() {
		$session = Session::instance();
		$calibre = $session->get('calibre');

		$calibres = Core_Calibre::factory()->get_list()->as_array('ubCalibre', 'name');

		$filter_control = Force_Filter_Select::factory('calibre', 'Калибр', $calibres);

		if ($calibre) {
			$filter_control->default_value($calibre);
		}

		return $filter_control;
	}

	public static function check_filter_control(Force_Filter $filter) {
		$session = Session::instance();
		$calibre = $session->get('calibre');

		$filter_control = $filter->get_control('calibre');
		if ($filter_control instanceof Force_Filter_Select) {
			$calibre = $filter_control->get_value();
		}

		$calibre = intval($calibre);

		if (empty($calibre)) {
			$session->delete('calibre');
		} else {
			$session->set('calibre', $calibre);
		}

		return $calibre;
	}

	public static function get_mag_size_list($ubCalibre) {
		if (empty(self::$mag_sizes)) {
			$mag_size_list = Core_Magazine::factory()->get_builder()
				->where('ubMagType', '!=', 3)
				->order_by('ubMagSize')
				->select_all();

			foreach ($mag_size_list as $model) {
				self::$mag_sizes[$model->ubCalibre][$model->ubMagSize] = $model->ubMagSize;
			}
		}

		if (array_key_exists($ubCalibre, self::$mag_sizes)) {
			return self::$mag_sizes[$ubCalibre];
		} else {
			return array();
		}
	}

} // End Core_Calibre
