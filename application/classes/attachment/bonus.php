<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Attachment_Bonus
 * User: legion
 * Date: 04.01.2020
 * Time: 23:05
 */
abstract class Attachment_Bonus extends Attachment_Type {

	const BONUS_SCOPE_ADJUSTABLE_WINDAGE = 'Scope_Adjustable_Windage';
	const BONUS_SCOPE_ADJUSTABLE_ELEVATION = 'Scope_Adjustable_Elevation';
	const BONUS_SCOPE_FAST_TARGETING = 'Scope_Fast_Targeting';
	const BONUS_SCOPE_GRID_RANGEFINDER_SIMPLE = 'Scope_Grid_Rangefinder_Simple';
	const BONUS_SCOPE_GRID_RANGEFINDER_ADVANCED = 'Scope_Grid_Rangefinder_Advanced';
	const BONUS_SCOPE_GRID_DOUGHNUT = 'Scope_Doughnut';
	const BONUS_SCOPE_GRID_GLOW = 'Scope_Grid_Glow';
	const BONUS_SCOPE_DOT_GLOW = 'Scope_Dot_Glow';
	const BONUS_SCOPE_NIGHT_VISION = 'Scope_Night_Vision';
	const BONUS_SCOPE_IR_VISION = 'Scope_IR_Vision';
	const BONUS_SCOPE_TIGHT_FOV = 'Scope_Tight_FOV';
	const BONUS_SCOPE_WIDE_FOV = 'Scope_Wide_FOV';
	const BONUS_SCOPE_SEE_THROUGH = 'Scope_See_Through';
	const BONUS_SCOPE_HIGH_PROFILE = 'Scope_High_Profile';
	const BONUS_SCOPE_LOW_PROFILE = 'Scope_Low_Profile';
	const BONUS_SCOPE_OFFSET_AXIS = 'Scope_Offset_Axis';

	const BONUS_SIGHT_COLLIMATOR_LARGE = 'Sight_Collimator_Large';
	const BONUS_SIGHT_COLLIMATOR_SMALL = 'Sight_Collimator_Small';
	const BONUS_SIGHT_MATCH = 'Sight_Match';
	const BONUS_SIGHT_DOT_GLOW = 'Sight_Dot_Glow';
	const BONUS_SIGHT_NIGHT_VISION = 'Sight_Night_Vision';
	const BONUS_SIGHT_IR_VISION = 'Sight_IR_Vision';
	const BONUS_SIGHT_HIGH_PROFILE = 'Sight_High_Profile';
	const BONUS_SIGHT_LOW_PROFILE = 'Sight_Low_Profile';

	const BONUS_FLASHLIGHT_IR = 'Flashlight_IR';
	const BONUS_FLASHLIGHT_ON = 'Flashlight_ON';
	const BONUS_FLASHLIGHT_OFF = 'Flashlight_OFF';

	const BONUS_LASER_IR = 'Laser_IR';
	const BONUS_LASER_ON = 'Laser_ON';
	const BONUS_LASER_OFF = 'Laser_OFF';

	const BONUS_STOCK_SHOOT_MINIMIZED = 'Stock_Shoot_Minimized';
	const BONUS_STOCK_ADJUSTABLE = 'Stock_Adjustable';
	const BONUS_STOCK_SIMPLE = 'Stock_Simple';
	const BONUS_STOCK_SOLID = 'Stock_Solid';
	const BONUS_STOCK_LONG = 'Stock_Long';
	const BONUS_STOCK_SHORT = 'Stock_Short';

	const BONUS_FOREGRIP_ACCURACY = 'Foregrip_Accuracy';
	const BONUS_FOREGRIP_HANDLING = 'Foregrip_Handling';
	const BONUS_FOREGRIP_STABILITY = 'Foregrip_Stability';
	const BONUS_FOREGRIP_COUNTER_FORCE = 'Foregrip_Counter_Force';
	const BONUS_FOREGRIP_POSITIVE_ANGLED = 'Foregrip_Positive_Angled';
	const BONUS_FOREGRIP_NEGATIVE_ANGLED = 'Foregrip_Negative_Angled';
	const BONUS_FOREGRIP_POD = 'Foregrip_Pod';
	const BONUS_FOREGRIP_MAG = 'Foregrip_Mag';

