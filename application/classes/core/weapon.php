<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Weapon
 * User: legion
 * Date: 05.05.18
 * Time: 6:05
 */
class Core_Weapon extends Core_Common {

	const CLASS_BLANK = 0;
	const CLASS_HANDGUN = 1;
	const CLASS_SMG = 2;
	const CLASS_RIFLE = 3;
	const CLASS_MACHINEGUN = 4;
	const CLASS_SHOTGUN = 5;
	const CLASS_MELEE = 6;
	const CLASS_NPC = 7;

	const TYPE_BLANK = 0;
	const TYPE_PISTOL = 1;
	const TYPE_MP = 2;
	const TYPE_SMG = 3;
	const TYPE_RIFLE = 4;
	const TYPE_SNIPER = 5;
	const TYPE_AR = 6;
	const TYPE_MACHINEGUN = 7;
	const TYPE_SHOTGUN = 8;

	const DEFAULT_APS_TO_RELOAD = 20;

	use Core_Common_Static;
	use Core_Weapon_Calculate_Energy;
	use Core_Weapon_Calculate_Damage;
	use Core_Weapon_Calculate_Range;
	use Core_Weapon_Calculate_Accuracy;
	use Core_Weapon_Calculate_Ready;
	use Core_Weapon_Calculate_Handling;
	use Core_Weapon_Calculate_SP4T;
	use Core_Weapon_Calculate_APTRM;
	use Core_Weapon_Calculate_Reload;
	use Core_Weapon_Calculate_Auto;
	use Core_Weapon_Calculate_Burst;
	use Core_Weapon_Calculate_Recoil;
	use Core_Weapon_Calculate_Deadliness;
	use Core_Weapon_Calculate_Messy;

	protected static $model_class = 'Model_Weapon';
	protected $model_name = 'weapon';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * TYPE
	 */

	public static function get_weapon_type(Model_Weapon_Group $model) {
		return ($model->weapon_type > 0) ? $model->weapon_type : $model->ubWeaponType;
	}

	public static function get_type_name($ubWeaponType) {
		switch ($ubWeaponType) {
			case Core_Weapon::TYPE_BLANK:
				$name = 'NONE';
				break;
			case Core_Weapon::TYPE_PISTOL:
				$name = 'Pistol';
				break;
			case Core_Weapon::TYPE_MP:
				$name = 'Machine Pistol';
				break;
			case Core_Weapon::TYPE_SMG:
				$name = 'SMG';
				break;
			case Core_Weapon::TYPE_RIFLE:
				$name = 'Rifle';
				break;
			case Core_Weapon::TYPE_SNIPER:
				$name = 'Sniper Rifle';
				break;
			case Core_Weapon::TYPE_AR:
				$name = 'Assault Rifle';
				break;
			case Core_Weapon::TYPE_MACHINEGUN:
				$name = 'Machinegun';
				break;
			case Core_Weapon::TYPE_SHOTGUN:
				$name = 'Shotgun';
				break;
			default:
				$name = '';
				break;
		}

		return $name;
	}

	public static function get_type_label(Model_Weapon_Group $model) {
		$ubWeaponType = self::get_weapon_type($model);

		$label = Force_Label::factory('');

		if ($ubWeaponType != $model->ubWeaponType) {
			$label->label(self::get_type_name($ubWeaponType))->color_red();
		} else {
			switch ($ubWeaponType) {
				case Core_Weapon::TYPE_BLANK:
					$label->label(self::get_type_name($ubWeaponType))->color_gray();
					break;
				case Core_Weapon::TYPE_PISTOL:
				case Core_Weapon::TYPE_MP:
					$label->label(self::get_type_name($ubWeaponType))->color_yellow();
					break;
				case Core_Weapon::TYPE_SMG:
				case Core_Weapon::TYPE_SHOTGUN:
					$label->label(self::get_type_name($ubWeaponType))->color_green();
					break;
				case Core_Weapon::TYPE_RIFLE:
				case Core_Weapon::TYPE_SNIPER:
					$label->label(self::get_type_name($ubWeaponType))->color_cyan();
					break;
				case Core_Weapon::TYPE_AR:
				case Core_Weapon::TYPE_MACHINEGUN:
					$label->label(self::get_type_name($ubWeaponType))->color_blue();
					if ($model->has_sniper_barrel) {
						$label->color_cyan();
					}
					break;
				default:
					$label = '';
					break;
			}
		}

		if ($label instanceof Force_Label) {
			$label = $label->render();
		}

		return $label;
	}

	public static function get_type_list() {
		return array(
//			self::TYPE_BLANK => self::get_type_name(self::TYPE_BLANK),
			self::TYPE_PISTOL => self::get_type_name(self::TYPE_PISTOL),
			self::TYPE_MP => self::get_type_name(self::TYPE_MP),
			self::TYPE_SMG => self::get_type_name(self::TYPE_SMG),
			self::TYPE_RIFLE => self::get_type_name(self::TYPE_RIFLE),
			self::TYPE_SNIPER => self::get_type_name(self::TYPE_SNIPER),
			self::TYPE_AR => self::get_type_name(self::TYPE_AR),
			self::TYPE_MACHINEGUN => self::get_type_name(self::TYPE_MACHINEGUN),
			self::TYPE_SHOTGUN => self::get_type_name(self::TYPE_SHOTGUN),
		);
	}

	/*
	 * CLASS
	 */

