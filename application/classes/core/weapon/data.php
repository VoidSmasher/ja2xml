<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Weapon_Data
 * User: legion
 * Date: 05.05.18
 * Time: 6:45
 */
class Core_Weapon_Data extends Core_Common {

	const ACTION_BLOWBACK_SIMPLE = 'Simple Blowback';
	const ACTION_BLOWBACK_DELAYED = 'Delayed Blowback';
	const ACTION_BLOWBACK_DELAYED_ROLLER = 'Roller-delayed Blowback';
	const ACTION_BLOWBACK_DELAYED_LEVER = 'Lever-delayed Blowback';
	const ACTION_BLOWBACK_DELAYED_GAZ = 'Gaz-delayed Blowback';
	const ACTION_BLOWBACK_DELAYED_CHAMBER_RING = 'Chamber-ring delayed Blowback';
	const ACTION_SHORT_RECOIL_OPERATION = 'Short recoil operation';
	const ACTION_LONG_RECOIL_OPERATION = 'Long recoil operation';
	const ACTION_DIRECT_GAZ_IMPINGEMENT = 'Direct Gaz Impingement';
	const ACTION_GAZ_OPERATED = 'Gaz-Operated';
	const ACTION_GAZ_OPERATED_SHORT_STROKE = 'Gaz-Operated, Short-stroke piston';
	const ACTION_GAZ_OPERATED_LONG_STROKE = 'Gaz-Operated, Long-stroke piston';
	const ACTION_GAZ_OPERATED_GUN_CARRIAGE = 'Gaz-Operated, Gun Carriage';
	const ACTION_BOLT = 'Bolt-action';
	const ACTION_PUMP = 'Pump-action';
	const ACTION_BREAK = 'Break-action';
	const ACTION_LEVER = 'Lever-action';
	const ACTION_METAL_STORM = 'Metal Storm';

	const TRIGGER_SINGLE_ACTION = 'Single Action';
	const TRIGGER_DOUBLE_ACTION = 'Double Action';

	const FEATURE_ROTATING_BOLT = 'Rotating bolt';
	const FEATURE_ROTATING_BARREL = 'Rotating barrel';
	const FEATURE_ROTATING_BREECH = 'Rotating breech';
	const FEATURE_ROTARY_FIRING_PIN = 'Rotary firing pin';
	const FEATURE_LOCKED_BREECH = 'Locked breech';
	const FEATURE_ROLLER_LOCKED = 'Roller-locked';
	const FEATURE_TILTING_BOLT = 'Tilting bolt';
	const FEATURE_CLOSED_BOLT = 'Closed bolt';
	const FEATURE_OPENED_BOLT = 'Open bolt';
	const FEATURE_STRAIGHT_PULL_BOLT = 'Straight-pull bolt';
	const FEATURE_STRAIGHT_PULL_GRIP = 'Straight-pull grip';

	const RELOAD_MAGAZINE = 'Magazine';
	const RELOAD_MAGAZINE_PISTOL = 'Magazine Pistol';
	const RELOAD_MAGAZINE_DESERT_EAGLE = 'Magazine Desert Eagle';
	const RELOAD_MAGAZINE_P90 = 'Magazine P90';
	const RELOAD_MAGAZINE_G3 = 'Magazine G3';
	const RELOAD_MAGAZINE_G11 = 'Magazine G11';
	const RELOAD_BELT = 'Belt';
	const RELOAD_BELT_HK21 = 'Belt HK21';
	const RELOAD_REVOLVER = 'Revolver';
	const RELOAD_REVOLVER_PRE_WOUND = 'Revolver Pre-wound';
	const RELOAD_REVOLVER_AUTO_EXTRACT = 'Revolver Auto Extract';
	const RELOAD_TUBE = 'Tube';
	const RELOAD_MANUAL = 'Manual';
	const RELOAD_MANUAL_AUTO_EXTRACT = 'Manual Auto Extract';
	const RELOAD_EN_BLOC_CLIP = 'En Bloc Clip';

	const COMFORT_COMFORTABLE = '1';
	const COMFORT_UNCOMFORTABLE = '-1';

	const RARITY_VERY_COMMON = 0;
	const RARITY_COMMON = 1;
	const RARITY_RARE = 2;
	const RARITY_VERY_RARE = 3;
	const RARITY_EXCLUSIVE = 4;

