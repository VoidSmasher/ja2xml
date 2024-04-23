<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachment_Data
 * User: legion
 * Date: 13.10.19
 * Time: 1:18
 */
class Core_Attachment_Data extends Core_Common {

	const DEFAULT_AP_COST = 20;

	const INDEX_PISTOL_SUPPRESSOR = 207;
	const INDEX_BIPOD = 209;
	const INDEX_DUCKBILL = 244;
	const INDEX_SNIPER_SUPPRESSOR = 592;
	const INDEX_GRIPPOD = 947;
	const INDEX_762_39_MAG_ADAPTER = 997;
	const INDEX_556_45_MAG_ADAPTER = 1001;
	const INDEX_9_19_MAG_ADAPTER = 1002;
	const INDEX_FlASH_SUPPRESSOR = 1003;
	const INDEX_AR_SUPPRESSOR = 1012;
	const INDEX_MATCH_SIGHTS = 1183;
	const INDEX_IMP_MOD_SHOTGUN_CHOKE = 1329;
	const INDEX_FULL_SHOTGUN_CHOKE = 1330;
	const INDEX_RIFLED_SHOTGUN_CHOKE = 1331;
	const INDEX_TALON = 50;
	const INDEX_M203 = 902;
	const INDEX_FN_EGLM = 921;
	const INDEX_KAC_MASTERKEY = 1522;
	const INDEX_AICW_LAUNCHER = 909;
	const INDEX_F2000_LAUNCHER = 910;
	const INDEX_OICW_LAUNCHER = 912;

	const INDEX_STOCK_NONE = -1;
	const INDEX_STOCK_PISTOL = -2;
	const INDEX_STOCK_BULLPUP = -3;
	const INDEX_STOCK_TRANSPORTATION = -5;
	const INDEX_STOCK_FOLDING_SIMPLE = -6;
	const INDEX_STOCK_WEAPON = -7;
	const INDEX_STOCK_FOLDING_ADJUSTABLE = -8;
	const INDEX_STOCK_RETRACTABLE_SIMPLE = -9;

	const INDEX_FOREGRIP_THIN = -17;
	const INDEX_FOREGRIP_MAG = -20;
	const INDEX_FOREGRIP_ANGLED = 1010;

	use Core_Common_Static;
	use Core_Attachments_Default;
	use Core_Attachments_Possible;

	protected static $model_class = 'Model_Data_Attachment';
	protected $model_name = 'attachment_data';

	private static $fixed_attachments;
	private static $get_attachments_list = array();
	private static $get_attachments_list_of_models = array();

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function get_attachments_builder($with_integrated = true) {
		$builder = Core_Attachment_Data::factory()->preset_for_admin()->get_builder()
			->join('items', 'LEFT')->on('items.uiIndex', '=', 'data_attachments.uiIndex')
			->join('items_mod', 'LEFT')->on('items_mod.uiIndex', '=', 'data_attachments.uiIndex')
			->join('slots_mod', 'LEFT')->on('slots_mod.nasAttachmentClass', '=', 'data_attachments.nasAttachmentClass')
			->select_column('data_attachments.*')
			->select_column('data_attachments.uiIndex', 'uiIndex')
			->select_column('slots_mod.szSlotName', 'szSlotName')
			->select_column('items.ubCoolness', 'ubCoolness_original')
			->select_column(DB::expr('IFNULL(data_attachments.szLongItemName, items_mod.szLongItemName)'), 'attach_name')
			->select_column(DB::expr('IFNULL(data_attachments.szLongItemName, items_mod.szLongItemName)'), 'szLongItemName');

		if (!$with_integrated) {
			$builder->where('uiIndex', '>', 0);
		}

		foreach ($builder->meta()->fields() as $field_name => $field_data) {
			if (Core_Item::check_field_save_status($field_data)) {
				$builder->select_column('items_mod.' . $field_name, 'item_' . $field_name);
			}
		}

		return $builder;
	}