	public static function get_weapon_class(Model_Weapon_Group $model) {
		return ($model->weapon_class > 0) ? $model->weapon_class : $model->ubWeaponClass;
	}

	public static function get_class_name($ubWeaponClass) {
		switch ($ubWeaponClass) {
			case Core_Weapon::CLASS_BLANK:
				$name = 'NONE';
				break;
			case Core_Weapon::CLASS_HANDGUN:
				$name = 'Handgun';
				break;
			case Core_Weapon::CLASS_SMG:
				$name = 'SMG';
				break;
			case Core_Weapon::CLASS_RIFLE:
				$name = 'Rifle';
				break;
			case Core_Weapon::CLASS_MACHINEGUN:
				$name = 'Machinegun';
				break;
			case Core_Weapon::CLASS_SHOTGUN:
				$name = 'Shotgun';
				break;
			case Core_Weapon::CLASS_NPC:
				$name = 'NPC';
				break;
			default:
				$name = '';
				break;
		}

		return $name;
	}

	public static function get_class_label(Model_Weapon_Group $model) {
		$ubWeaponClass = self::get_weapon_class($model);

		$label = Force_Label::factory('');

		if ($ubWeaponClass != $model->ubWeaponClass) {
			$label->label(self::get_class_name($ubWeaponClass))->color_red();
		} else {
			switch ($ubWeaponClass) {
				case Core_Weapon::CLASS_BLANK:
				case Core_Weapon::CLASS_MELEE:
				case Core_Weapon::CLASS_NPC:
					$label->label(self::get_class_name($ubWeaponClass))->color_gray();
					break;
				case Core_Weapon::CLASS_HANDGUN:
					$label->label(self::get_class_name($ubWeaponClass))->color_yellow();
					break;
				case Core_Weapon::CLASS_SHOTGUN:
				case Core_Weapon::CLASS_SMG:
					$label->label(self::get_class_name($ubWeaponClass))->color_green();
					break;
				case Core_Weapon::CLASS_RIFLE:
					$label->label(self::get_class_name($ubWeaponClass))->color_cyan();
					break;
				case Core_Weapon::CLASS_MACHINEGUN:
					$label->label(self::get_class_name($ubWeaponClass))->color_blue();
					break;
				default:
					$label = '';
					break;
			}
		}

		if ($label instanceof Force_Label) {
			$label = $label->render();
		}

		return $label;
	}

	public static function get_class_list() {
		return array(
//			self::CLASS_BLANK => self::get_class_name(self::CLASS_BLANK),
			self::CLASS_HANDGUN => self::get_class_name(self::CLASS_HANDGUN),
			self::CLASS_SMG => self::get_class_name(self::CLASS_SMG),
			self::CLASS_RIFLE => self::get_class_name(self::CLASS_RIFLE),
			self::CLASS_MACHINEGUN => self::get_class_name(self::CLASS_MACHINEGUN),
			self::CLASS_SHOTGUN => self::get_class_name(self::CLASS_SHOTGUN),
//			self::CLASS_MELEE => self::get_class_name(self::CLASS_MELEE),
//			self::CLASS_NPC => self::get_class_name(self::CLASS_NPC),
		);
	}

	/**
	 * @return Force_Filter_Input
	 */
	public static function get_filter_control_name() {
//		$session = Session::instance();
//		$weapon_name = $session->get('weapon_name');

		$filter_control = Force_Filter_Input::factory('weapon_name', __('common.name'));

//		if ($weapon_name) {
//			$filter_control->default_value($weapon_name);
//		}

		return $filter_control;
	}

	public static function check_filter_control_name(Force_Filter $filter) {
		$session = Session::instance();
		$weapon_name = $session->get('weapon_name');

		$filter_control = $filter->get_control('weapon_name');
		if ($filter_control instanceof Force_Filter_Input) {
			$weapon_name = $filter_control->get_value();
		}

		if (empty($weapon_name)) {
			$session->delete('weapon_name');
		} else {
			$session->set('weapon_name', $weapon_name);
		}

		return $weapon_name;
	}

	public static function get_filter_index() {
		$indexes = Core_Weapon_Mod::factory()->get_builder()
			->select_column('uiIndex')
			->where('uiIndex', '>', 0)
			->select_all()
			->as_array('uiIndex', 'uiIndex');

		$session = Session::instance();
		$weapon_index = $session->get('weapon_index');

		$filter_control = Force_Filter_Select::factory('weapon_index', 'Index', $indexes);

		if ($weapon_index) {
			$filter_control->default_value($weapon_index);
		}

		return $filter_control;
	}

	public static function check_filter_index(Force_Filter $filter) {
		$session = Session::instance();
		$weapon_index = $session->get('weapon_index');

		$filter_control = $filter->get_control('weapon_index');
		if ($filter_control instanceof Force_Filter_Select) {
			$weapon_index = $filter_control->get_value();
		}

		$weapon_index = intval($weapon_index);

		if (empty($weapon_index)) {
			$session->delete('weapon_index');
		} else {
			$session->set('weapon_index', $weapon_index);
		}

		return $weapon_index;
	}

	public static function button_index($weapon_index, $param = 'weapon_index') {
		$url = Force_URL::current()
			->query_param($param, $weapon_index)
			->get_url();

		return Force_Button::factory($weapon_index)
			->link($url)
			->btn_xs();
	}

	public static function button_index_model(Jelly_Model $model) {
		$model->format('uiIndex', self::button_index($model->uiIndex)->render());
	}

} // End Core_Weapon