	use Core_Common_Static;
	use Core_Attachments_Default;
	use Core_Attachments_Possible;
	use Core_Weapon_Rarity;

	protected static $model_class = 'Model_Weapon_Data';
	protected $model_name = 'weapon_data';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function get_mechanism_action_list() {
		return array(
			self::ACTION_BLOWBACK_SIMPLE => self::ACTION_BLOWBACK_SIMPLE,
			self::ACTION_BLOWBACK_DELAYED => self::ACTION_BLOWBACK_DELAYED,
			self::ACTION_BLOWBACK_DELAYED_ROLLER => self::ACTION_BLOWBACK_DELAYED_ROLLER,
			self::ACTION_BLOWBACK_DELAYED_LEVER => self::ACTION_BLOWBACK_DELAYED_LEVER,
			self::ACTION_BLOWBACK_DELAYED_GAZ => self::ACTION_BLOWBACK_DELAYED_GAZ,
			self::ACTION_BLOWBACK_DELAYED_CHAMBER_RING => self::ACTION_BLOWBACK_DELAYED_CHAMBER_RING,
			self::ACTION_SHORT_RECOIL_OPERATION => self::ACTION_SHORT_RECOIL_OPERATION,
			self::ACTION_LONG_RECOIL_OPERATION => self::ACTION_LONG_RECOIL_OPERATION,
			self::ACTION_DIRECT_GAZ_IMPINGEMENT => self::ACTION_DIRECT_GAZ_IMPINGEMENT,
			self::ACTION_GAZ_OPERATED => self::ACTION_GAZ_OPERATED,
			self::ACTION_GAZ_OPERATED_SHORT_STROKE => self::ACTION_GAZ_OPERATED_SHORT_STROKE,
			self::ACTION_GAZ_OPERATED_LONG_STROKE => self::ACTION_GAZ_OPERATED_LONG_STROKE,
			self::ACTION_GAZ_OPERATED_GUN_CARRIAGE => self::ACTION_GAZ_OPERATED_GUN_CARRIAGE,
			self::ACTION_BOLT => self::ACTION_BOLT,
			self::ACTION_PUMP => self::ACTION_PUMP,
			self::ACTION_BREAK => self::ACTION_BREAK,
			self::ACTION_LEVER => self::ACTION_LEVER,
			self::ACTION_METAL_STORM => self::ACTION_METAL_STORM,
		);
	}

	public static function get_mechanism_trigger_list() {
		return array(
			self::TRIGGER_SINGLE_ACTION => self::TRIGGER_SINGLE_ACTION,
			self::TRIGGER_DOUBLE_ACTION => self::TRIGGER_DOUBLE_ACTION,
		);
	}

	// Механизмы запирания
	public static function get_mechanism_feature_list() {
		return array(
			self::FEATURE_ROTATING_BOLT => self::FEATURE_ROTATING_BOLT,
			self::FEATURE_ROTATING_BARREL => self::FEATURE_ROTATING_BARREL,
			self::FEATURE_ROTATING_BREECH => self::FEATURE_ROTATING_BREECH,
			self::FEATURE_ROTARY_FIRING_PIN => self::FEATURE_ROTARY_FIRING_PIN,
			self::FEATURE_LOCKED_BREECH => self::FEATURE_LOCKED_BREECH,
			self::FEATURE_ROLLER_LOCKED => self::FEATURE_ROLLER_LOCKED,
			self::FEATURE_TILTING_BOLT => self::FEATURE_TILTING_BOLT,
			self::FEATURE_CLOSED_BOLT => self::FEATURE_CLOSED_BOLT,
			self::FEATURE_OPENED_BOLT => self::FEATURE_OPENED_BOLT,
			self::FEATURE_STRAIGHT_PULL_BOLT => self::FEATURE_STRAIGHT_PULL_BOLT,
			self::FEATURE_STRAIGHT_PULL_GRIP => self::FEATURE_STRAIGHT_PULL_GRIP,
		);
	}