	public static function update_builder_with_data_attachments(Jelly_Builder $builder) {
		$builder
			->select_column('data_attachments.default_attachments', 'default_attachments')
			->select_column('data_attachments.possible_attachments', 'possible_attachments')
			->select_column('data_attachments.attachment_bonuses', 'attachment_bonuses')
			->select_column('data_attachments.attachment_mounts', 'attachment_mounts')
			->select_column('data_attachments.attachment_mounts_external', 'attachment_mounts_external')
			->select_column('data_attachments.attachment_types', 'attachment_types');
	}

	public static function get_fixed_attachments() {
		if (!is_array(self::$fixed_attachments)) {
			$builder = Core_Attachment_Data::factory()->get_builder()
//				->where('Inseparable', '=', true)
				->where('is_fixed', '=', true)
				->select_column('uiIndex')
				->select_column('nasAttachmentClass');

			self::$fixed_attachments = $builder
				->select_all()
				->as_array('uiIndex', 'nasAttachmentClass');
		}

		return self::$fixed_attachments;
	}

	public static function get_weight(Jelly_Model $model) {
		if (!empty($model->ubWeight)) {
			$weight = $model->ubWeight;
		} else {
			$weight = $model->item_ubWeight;
		}
		return (integer)$weight;
	}

	public static function get_name_long(Jelly_Model $model) {
		if (!empty($model->szLongItemName)) {
			$name = $model->szLongItemName;
		} else {
			$name = $model->item_szLongItemName;
		}
		return $name;
	}

	/**
	 * @param bool $models
	 * @param bool $with_integrated
	 * @param null $nasAttachmentClass
	 * @param null $attachment_type
	 * @return array
	 */
	protected static function _get_attachments($models = true, $with_integrated = false, $nasAttachmentClass = NULL, $attachment_type = NULL) {
		$builder = Core_Attachment_Data::get_attachments_builder();

		$builder
			->order_by('item_szLongItemName');

		$collection = $builder->select_all();

		$array = array();
		foreach ($collection as $model) {
			if ($nasAttachmentClass && $nasAttachmentClass != $model->nasAttachmentClass) {
				continue;
			}

			if ($attachment_type && !Attachment::has_type($model, $attachment_type)) {
				continue;
			}

			if (Attachment::has_type($model, Attachment::TYPE_UPGRADE_KIT)) {
				continue;
			}

			if (!$with_integrated && $model->uiIndex < 1) {
				continue;
			}

			if ($models) {
				$array[$model->uiIndex] = $model;
			} else {
				$array[$model->uiIndex] = self::get_name_long($model);
			}
		}

		return $array;
	}

	/**
	 * @param bool $with_integrated
	 * @param null $nasAttachmentClass
	 * @param null $attachment_type
	 * @return array
	 */
	public static function get_attachments_list($with_integrated = false, $nasAttachmentClass = NULL, $attachment_type = NULL) {
		if (isset(self::$get_attachments_list[$with_integrated][$nasAttachmentClass][$attachment_type])) {
			return self::$get_attachments_list[$with_integrated][$nasAttachmentClass][$attachment_type];
		}
		self::$get_attachments_list[$with_integrated][$nasAttachmentClass][$attachment_type] = self::_get_attachments(false, $with_integrated, $nasAttachmentClass, $attachment_type);
		return self::$get_attachments_list[$with_integrated][$nasAttachmentClass][$attachment_type];
	}

	/**
	 * @param bool $with_integrated
	 * @param null $nasAttachmentClass
	 * @param null $attachment_type
	 * @return array
	 */
	public static function get_attachments_list_of_models($with_integrated = false, $nasAttachmentClass = NULL, $attachment_type = NULL) {
		if (isset(self::$get_attachments_list_of_models[$with_integrated][$nasAttachmentClass][$attachment_type])) {
			return self::$get_attachments_list_of_models[$with_integrated][$nasAttachmentClass][$attachment_type];
		}
		self::$get_attachments_list_of_models[$with_integrated][$nasAttachmentClass][$attachment_type] = self::_get_attachments(true, $with_integrated, $nasAttachmentClass, $attachment_type);
		return self::$get_attachments_list_of_models[$with_integrated][$nasAttachmentClass][$attachment_type];
	}

