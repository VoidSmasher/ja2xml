<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Merge_Mod
 * User: legion
 * Date: 25.04.2021
 * Time: 10:44
 */
class Core_Merge_Mod extends Core_Common {

	const TYPE_DESTRUCTION = 0;
	const TYPE_COMBINE_POINTS = 1;
	const TYPE_TREAT_ARMOUR = 2;
	const TYPE_EXPLOSIVE = 3;
	const TYPE_EASY_MERGE = 4;
	const TYPE_ELECTRONIC_MERGE = 5;
	const TYPE_USE_ITEM = 6;
	const TYPE_USE_ITEM_HARD = 7;
	const TYPE_REPLACE_BARREL = 8;
	const TYPE_MAKE_TRAP = 9;
	const TYPE_ADD_POINTS = 12;
	const TYPE_DIVIDE_POINTS = 13;

	private static $types = array(
		self::TYPE_DESTRUCTION => 'DESTRUCTION',
		self::TYPE_COMBINE_POINTS => 'COMBINE_POINTS',
		self::TYPE_TREAT_ARMOUR => 'TREAT_ARMOUR',
		self::TYPE_EXPLOSIVE => 'EXPLOSIVE',
		self::TYPE_EASY_MERGE => 'EASY_MERGE',
		self::TYPE_ELECTRONIC_MERGE => 'ELECTRONIC_MERGE',
		self::TYPE_USE_ITEM => 'USE_ITEM',
		self::TYPE_USE_ITEM_HARD => 'USE_ITEM_HARD',
		self::TYPE_REPLACE_BARREL => 'REPLACE_BARREL',
		self::TYPE_MAKE_TRAP => 'MAKE_TRAP',
		self::TYPE_ADD_POINTS => 'ADD_POINTS',
		self::TYPE_DIVIDE_POINTS => 'DIVIDE_POINTS',
	);

	use Core_Common_Static;

	protected static $model_class = 'Model_Merge_Mod';
	protected $model_name = 'merge_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function get_merge_type_name($type) {
		if (array_key_exists($type, self::$types)) {
			return $type . ' ' . self::$types[$type];
		} else {
			return $type . ' Unknown';
		}
	}

	public static function get_merge_types() {
		$types = [];
		foreach (self::$types as $type => $name) {
			$types[] = self::get_merge_type_name($type);
		}
		return $types;
	}

}