	public static function get_mechanism_reload_list() {
		return array(
			self::RELOAD_MAGAZINE => self::RELOAD_MAGAZINE,
			self::RELOAD_MAGAZINE_PISTOL => self::RELOAD_MAGAZINE_PISTOL,
			self::RELOAD_MAGAZINE_DESERT_EAGLE => self::RELOAD_MAGAZINE_DESERT_EAGLE,
			self::RELOAD_MAGAZINE_P90 => self::RELOAD_MAGAZINE_P90,
			self::RELOAD_MAGAZINE_G3 => self::RELOAD_MAGAZINE_G3,
			self::RELOAD_MAGAZINE_G11 => self::RELOAD_MAGAZINE_G11,
			self::RELOAD_BELT => self::RELOAD_BELT,
			self::RELOAD_BELT_HK21 => self::RELOAD_BELT_HK21,
			self::RELOAD_REVOLVER => self::RELOAD_REVOLVER,
			self::RELOAD_REVOLVER_PRE_WOUND => self::RELOAD_REVOLVER_PRE_WOUND,
			self::RELOAD_REVOLVER_AUTO_EXTRACT => self::RELOAD_REVOLVER_AUTO_EXTRACT,
			self::RELOAD_TUBE => self::RELOAD_TUBE,
			self::RELOAD_MANUAL => self::RELOAD_MANUAL,
			self::RELOAD_MANUAL_AUTO_EXTRACT => self::RELOAD_MANUAL_AUTO_EXTRACT,
			self::RELOAD_EN_BLOC_CLIP => self::RELOAD_EN_BLOC_CLIP,
		);
	}

	public static function get_comfort_list() {
		return array(
			self::COMFORT_COMFORTABLE => 'Comfortable',
			self::COMFORT_UNCOMFORTABLE => 'Uncomfortable',
		);
	}

	public static function has_stock(Model_Weapon_Group $model) {
		$has_stock = (!in_array($model->integrated_stock_index, [
			Core_Attachment_Data::INDEX_STOCK_NONE,
			Core_Attachment_Data::INDEX_STOCK_PISTOL,
		]));

		return $has_stock;
	}

	public static function is_two_handed(Model_Weapon_Group $model) {
		return !is_null($model->is_two_handed) ? $model->is_two_handed : $model->TwoHanded;
	}


	/*
	 * RELIABILITY
	 */

	public static function get_reliability(Jelly_Model $model) {
		if ($model->item_bReliability) {
			$reliability = $model->item_bReliability;
		} else {
			$reliability = $model->bReliability;
		}
		return $reliability;
	}

	/*
	 * CALIBRE
	 */

	public static function get_calibre(Model_Weapon_Group $model) {
		if ($model->calibre) {
			$mag_size = $model->calibre;
		} else {
			$mag_size = $model->ubCalibre;
		}
		return $mag_size;
	}

	/*
	 * MAG SIZE
	 */

	public static function get_mag_size(Model_Weapon_Group $model) {
		if ($model->mag_size) {
			$mag_size = $model->mag_size;
		} else {
			$mag_size = $model->ubMagSize;
		}
		return $mag_size;
	}

	/*
	 * WEIGHT
	 */

	public static function get_weight(Model_Weapon_Group $model) {
		if ($model->calc_weight !== 'undefined') {
			return $model->calc_weight;
		}

		if ($model->weight_empty > 0) {
			$weight = $model->weight_empty;
		} elseif ($model->weight > 0) {
			$weight = $model->weight;
		} else {
			$weight = $model->ubWeight / 10;
		}

		$model->calc_weight = $weight;

		return $model->calc_weight;
	}