	/**
	 * @param $field
	 * @param bool $with_integrated
	 * @return array
	 */
	public static function get_attachments_list_by_field($field, $with_integrated = false) {
		switch ($field) {
			case 'integrated_suppressor_index':
				return Core_Attachment_Data::get_suppressor_list($with_integrated);
			case 'integrated_laser_index':
				return Core_Attachment_Data::get_laser_list($with_integrated);
			case 'integrated_sight_index':
				return Core_Attachment_Data::get_sight_list($with_integrated);
			case 'integrated_scope_index':
				return Core_Attachment_Data::get_scope_list($with_integrated);
			case 'integrated_bipod_index':
				return Core_Attachment_Data::get_bipod_list($with_integrated);
			case 'integrated_foregrip_index':
				return Core_Attachment_Data::get_foregrip_list($with_integrated);
			case 'integrated_stock_index':
				return Core_Attachment_Data::get_stock_list($with_integrated);
			default:
				return array();
		}
	}

	public static function get_stock_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, Core_Item::NAS_ATTACHMENT_CLASS_STOCK);
	}

	public static function get_scope_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, Core_Item::NAS_ATTACHMENT_CLASS_SCOPE);
	}

	public static function get_sight_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, Core_Item::NAS_ATTACHMENT_CLASS_SIGHT);
	}

	public static function get_laser_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, Core_Item::NAS_ATTACHMENT_CLASS_LASER);
	}

	public static function get_bipod_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, null, Attachment::TYPE_BIPOD);
	}

	public static function get_foregrip_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, null, Attachment::TYPE_FOREGRIP);
	}

	public static function get_suppressor_list($with_integrated = false) {
		return self::get_attachments_list($with_integrated, null, Attachment::TYPE_SUPPRESSOR);
	}

	public static function get_integrated_attachment_labels(Jelly_Model $model) {
		/** @var Model_Weapon_Group $model */
		$integrated_attachments = array();

		$caption = $model->integrated_stock_name;

		if (empty($caption)) {
			$caption = 'Default Stock';
		}

		$label = Force_Label::factory($caption);

		switch ($model->integrated_stock_index) {
			case NULL:
				$label->color_gray();
				break;
			case Core_Attachment_Data::INDEX_STOCK_NONE:
				$label->color_red();
				break;
			case Core_Attachment_Data::INDEX_STOCK_PISTOL:
				$label->color_yellow();
				break;
			case Core_Attachment_Data::INDEX_STOCK_BULLPUP:
			case Core_Attachment_Data::INDEX_STOCK_TRANSPORTATION:
			case Core_Attachment_Data::INDEX_STOCK_WEAPON:
				$label->color_green();
				break;
			default:
				$label->color_blue();
		}

		$integrated_attachments[] = $label->render();

		if ($model->integrated_scope_index) {
			$integrated_attachments[] = Force_Label::factory($model->integrated_scope_name)
				->color_cyan()
				->render();
		}

		if ($model->integrated_sight_index) {
			$integrated_attachments[] = Force_Label::factory($model->integrated_sight_name)
				->color_cyan()
				->render();
		}

		if ($model->integrated_laser_index) {
			$integrated_attachments[] = Force_Label::factory($model->integrated_laser_name)
				->color_red()
				->render();
		}

		if ($model->integrated_foregrip_index) {
			$integrated_attachments[] = Force_Label::factory($model->integrated_foregrip_name)
				->color_blue()
				->render();
		}

		if ($model->integrated_bipod_index) {
			$integrated_attachments[] = Force_Label::factory($model->integrated_bipod_name)
				->color_blue()
				->render();
		}

		if ($model->integrated_suppressor_index) {
			$integrated_attachments[] = Force_Label::factory($model->integrated_suppressor_name)
				->color_green()
				->render();
		}

		if ($model->has_hp_iron_sights) {
			$integrated_attachments[] = Force_Label::factory('High Profile Iron Sights')
				->color_yellow()
				->render();
		}

		if ($model->has_hp_scope_mount) {
			$integrated_attachments[] = Force_Label::factory('High Profile Scope Mount')
				->color_yellow()
				->render();
		}

		if ($model->has_hk_trigger) {
			$integrated_attachments[] = Force_Label::factory('HK Trigger')
				->color_blue()
				->render();
		}

		if ($model->has_mag_stanag) {
			$integrated_attachments[] = Force_Label::factory('STANAG Mag')
				->color_blue()
				->render();
		}

		if ($model->has_drum_mag) {
			$integrated_attachments[] = Force_Label::factory('Drum Mag')
				->color_yellow()
				->render();
		}

		if ($model->has_calico_mag) {
			$integrated_attachments[] = Force_Label::factory('Calico Mag')
				->color_yellow()
				->render();
		}

		if ($model->has_long_mag) {
			$integrated_attachments[] = Force_Label::factory('Long Mag')
				->color_blue()
				->render();
		}

		/*
		 * Weapon Status
		 */
		if ($model->is_secondary_weapon) {
			$integrated_attachments[] = Force_Label::factory('Secondary Weapon')
				->color_yellow()
				->render();
		}

		/*
		 * Integrated features
		 */

		if ($model->has_bolt_hold_open) {
			$integrated_attachments[] = Force_Label::factory('Bolt Hold Open')
				->color_blue()
				->render();
		}

		if ($model->has_balanced_automatic) {
			$integrated_attachments[] = Force_Label::factory('Balanced Automatic')
				->color_gray()
				->render();
		}

		if ($model->has_compensator) {
			$integrated_attachments[] = Force_Label::factory('Compensator')
				->color_gray()
				->render();
		}

		if ($model->has_muzzle_break) {
			$integrated_attachments[] = Force_Label::factory('Muzzle Break')
				->color_gray()
				->render();
		}

		if ($model->has_recoil_reducing_stock) {
			$integrated_attachments[] = Force_Label::factory('Recoil-reducing Stock')
				->color_gray()
				->render();
		}

		if ($model->has_recoil_buffer_in_stock) {
			$integrated_attachments[] = Force_Label::factory('Recoil Buffer in Stock')
				->color_gray()
				->render();
		}

		if ($model->has_ported_barrel) {
			$integrated_attachments[] = Force_Label::factory('Ported Barrel')
				->color_gray()
				->render();
		}

		if ($model->has_heavy_barrel) {
			$integrated_attachments[] = Force_Label::factory('Heavy Barrel')
				->color_yellow()
				->render();
		}

		if ($model->has_sniper_barrel) {
			$integrated_attachments[] = Force_Label::factory('Matching Barrel')
				->color_cyan()
				->render();
		}

		if ($model->has_floating_barrel) {
			$integrated_attachments[] = Force_Label::factory('Free-floating Barrel')
				->color_cyan()
				->render();
		}

		if ($model->has_replaceable_barrel) {
			$integrated_attachments[] = Force_Label::factory('Replaceable Barrel')
				->color_yellow()
				->render();
		}

		if ($model->has_cheek_piece) {
			$integrated_attachments[] = Force_Label::factory('Cheek-piece')
				->color_blue()
				->render();
		}

		if ($model->has_adjustable_cheek_piece) {
			$integrated_attachments[] = Force_Label::factory('Adjustable Cheek-piece')
				->color_blue()
				->render();
		}

		if ($model->has_adjustable_butt_stock) {
			$integrated_attachments[] = Force_Label::factory('Adjustable Butt-stock')
				->color_blue()
				->render();
		}

		if ($model->has_adjustable_grip) {
			$integrated_attachments[] = Force_Label::factory('Adjustable Grip')
				->color_blue()
				->render();
		}

		return implode(' ', $integrated_attachments);
	}

	public static function get_filter_control_has_integrated() {
		$control = Force_Filter_Select::factory('integrated', 'Integrated', [
			'bipod' => 'Bipod',
			'foregrip' => 'Foregrip',
			'laser' => 'Laser',
			'stock' => 'Stock',
			'scope' => 'Scope',
			'sight' => 'Sight',
			'suppressor' => 'Suppressor',
			'trigger_group' => 'Trigger Group',
			'large_mag' => 'Large Mag',
			'compensator' => 'Compensator',
			'muzzle_break' => 'Muzzle Break',
			'recoil_reducing_stock' => 'Recoil Reducing Stock',
			'recoil_buffer_in_stock' => 'Recoil Buffer in Stock',
			'ported_barrel' => 'Ported Barrel',
			'heavy_barrel' => 'Heavy Barrel',
			'sniper_barrel' => 'Matching Barrel',
			'floating_barrel' => 'Floating Barrel',
			'replaceable_barrel' => 'Replaceable Barrel',
			'cheek_piece' => 'Cheek Piece',
			'adjustable_cheek_piece' => 'Adjustable Cheek Piece',
			'adjustable_butt_stock' => 'Adjustable Butt Stock',
			'adjustable_grip' => 'Adjustable Grip',
		]);
		return $control;
	}

	public static function check_filter_control_has_integrated(Force_Filter $filter, Jelly_Builder $builder) {
		$integrated = $filter->get_value('integrated');

		switch ($integrated) {
			case 'foregrip':
				$builder->where('data_weapons.integrated_foregrip_index', 'IS NOT', null);
				break;
			case 'bipod':
				$builder->where('data_weapons.integrated_bipod_index', 'IS NOT', null);
				break;
			case 'laser':
				$builder->where('data_weapons.integrated_laser_index', 'IS NOT', null);
				break;
			case 'stock':
				$builder->where('data_weapons.integrated_stock_index', 'IS NOT', null);
				break;
			case 'scope':
				$builder->where('data_weapons.integrated_scope_index', 'IS NOT', null);
				break;
			case 'sight':
				$builder->where('data_weapons.integrated_sight_index', 'IS NOT', null);
				break;
			case 'suppressor':
				$builder->where('data_weapons.integrated_suppressor_index', 'IS NOT', null);
				break;

			case 'trigger_group':
				$builder->where('data_weapons.has_hk_trigger', '=', true);
				break;

			case 'large_mag':
				$builder->and_where_open()
					->where('data_weapons.has_drum_mag', '=', true)
					->or_where('data_weapons.has_calico_mag', '=', true)
					->and_where_close();
				break;

			case 'compensator':
				$builder->where('data_weapons.has_compensator', '=', true);
				break;
			case 'muzzle_break':
				$builder->where('data_weapons.has_muzzle_break', '=', true);
				break;
			case 'recoil_reducing_stock':
				$builder->where('data_weapons.has_recoil_reducing_stock', '=', true);
				break;
			case 'recoil_buffer_in_stock':
				$builder->where('data_weapons.has_recoil_buffer_in_stock', '=', true);
				break;

			case 'ported_barrel':
				$builder->where('data_weapons.has_ported_barrel', '=', true);
				break;
			case 'heavy_barrel':
				$builder->where('data_weapons.has_heavy_barrel', '=', true);
				break;
			case 'sniper_barrel':
				$builder->where('data_weapons.has_sniper_barrel', '=', true);
				break;
			case 'floating_barrel':
				$builder->where('data_weapons.has_floating_barrel', '=', true);
				break;
			case 'replaceable_barrel':
				$builder->where('data_weapons.has_replaceable_barrel', '=', true);
				break;

			case 'cheek_piece':
				$builder->where('data_weapons.has_cheek_piece', '=', true);
				break;
			case 'adjustable_cheek_piece':
				$builder->where('data_weapons.has_adjustable_cheek_piece', '=', true);
				break;
			case 'adjustable_butt_stock':
				$builder->where('data_weapons.has_adjustable_butt_stock', '=', true);
				break;
			case 'adjustable_grip':
				$builder->where('data_weapons.has_adjustable_grip', '=', true);
				break;
		}
	}

	public static function stack_integrated_attachments(Jelly_Model $item, array $attachments, array $fields, array $incrementable_fields) {
		foreach ([
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
		] as $stance) {
			$item->{$stance . '_ARRAY'} = array();
		}

		foreach ([
			'integrated_foregrip_index',
			'integrated_bipod_index',
			'integrated_laser_index',
			'integrated_stock_index',
			'integrated_scope_index',
			'integrated_sight_index',
			'integrated_suppressor_index',
		] as $integrated_index) {
			if ($item->{$integrated_index}) {
				$integrated_attachment = Arr::get($attachments, $item->{$integrated_index});
				if ($integrated_attachment instanceof Jelly_Model) {
					self::stack_modifiers($item, $integrated_attachment, $fields, $incrementable_fields);
				}
			}
		}

		/*
		 * Убираем лишние данные.
		 * Значения будут автоматически передаваться по пути STAND -> CROUCH -> PRONE
		 * Таким образом, если значение было установлено в пути выше,
		 * его не нужно опять устанавливать в то же значение в текущем состоянии.
		 */
		foreach ([
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
		] as $stance) {
			$stance_array = $item->{$stance . '_ARRAY'};

			switch ($stance) {
				case Ja2_Item::PRONE_MODIFIERS:
					$CROUCH_MODIFIERS = $item->{Ja2_Item::CROUCH_MODIFIERS . '_ARRAY'};

					foreach ($stance_array as $stance_field => $stance_value) {
						if (array_key_exists($stance_field, $CROUCH_MODIFIERS)) {
							if ($stance_value == $CROUCH_MODIFIERS[$stance_field]) {
								unset($stance_array[$stance_field]);
							}
						}
					}

					break;
				case Ja2_Item::CROUCH_MODIFIERS:
					$STAND_MODIFIERS = $item->{Ja2_Item::STAND_MODIFIERS . '_ARRAY'};

					foreach ($stance_array as $stance_field => $stance_value) {
						if (array_key_exists($stance_field, $STAND_MODIFIERS)) {
							if ($stance_value == $STAND_MODIFIERS[$stance_field]) {
								unset($stance_array[$stance_field]);
							}
						}
					}

					break;
			}

			if (!empty($stance_array)) {
				$item->{$stance} = json_encode($stance_array);
			} else {
				$item->{$stance} = NULL;
			}

			unset($stance_array);
		}
	}

	/**
	 * @param Jelly_Model $item
	 * @param Jelly_Model $attachment
	 * @param array $fields
	 * @param array $incrementable_fields
	 */
	private static function stack_modifiers(Jelly_Model $item, Jelly_Model $attachment, array $fields, array $incrementable_fields) {
		foreach ($fields as $field) {
			$value_attachment = $attachment->{$field};
			if (!empty($value_attachment)) {
				if (in_array($field, $incrementable_fields)) {
					$item->{$field} += $value_attachment;
				} else {
					$item->{$field} = $value_attachment;
				}
			}
		}

		if (!empty($attachment->{Ja2_Item::STAND_MODIFIERS})) {
			if (empty($attachment->{Ja2_Item::CROUCH_MODIFIERS})) {
				$attachment->{Ja2_Item::CROUCH_MODIFIERS} = $attachment->{Ja2_Item::STAND_MODIFIERS};
			}
		}
		if (!empty($attachment->{Ja2_Item::CROUCH_MODIFIERS})) {
			if (empty($attachment->{Ja2_Item::PRONE_MODIFIERS})) {
				$attachment->{Ja2_Item::PRONE_MODIFIERS} = $attachment->{Ja2_Item::CROUCH_MODIFIERS};
			}
		}

		foreach ([
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
		] as $stance) {
			$stance_data = json_decode($attachment->{$stance}, true);
			if (!is_array($stance_data)) {
				continue;
			}
			foreach ($stance_data as $field => $value) {
				if (array_key_exists($field, $item->{$stance . '_ARRAY'})) {
					$item->{$stance . '_ARRAY'}[$field] += $value;
				} else {
					$item->{$stance . '_ARRAY'}[$field] = $value;
				}
			}
		}
	}

	public static function button_index($weapon_index) {
		$url = Force_URL::current()
			->query_param('id', $weapon_index)
			->get_url();

		return Force_Button::factory($weapon_index)
			->link($url)
			->btn_xs();
	}

} // End Core_Attachment_Data