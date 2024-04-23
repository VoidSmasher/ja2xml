<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Choices_Item
 * User: legion
 * Date: 27.03.2021
 * Time: 15:11
 */
class Core_Choices_Item extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Choices_Item';
	protected $model_name = 'choices_item';

	const NPC_SIDE_ENEMY = 'Enemy';
	const NPC_SIDE_MILITIA = 'Militia';

	const NPC_TYPE_COMMON = 'Common';
	const NPC_TYPE_ADMIN = 'Admin';
	const NPC_TYPE_GREEN = 'Green';
	const NPC_TYPE_REGULAR = 'Regular';
	const NPC_TYPE_ELITE = 'Elite';

	protected static $npc_sides = [
		self::NPC_SIDE_ENEMY => self::NPC_SIDE_ENEMY,
		self::NPC_SIDE_MILITIA => self::NPC_SIDE_MILITIA,
	];

	protected static $npc_types = [
		self::NPC_TYPE_COMMON => self::NPC_TYPE_COMMON,
		self::NPC_TYPE_ADMIN => self::NPC_TYPE_ADMIN,
		self::NPC_TYPE_GREEN => self::NPC_TYPE_GREEN,
		self::NPC_TYPE_REGULAR => self::NPC_TYPE_REGULAR,
		self::NPC_TYPE_ELITE => self::NPC_TYPE_ELITE,
	];

	protected static $lists = [
		0 => 'Helmets',
		1 => 'Vests',
		2 => 'Leggings',
		3 => 'Hand Grenades',
		4 => 'Kits',
		5 => 'Bombs',
		6 => 'Misc',
		7 => 'Grenade Launchers',
		8 => 'Single Shot Rocket Launchers',
		9 => 'Multi-shot Rocket Launchers',
		10 => 'Mortars',
		11 => 'Armour Attachments',

		13 => 'Low Quality Face Items',
		14 => 'High Quality Face Items',

		16 => 'Scopes',
		17 => 'Weapon Attachments',
		18 => 'Knives',
		19 => 'Ammo',
		20 =>'LBE',
	];

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function get_list_name($list) {
		return array_key_exists($list, self::$lists) ? self::$lists[$list] : 'Unknown';
	}

	public static function get_lists() {
		return self::$lists;
	}

	public static function get_npc_sides() {
		return self::$npc_sides;
	}

	public static function get_npc_types() {
		return self::$npc_types;
	}

	public static function get_combo_filter() {
		return array(
			self::NPC_SIDE_ENEMY . '_' . self::NPC_TYPE_COMMON => 'Enemy Common',
			self::NPC_SIDE_ENEMY . '_' . self::NPC_TYPE_ADMIN => 'Enemy Admin',
			self::NPC_SIDE_ENEMY . '_' . self::NPC_TYPE_REGULAR => 'Enemy Regular',
			self::NPC_SIDE_ENEMY . '_' . self::NPC_TYPE_ELITE => 'Enemy Elite',
			self::NPC_SIDE_MILITIA . '_' . self::NPC_TYPE_GREEN => 'Militia Green',
			self::NPC_SIDE_MILITIA . '_' . self::NPC_TYPE_REGULAR => 'Militia Regular',
			self::NPC_SIDE_MILITIA . '_' . self::NPC_TYPE_ELITE => 'Militia Elite',
		);
	}

	public static function apply_combo_filter(Jelly_Builder $builder, Force_Filter $filter) {
		$npc_filter = $filter->get_value('npc');

		if ($npc_filter) {
			$npc_array = explode('_', $npc_filter);
			if (count($npc_array) === 2) {
				list($npc_side, $npc_type) = explode('_', $npc_filter);
				$builder->where('npc_side', '=', $npc_side);
				$builder->where('npc_type', '=', $npc_type);
			}
		}
	}

}