	public static function get_weight_front_percent(Model_Weapon_Group $model) {
		if (!empty($model->weight_front_percent)) {
			return $model->weight_front_percent;
		}

		if ($model->calc_weight_front_percent !== 'undefined') {
			return $model->calc_weight_front_percent;
		}

		$length_front = Core_Weapon_Data::get_length_front($model);

		if ($length_front > 0) {
			$weight_front_percent = round($length_front * 100 / $model->length_max);
		} else {
			$weight_front_percent = 50;
		}

		$has_stock = Core_Weapon_Data::has_stock($model);

		if ($has_stock) {
			/*
			 * Изменение рассчётного баланса.
			 * Лёгкие приклады утяжелают переднюю часть оружия.
			 * Буллпапы наоборот облегчают переднюю часть оружия.
			 */
			switch ($model->integrated_stock_index) {
				case Core_Attachment_Data::INDEX_STOCK_FOLDING_SIMPLE:
				case Core_Attachment_Data::INDEX_STOCK_RETRACTABLE_SIMPLE:
					$weight_front_percent += 15;
					break;
				case Core_Attachment_Data::INDEX_STOCK_BULLPUP:
					$weight_front_percent -= 15;
					break;
				default:
					$weight_front_percent += 10;
					break;
			}
		}

		if ($model->integrated_foregrip_index) {
			switch ($model->integrated_foregrip_index) {
				case Core_Attachment_Data::INDEX_FOREGRIP_THIN:
				case Core_Attachment_Data::INDEX_FOREGRIP_MAG:
					break;
				default:
					$weight_front_percent += 5;
			}
		}

		if ($model->has_heavy_barrel) {
			$weight_front_percent += 5;
		}

		if ($model->has_drum_mag) {
			$weight_front_percent += 5;
		}

		if ($model->has_calico_mag) {
			$weight_front_percent -= 20;
		}

		if ($weight_front_percent > 95) {
			$weight_front_percent = 95;
		}

		$model->calc_weight_front_percent = $weight_front_percent;

		return $model->calc_weight_front_percent;
	}

	/*
	 * LENGTH
	 */

	public static function get_length_front(Jelly_Model $model) {
		/*
		 * Длина удерживаемая рукой 100 мм, 80 мм вперёд и 20 мм назад
		 * Центр опоры по середине
		 */
//		$length_handle = 80;

		$length_front = round($model->length_front_to_trigger);

		return $length_front;
	}

	public static function get_length_front_percent(Jelly_Model $model) {
		$length_front = self::get_length_front($model);

		if ($model->length_min > 0) {
			$length = $model->length_min;
		} else {
			$length = $model->length_max;
		}

		if ($length > 0) {
			$length_front_percent = ($length_front * 100) / $length;
		} else {
			$length_front_percent = 0;
		}

		return $length_front_percent;
	}

	public static function get_height_diff_stock_barrel(Jelly_Model $model) {
		$height_diff_stock_barrel = is_null($model->height_diff_stock_barrel) ? 50 : abs($model->height_diff_stock_barrel);

		return $height_diff_stock_barrel;
	}

	/*
	 * MOA
	 */

	public static function calculate_moa($range, $diameter_x, $diameter_y) {
		if (empty($range)) {
			return NULL;
		}

		$diameter = $diameter_x + $diameter_y;
		if ($diameter <= 0) {
			return NULL;
		}

		// берём средний разброс между вертикалью и горизонталью
		$diameter = $diameter / 2;

		//переводим миллиметры в метры
		$diameter = $diameter / 1000;

		$rad = atan($diameter / (2 * $range));

		$grad = ($rad * 180) / M_PI;

		$moa = 2 * $grad * 60;

//		1 MOA
//		100 yard :: 1.047 inch
//		91.44 m :: 26.5938 mm

		return round($moa, 2);
	}

	public static function get_diameter($range, $moa) {
		if (empty($range)) {
			return NULL;
		}
		if (empty($moa)) {
			return NULL;
		}

//		1 MOA
//		100 yard :: 1.047 inch
//		91.44 m :: 26.5938 mm

		// 1 градус = 60 минут

		// переводим угловые минуты в градусы
		$grad = $moa / 60;

		// 1 градус в радианах
//		$rad = (2 * pi()) / 360;

		// переводим градусы в радианы
		$rad = (M_PI * $grad) / 180;

		$diameter = 2 * $range * tan($rad / 2);

		// переводим метры в миллиметры
		$diameter = $diameter * 1000;

		return round($diameter);
	}

	public static function get_fire_rate_semi_auto($sp4t, $aptrm) {
		$fire_rate = round($sp4t * (60 - $aptrm) / 30);
		return $fire_rate;
	}

	public static function calculate_br_rof(Model_Weapon_Group $model) {
		if (empty($model->length_barrel)) {
			return empty($model->BR_ROF) ? NULL : $model->BR_ROF;
		}

		if ($model->calc_br_rof !== 'undefined') {
			return $model->calc_br_rof;
		}

		$sp4t = Core_Weapon::calculate_sp4t($model);
		$aptrm = Core_Weapon::calculate_aptrm($model);

		$fire_rate_auto = Core_Weapon_Data::get_fire_rate_auto($model);
		$fire_rate_burst = $model->fire_rate_burst;

		if (empty($fire_rate_auto) && !empty($model->ubShotsPerBurst) && !empty($fire_rate_burst) && $fire_rate_burst < 5000) {
			$fire_rate_auto = $fire_rate_burst;
		}

		if (empty($fire_rate_auto)) {
			$fire_rate_auto = Core_Weapon_Data::get_fire_rate_semi_auto($sp4t, $aptrm);
		}

		return $model->calc_br_rof = $fire_rate_auto;
	}