	const BONUS_BIPOD_LIGHT = 'Bipod_Light';
	const BONUS_BIPOD_LONG = 'Bipod_Long';

	const BONUS_UBW_SMALL_FOREGRIP = 'UBW_Small_Foregrip';

	const BONUS_MUZZLE_WEIGHT = 'Muzzle_Weight';
	const BONUS_MUZZLE_BARREL = 'Muzzle_Barrel';
	const BONUS_MUZZLE_LONG = 'Muzzle_Long';

	private $bonus_list = array();
	private $bonus_list_by_type = array();
	/*
	 * COLORS:
	 * - red
	 * - yellow
	 * - green
	 * - cyan
	 * - blue
	 */
	private static $bonuses = array(
		self::BONUS_SCOPE_ADJUSTABLE_WINDAGE => array(
			'color' => 'cyan',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_ADJUSTABLE_ELEVATION => array(
			'color' => 'cyan',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_FAST_TARGETING => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_GRID_RANGEFINDER_SIMPLE => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_GRID_RANGEFINDER_ADVANCED => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_GRID_DOUGHNUT => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_GRID_GLOW => array(
			'color' => 'green',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_DOT_GLOW => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_NIGHT_VISION => array(
			'color' => 'green',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_IR_VISION => array(
			'color' => 'red',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_TIGHT_FOV => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_WIDE_FOV => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_SEE_THROUGH => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_HIGH_PROFILE => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_LOW_PROFILE => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SCOPE,
		),
		self::BONUS_SCOPE_OFFSET_AXIS => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SCOPE,
		),

		self::BONUS_SIGHT_COLLIMATOR_LARGE => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_COLLIMATOR_SMALL => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_MATCH => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_DOT_GLOW => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_NIGHT_VISION => array(
			'color' => 'green',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_IR_VISION => array(
			'color' => 'red',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_HIGH_PROFILE => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_SIGHT,
		),
		self::BONUS_SIGHT_LOW_PROFILE => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_SIGHT,
		),

		self::BONUS_FLASHLIGHT_IR => array(
			'color' => 'red',
			'type' => Attachment::TYPE_FLASHLIGHT,
		),
		self::BONUS_FLASHLIGHT_ON => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_FLASHLIGHT,
		),
		self::BONUS_FLASHLIGHT_OFF => array(
			'color' => 'gray',
			'type' => Attachment::TYPE_FLASHLIGHT,
		),

		self::BONUS_LASER_IR => array(
			'color' => 'red',
			'type' => Attachment::TYPE_LASER,
		),
		self::BONUS_LASER_ON => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_LASER,
		),
		self::BONUS_LASER_OFF => array(
			'color' => 'gray',
			'type' => Attachment::TYPE_LASER,
		),

		self::BONUS_STOCK_SHOOT_MINIMIZED => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_STOCK,
		),
		self::BONUS_STOCK_ADJUSTABLE => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_STOCK,
		),
		self::BONUS_STOCK_SIMPLE => array(
			'color' => 'red',
			'type' => Attachment::TYPE_STOCK,
		),
		self::BONUS_STOCK_SOLID => array(
			'color' => 'cyan',
			'type' => Attachment::TYPE_STOCK,
		),
		self::BONUS_STOCK_LONG => array(
			'color' => 'green',
			'type' => Attachment::TYPE_STOCK,
		),
		self::BONUS_STOCK_SHORT => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_STOCK,
		),

		self::BONUS_FOREGRIP_ACCURACY => array(
			'color' => 'cyan',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_HANDLING => array(
			'color' => 'green',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_STABILITY => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_COUNTER_FORCE => array(
			'color' => 'red',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_POSITIVE_ANGLED => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_NEGATIVE_ANGLED => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_POD => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_FOREGRIP_MAG => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_FOREGRIP,
		),
		self::BONUS_UBW_SMALL_FOREGRIP => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_UNDER_BARREL_WEAPON,
		),

		self::BONUS_BIPOD_LIGHT => array(
			'color' => 'green',
			'type' => Attachment::TYPE_BIPOD,
		),
		self::BONUS_BIPOD_LONG => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_BIPOD,
		),

		self::BONUS_MUZZLE_WEIGHT => array(
			'color' => 'green',
			'type' => Attachment::TYPE_MUZZLE,
		),
		self::BONUS_MUZZLE_BARREL => array(
			'color' => 'blue',
			'type' => Attachment::TYPE_MUZZLE,
		),
		self::BONUS_MUZZLE_LONG => array(
			'color' => 'yellow',
			'type' => Attachment::TYPE_MUZZLE,
		),
	);

	use Attachment_Bonus_Targeting;
	use Attachment_Bonus_Handling;
	use Attachment_Bonus_Stock;
	use Attachment_Bonus_Secondary;
	use Attachment_Bonus_Knife;
	use Attachment_Bonus_Muzzle;
	use Attachment_Bonus_Mag;

	public static function calculate_bonuses(Jelly_Model $model) {
		self::calculate_targeting_bonuses($model);
		self::calculate_stock_bonuses($model);
		self::calculate_handling_bonuses($model);
		self::calculate_secondary_weapon_bonuses($model);
		self::calculate_knife_bonuses($model);
		self::calculate_muzzle_bonuses($model);
		self::calculate_mag_adapter_bonuses($model);
	}

	public static function get_bonuses(Jelly_Model $model) {
		return Helper::get_json_as_array($model, 'attachment_bonuses');
	}

	public static function get_bonus_label($field) {
		$caption = Helper::get_bonus_caption($field);
		$label = Force_Label::factory($caption);

		$params = Arr::get(self::$bonuses, $field, array());
		Helper::set_label_color($label, $params);

		return $label->render();
	}

	/**
	 * @param Jelly_Model $model
	 * @param $attachment_bonus
	 * @param bool $must_have_all
	 * @return bool
	 */
	public static function has_bonus(Jelly_Model $model, $attachment_bonus, $must_have_all = false) {
		$attachment_bonuses = Attachment::get_bonuses($model);
		if (is_array($attachment_bonus)) {
			if ($must_have_all) {
				$count = count($attachment_bonus);
				$match = 0;
				foreach ($attachment_bonus as $bonus) {
					if (array_key_exists($bonus, $attachment_bonuses)) {
						$match++;
					}
				}
				return ($count == $match);
			} else {
				foreach ($attachment_bonus as $bonus) {
					if (array_key_exists($bonus, $attachment_bonuses)) {
						return true;
					}
				}
			}
		} else {
			return array_key_exists($attachment_bonus, $attachment_bonuses);
		}
		return false;
	}

	/*
	 * INSTANCE INIT
	 */

	protected function init_bonus() {
		$this->bonus_list = array();
		foreach (self::$bonuses as $bonus => $params) {
			$this->bonus_list[$bonus] = $bonus;
		}

		$this->bonus_list_by_type = array();
		foreach (self::$bonuses as $bonus => $params) {
			$type = Arr::get($params, 'type');
			$this->bonus_list_by_type[$type][$bonus] = $bonus;
		}
	}

	/*
	 * INSTANCE ONLY
	 */

	/**
	 * @return array
	 */
	public function get_bonus_list() {
		return $this->bonus_list;
	}

	public function get_bonus_list_by_model(Jelly_Model $model) {
		$attachment_types = Attachment::get_types($model);

		if (!is_array($attachment_types)) {
			$attachment_types = array();
		}

		$list = array();

		foreach ($attachment_types as $attachment_type) {
			$list = $list + Attachment::get_bonus_list_by_attachment_type($attachment_type);
		}

		return $list;
	}

	/**
	 * @param $attachment_type
	 * @return array
	 */
	public function get_bonus_list_by_attachment_type($attachment_type) {
		return Arr::get($this->bonus_list_by_type, $attachment_type, array());
	}

} // End Attachment_Bonus
