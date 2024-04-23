<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Weapon_Quality
 * User: legion
 * Date: 04.01.2020
 * Time: 1:37
 */
abstract class Weapon_Quality {

	const QL_VERY_HIGH_QUALITY = 'Very_High_Quality';
	const QL_HIGH_QUALITY = 'High_Quality';
	const QL_HIGH_DIRT_RESISTANCE = 'High_Dirt_Resistance';
	const QL_HIGH_SERVICE_LIFE = 'High_Service_Life';

	const QL_LOW_QUALITY = 'Low_Quality';
	const QL_VERY_LOW_QUALITY = 'Very_Low_Quality';
	const QL_LOW_DIRT_RESISTANCE = 'Low_Dirt_Resistance';
	const QL_LOW_SERVICE_LIFE = 'Low_Service_Life';

	private $qualities_list = array();
	/*
	 * COLORS:
	 * - red
	 * - yellow
	 * - green
	 * - cyan
	 * - blue
	 */
	private static $qualities = array(
		/*
		 * Good
		 */
		self::QL_VERY_HIGH_QUALITY => array(
			'color' => 'green',
		),
		self::QL_HIGH_QUALITY => array(
			'color' => 'green',
		),
		self::QL_HIGH_DIRT_RESISTANCE => array(
			'color' => 'green',
		),
		self::QL_HIGH_SERVICE_LIFE => array(
			'color' => 'green',
		),
		/*
		 * Bad
		 */
		self::QL_LOW_QUALITY => array(
			'color' => 'red',
		),
		self::QL_VERY_LOW_QUALITY => array(
			'color' => 'red',
		),
		self::QL_LOW_DIRT_RESISTANCE => array(
			'color' => 'red',
		),
		self::QL_LOW_SERVICE_LIFE => array(
			'color' => 'red',
		),
	);

	/**
	 * @param Jelly_Model $model
	 * @return array
	 */
	public static function get_qualities(Jelly_Model $model) {
		return Helper::get_json_as_array($model, 'weapon_qualities');
	}

	public static function get_quality_labels(Jelly_Model $model) {
		$weapon_qualities = Weapon::get_qualities($model);

		if (empty($weapon_qualities)) {
			return '';
		}

		$integrated_qualities = array();

		foreach ($weapon_qualities as $weapon_quality) {

			$name = Helper::get_bonus_caption($weapon_quality);

			if (array_key_exists($weapon_quality, self::$qualities)) {
				$label = Force_Label::factory($name);
				$params = self::$qualities[$weapon_quality];
				Helper::set_label_color($label, $params);

				$integrated_qualities[] = $label->render();
			} else {
				$integrated_qualities[] = Force_Label::factory($name)
					->render();
			}
		}
		return implode(' ', $integrated_qualities);
	}

	public static function has_quality(Jelly_Model $model, $weapon_quality, $must_have_all = false) {
		$weapon_qualities = Weapon::get_qualities($model);
		if (is_array($weapon_quality)) {
			if ($must_have_all) {
				$count = count($weapon_quality);
				$match = 0;
				foreach ($weapon_quality as $quality) {
					if (array_key_exists($quality, $weapon_qualities)) {
						$match++;
					}
				}
				return ($count == $match);
			} else {
				foreach ($weapon_quality as $quality) {
					if (array_key_exists($quality, $weapon_qualities)) {
						return true;
					}
				}
			}
		} else {
			return array_key_exists($weapon_quality, $weapon_qualities);
		}
		return false;
	}

	/*
	 * INSTANCE INIT
	 */

	protected function init_quality() {
		$this->qualities_list = array();
		foreach (self::$qualities as $quality => $params) {
			$this->qualities_list[$quality] = $quality;
		}
	}

	/*
	 * INSTANCE ONLY
	 */

	/**
	 * @return array
	 */
	public function get_quality_list() {
		return $this->qualities_list;
	}

} // End Weapon_Quality