	public static function get_fire_rate(Jelly_Model $model, $check_burst_length = false) {
		$fire_rate_auto = Core_Weapon_Data::get_fire_rate_auto($model);

		if ($check_burst_length) {
			if ($model->burst_length < 1) {
				return $fire_rate_auto;
			}
		}

		$fire_rate_burst = $model->fire_rate_burst;

		if (empty($fire_rate_auto) && !empty($fire_rate_burst)) {
			$fire_rate_auto = $fire_rate_burst;
		}

		return $fire_rate_auto;
	}

	public static function get_fire_rate_burst(Jelly_Model $model, $check_burst_length = false) {
		if ($check_burst_length) {
			if ($model->burst_length < 1) {
				return 0;
			}
		}

		$fire_rate_auto = Core_Weapon_Data::get_fire_rate_auto($model);

		$fire_rate_burst = $model->fire_rate_burst;

		if (empty($fire_rate_burst) && !empty($fire_rate_auto)) {
			$fire_rate_burst = $fire_rate_auto;
		}

		return $fire_rate_burst;
	}

	public static function get_fire_rate_auto(Jelly_Model $model) {
		$fire_rate = $model->fire_rate_auto_max;

		if ($model->fire_rate_auto_min) {
			$fire_rate = round(($model->fire_rate_auto_min + $model->fire_rate_auto_max) / 2);
		}

		return $fire_rate;
	}

	/*
	 * Data List
	 */

	public static function get_weapons_builder($items_mod = true, $weapons_mod = true) {
		if ($items_mod) {
			$table_item = 'items_mod';
		} else {
			$table_item = 'items';
		}

		if ($weapons_mod) {
			$table_weapon = 'weapons_mod';
		} else {
			$table_weapon = 'weapons';
		}

		$builder = Core_Weapon_Data::factory()->preset_for_admin()->get_builder()
			->join($table_weapon, 'LEFT')->on($table_weapon . '.uiIndex', '=', 'data_weapons.uiIndex')
			->join($table_item, 'LEFT')->on($table_item . '.uiIndex', '=', 'data_weapons.uiIndex')
			->join('calibres', 'LEFT')->on('calibres.ubCalibre', '=', 'weapons_mod.ubCalibre')
//			->where('weapons_mod.ubWeaponClass', 'IN', [
//				Core_Weapon::CLASS_HANDGUN,
//				Core_Weapon::CLASS_SMG,
//				Core_Weapon::CLASS_RIFLE,
//				Core_Weapon::CLASS_MACHINEGUN,
//				Core_Weapon::CLASS_SHOTGUN,
//			])
//			->where('weapons_mod.ubWeaponType', '!=', Core_Weapon::TYPE_BLANK)
//			->where('weapons_mod.ubWeaponType', 'IS NOT', NULL)
//			->where('weapons_mod.ubCalibre', '>', 0)
			->select_column('data_weapons.*')
			->select_column($table_item . '.szItemName', 'szItemName')
			->select_column($table_item . '.szLongItemName', 'szLongItemName')
			->select_column($table_item . '.szBRName', 'szBRName')
			->select_column($table_item . '.DefaultAttachment', 'DefaultAttachment')
			->select_column($table_item . '.TwoHanded', 'TwoHanded')
			->select_column($table_weapon . '.szWeaponName', 'szWeaponName')
			->select_column($table_weapon . '.ubWeaponClass', 'ubWeaponClass')
			->select_column($table_weapon . '.ubWeaponType', 'ubWeaponType')
			->select_column($table_weapon . '.ubCalibre', 'ubCalibre')
			->select_column($table_weapon . '.ubShotsPerBurst', 'ubShotsPerBurst')
			->select_column($table_weapon . '.usRange', 'usRange');

		Core_Weapon_Mod::update_weapons_builder_with_calibres($builder);

		return $builder;
	}

} // End Core_Weapon_Data
