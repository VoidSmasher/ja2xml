<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Calibre_Type
 * User: legion
 * Date: 18.01.2020
 * Time: 22:34
 */
abstract class Core_Calibre_Type extends Core_Common {

	const TYPE_PISTOL = 'PISTOL';
	const TYPE_PISTOL_LONG = 'PISTOL-LONG';
	const TYPE_SHOTGUN = 'SHOTGUN';
	const TYPE_RIFLE = 'RIFLE';
	const TYPE_RIFLE_ADVANCED = 'RIFLE-ADVANCED';
	const TYPE_SNIPER = 'SNIPER';
	const TYPE_DART = 'DART';
	const TYPE_ROCKET = 'ROCKET';
	const TYPE_GRENADE = 'GRENADE';
	const TYPE_MORTAR = 'MORTAR';
	const TYPE_FLAMER = 'FLAMER';
	const TYPE_GAZ = 'GAZ';

	protected static $types_list;
	protected static $types_menu;
	/*
	 * COLORS:
	 * - red
	 * - yellow
	 * - green
	 * - cyan
	 * - blue
	 */
	protected static $types = [
		self::TYPE_PISTOL => [
			'color' => 'yellow',
		],
		self::TYPE_PISTOL_LONG => [
			'color' => 'yellow',
		],
		self::TYPE_SHOTGUN => [
			'color' => 'green',
		],
		self::TYPE_RIFLE => [
			'color' => 'blue',
		],
		self::TYPE_RIFLE_ADVANCED => [
			'color' => 'cyan',
		],
		self::TYPE_SNIPER => [
			'color' => 'cyan',
		],
		self::TYPE_DART => [
			'color' => 'gray',
		],
		self::TYPE_ROCKET => [
			'color' => 'blue',
		],
		self::TYPE_GRENADE => [
			'color' => 'blue',
		],
		self::TYPE_MORTAR => [
			'color' => 'blue',
		],
		self::TYPE_FLAMER => [
			'color' => 'red',
		],
		self::TYPE_GAZ => [
			'color' => 'green',
		],
	];

	/**
	 * @return array
	 */
	public static function get_types_list() {

		if (is_null(self::$types_list)) {
			self::$types_list = array();

			foreach (self::$types as $type => $params) {
				self::$types_list[$type] = $type;
			}
		}

		return self::$types_list;
	}

	public static function get_type_label(Jelly_Model $model) {
		$type = $model->bullet_type;

		$label = Force_Label::factory('');
		$label->color_gray();

		$caption = Helper::get_bonus_caption($type);
		$params = Arr::get(self::$types, $type, []);
		$color = Arr::get($params, 'color');
		switch ($color) {
			case 'red':
				$label->color_red();
				break;
			case 'yellow':
				$label->color_yellow();
				break;
			case 'green':
				$label->color_green();
				break;
			case 'cyan':
				$label->color_cyan();
				break;
			case 'blue':
				$label->color_blue();
				break;
		}

		$label->label($caption);

		return $label;
	}

} // End Core_Calibre_Type
