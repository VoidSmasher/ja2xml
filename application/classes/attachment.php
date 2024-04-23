<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Attachment
 * User: legion
 * Date: 04.01.2020
 * Time: 2:05
 */
class Attachment extends Attachment_Bonus {

	private static $instance;

	public function __construct() {
		$this->init_bonus();
		$this->init_mount();
		$this->init_type();
	}

	/**
	 * @return Attachment
	 */
	public static function instance() {
		if (!(self::$instance instanceof Attachment)) {
			self::$instance = new Attachment();
		}
		return self::$instance;
	}

	public static function check_item(Jelly_Model $item, Jelly_Model $attachment, array $forbidden_nasAttachmentClasses = array()) {
		/*
		 * Проверка запрещённых классов
		 * Например default_attachments с флагом Inseparable
		 */
		if (array_key_exists($attachment->nasAttachmentClass, $forbidden_nasAttachmentClasses)) {
			return false;
		}

		if ($attachment instanceof Model_Attachment_Data) {
			if ($item->uiIndex == $attachment->uiIndex) {
				return false;
			}
		} elseif ($attachment instanceof Model_Attachment_Mod || $attachment instanceof Model_Attachment) {
			if ($item->uiIndex == $attachment->attachmentIndex) {
				return false;
			}
		}

		/*
		 * Mounts
		 */
		$item_mounts = Attachment::get_external_mounts($item);
		$result = false;
		$attachment_mounts = Attachment::get_mounts($attachment);
		foreach ($item_mounts as $mount) {
			if (array_key_exists($mount, $attachment_mounts)) {
				$result = true;
				break;
			}
		}
		if (!$result) {
			return false;
		}

		return true;
	}

	/**
	 * @param Model_Weapon_Group $item
	 * @param Model_Attachment_Data $attachment
	 * @param array $forbidden_nasAttachmentClasses
	 * @return bool
	 */
	public static function check_weapon(Model_Weapon_Group $item, Model_Attachment_Data $attachment, array $forbidden_nasAttachmentClasses = array()) {
		/*
		 * Проверка запрещённых классов
		 * Например default_attachments с флагом Inseparable
		 */
		if (array_key_exists($attachment->nasAttachmentClass, $forbidden_nasAttachmentClasses)) {
			return false;
		}

		$attachment_types = Attachment::get_types($attachment);
		$attachment_bonuses = Attachment::get_bonuses($attachment);

		/*
		 * Mounts
		 */
		$weapon_mounts = Attachment::get_mounts($item);
		$result = false;
		$attachment_mounts = Attachment::get_mounts($attachment);
		if (!array_key_exists(Attachment::TYPE_INTERNAL, $attachment_types)
			&& !array_key_exists(Attachment::TYPE_MAG_ADAPTER, $attachment_types)) {
			foreach ($weapon_mounts as $mount) {
				if (array_key_exists($mount, $attachment_mounts)) {
					$result = true;
					break;
				}
			}
			if (!$result) {
				return false;
			}
		}

		/*
		 * Проверка на nasAttachmentClass
		 */
		switch ($attachment->nasAttachmentClass) {
			case Core_Item::NAS_ATTACHMENT_CLASS_STOCK:
				if (!empty($item->integrated_stock_index)) {
					return false;
				}
				break;
			case Core_Item::NAS_ATTACHMENT_CLASS_SCOPE:
				if (!empty($item->integrated_scope_index)) {
					return false;
				}
				break;
			case Core_Item::NAS_ATTACHMENT_CLASS_SIGHT:
				if (!empty($item->integrated_sight_index)) {
					return false;
				}
				break;
			case Core_Item::NAS_ATTACHMENT_CLASS_LASER:
				if (!empty($item->integrated_laser_index)) {
					return false;
				}
				break;
			case Core_Item::NAS_ATTACHMENT_CLASS_MUZZLE:
				if ($item->integrated_suppressor_index == Core_Attachment_Data::INDEX_FlASH_SUPPRESSOR) {
					if ($attachment->uiIndex == Core_Attachment_Data::INDEX_FlASH_SUPPRESSOR) {
						return false;
					}
				} elseif (!empty($item->integrated_suppressor_index)) {
					return false;
				}
				break;
		}

		if (array_key_exists(Attachment::TYPE_UNDER_BARREL_WEAPON, $attachment_types)) {
			if (!empty($item->integrated_foregrip_index) && !in_array($item->integrated_foregrip_index, [
					Core_Attachment_Data::INDEX_FOREGRIP_MAG,
					Core_Attachment_Data::INDEX_FOREGRIP_ANGLED,
				])
			) {
				return false;
			}
		}

		/*
		 * Проверяем доступность прицелов
		 * Не имеет смысла ставить мощные прицелы на оружие не имеющее возможности их реализовать
		 */
		if (array_key_exists(Attachment::TYPE_SCOPE, $attachment_types)
			|| array_key_exists(Attachment::TYPE_SIGHT, $attachment_types)) {
//			$min_range = Helper::round_to_five($attachment->MinRangeForAimBonus * 5.8);
//			if ($min_range > $item->usRange) {
//				return false;
//			}

			if ($item->has_hp_iron_sights) {
				if (array_key_exists(Attachment::BONUS_SCOPE_LOW_PROFILE, $attachment_bonuses)
					|| array_key_exists(Attachment::BONUS_SIGHT_LOW_PROFILE, $attachment_bonuses)) {
					return false;
				}
			}

			if (array_key_exists(Attachment::TYPE_SCOPE, $attachment_types)) {
				switch ($item->bullet_type) {
					case Core_Calibre::TYPE_PISTOL:
						if (!array_key_exists(Attachment::TYPE_AMMO_PISTOL, $attachment_types)) {
							return false;
						}
						break;
					case Core_Calibre::TYPE_SHOTGUN:
						if (!array_key_exists(Attachment::TYPE_AMMO_SHOTGUN, $attachment_types)) {
							return false;
						}
						break;
					case Core_Calibre::TYPE_ROCKET:
						if (!array_key_exists(Attachment::TYPE_AMMO_ROCKET, $attachment_types)) {
							return false;
						}
						break;
				}
			}

			switch ($item->ubWeaponType) {
				case Core_Weapon::TYPE_PISTOL:
					if (!array_key_exists(Attachment::TYPE_PISTOL, $attachment_types)) {
						return false;
					}
					break;
				case Core_Weapon::TYPE_MP:
					if (!array_key_exists(Attachment::TYPE_MP, $attachment_types)) {
						return false;
					}
					break;
				case Core_Weapon::TYPE_SHOTGUN:
					if (!array_key_exists(Attachment::TYPE_SHOTGUN, $attachment_types)) {
						return false;
					}
					break;
				case Core_Weapon::TYPE_SMG:
					if (!array_key_exists(Attachment::TYPE_SMG, $attachment_types)) {
						return false;
					}
					break;
				case Core_Weapon::TYPE_AR:
				case Core_Weapon::TYPE_RIFLE:
					if (!array_key_exists(Attachment::TYPE_RIFLE, $attachment_types)) {
						return false;
					}
					break;
				case Core_Weapon::TYPE_MACHINEGUN:
					if (!array_key_exists(Attachment::TYPE_MACHINEGUN, $attachment_types)) {
						return false;
					}
					break;
				case Core_Weapon::TYPE_SNIPER:
					if (!array_key_exists(Attachment::TYPE_SNIPER, $attachment_types)) {
						return false;
					}
					break;
			}
		}

		/*
		 * Возможность установки триггера для стрельбы очередями
		 */
		if (array_key_exists(Attachment::TYPE_TRIGGER_BURST, $attachment_types)) {
			if (!$item->has_hk_trigger) {
				return false;
			}
			if (!$item->is_burst_fire_possible()) {
				return false;
			}
			if ($item->has_burst_fire()) {
				return false;
			}
		}

		/*
		 * Отсекаем аттачи требующие наличия двуручного оружия
		 */
		if (array_key_exists(Attachment::TYPE_TWO_HANDED, $attachment_types)) {
			if (!$item->is_two_handed) {
				return false;
			}
		}

		/*
		 * Отсекаем аттачи требующие наличия одиночного огня
		 */
		if (array_key_exists(Attachment::TYPE_SEMI_AUTO, $attachment_types)) {
			if ($item->no_semi_auto) {
				return false;
			}
		}

		/*
		 * Отсекаем аттачи требующие наличия автоматического огня
		 */
		if (array_key_exists(Attachment::TYPE_AUTOMATIC, $attachment_types)) {
			if (!$item->is_automatic() && !$item->is_burst_fire_possible()) {
				return false;
			}
		}

		/*
		 * Rod & Spring
		 */
		if (array_key_exists(Attachment::TYPE_INTERNAL, $attachment_types)) {
			if (array_key_exists(Attachment::TYPE_AUTOMATIC, $attachment_types)) {
				if (!$item->is_automatic()) {
					return false;
				}
				/*
				 * Отсеиваем по механизму действия
				 */
				switch ($item->mechanism_action) {
					case Core_Weapon_Data::ACTION_PUMP:
					case Core_Weapon_Data::ACTION_BOLT:
					case Core_Weapon_Data::ACTION_LEVER:
					case Core_Weapon_Data::ACTION_BREAK:
					case Core_Weapon_Data::ACTION_METAL_STORM:
						return false;
						break;
				}
				/*
				 * Отсеиваем по особенностям автоматики
				 */
				switch ($item->mechanism_feature) {
					case Core_Weapon_Data::FEATURE_ROTATING_BREECH:
					case Core_Weapon_Data::FEATURE_ROTARY_FIRING_PIN:
					case Core_Weapon_Data::FEATURE_STRAIGHT_PULL_BOLT:
					case Core_Weapon_Data::FEATURE_STRAIGHT_PULL_GRIP:
						return false;
						break;
				}
			}
		}

		/*
		 * Возможность установки дисковых магазинов
		 */
		if (array_key_exists(Attachment::TYPE_MAG_ADAPTER, $attachment_types)) {
			if (!$item->is_automatic()) {
				return false;
			}
			/*
			 * Отсеиваем встроенные магазины и внезапно снайперские стволы ибо нефиг.
			 */
			if ($item->has_drum_mag || $item->has_calico_mag || $item->has_sniper_barrel) {
				return false;
			}
			switch ($item->ubWeaponType) {
				case Core_Weapon::TYPE_PISTOL:
				case Core_Weapon::TYPE_MP:
				case Core_Weapon::TYPE_MACHINEGUN:
				case Core_Weapon::TYPE_SNIPER:
					return false;
			}
			switch ($item->integrated_stock_index) {
				case Core_Attachment_Data::INDEX_STOCK_BULLPUP:
					return false;
			}
			/*
			 * Проверка на соответствие калибру, тут всего три варианта
			 * @todo Стоит переделать, чтобы не указывать калибр напрямую, а сравнивать калибр оружия и аттача.
			 * А вот проверка на has_mag_stanag более изощрённая. Не в каждое оружие полезет такой магазин.
			 * Параметр has_mag_stanag отсеивает оружие технически не приспособленное для дисковых магазинов.
			 */
			if (array_key_exists(Attachment::TYPE_762x39, $attachment_types)) {
				if ($item->ubCalibre != Core_Calibre::CALIBRE_762_39) {
					return false;
				}
				if ($item->has_mag_stanag) {
					return false;
				}
			}
			if (array_key_exists(Attachment::TYPE_556x45, $attachment_types)) {
				if ($item->ubCalibre != Core_Calibre::CALIBRE_556_45) {
					return false;
				}
				if (!$item->has_mag_stanag) {
					return false;
				}
			}
			if (array_key_exists(Attachment::TYPE_9x19, $attachment_types)) {
				if ($item->ubCalibre != Core_Calibre::CALIBRE_9_19) {
					return false;
				}
				if (!$item->has_mag_stanag) {
					return false;
				}
			}
		}

		/*
		 * Глушители
		 * Ограничения по типам пуль и оружия
		 */
		if (array_key_exists(Attachment::TYPE_SUPPRESSOR, $attachment_types)) {
			switch ($item->ubWeaponType) {
				case Core_Weapon::TYPE_SNIPER:
					if (!array_key_exists(Attachment::TYPE_SNIPER, $attachment_types)) {
						return false;
					}
					break;
				default:
					switch ($item->bullet_type) {
						case Core_Calibre::TYPE_PISTOL:
							if (!array_key_exists(Attachment::TYPE_PISTOL, $attachment_types)) {
								return false;
							}
							break;
						case Core_Calibre::TYPE_PISTOL_LONG:
						case Core_Calibre::TYPE_RIFLE:
						case Core_Calibre::TYPE_RIFLE_ADVANCED:
						case Core_Calibre::TYPE_SNIPER:
							if (Core_Weapon_Data::is_two_handed($item)) {
								if (!array_key_exists(Attachment::TYPE_RIFLE, $attachment_types)) {
									return false;
								}
							} else {
								if (!array_key_exists(Attachment::TYPE_PISTOL, $attachment_types)) {
									return false;
								}
							}
							break;
						case Core_Calibre::TYPE_SHOTGUN:
							if (!array_key_exists(Attachment::TYPE_SHOTGUN, $attachment_types)) {
								return false;
							}
							break;
					}
			}
		}

		if (array_key_exists(Attachment::TYPE_UNDER_BARREL_WEAPON, $attachment_types)) {
			switch ($item->ubWeaponType) {
				case Core_Weapon::TYPE_PISTOL:
				case Core_Weapon::TYPE_MP:
				case Core_Weapon::TYPE_SNIPER:
					return false;
			}
			if (array_key_exists(Attachment::TYPE_LONG, $attachment_types)) {
				if ($item->has_drum_mag) {
					return false;
				}
			}
		}

		if (array_key_exists(Attachment::TYPE_BIPOD, $attachment_types)) {
			if (!empty($item->integrated_bipod_index)) {
				return false;
			}
			if ($item->has_long_mag) {
				return false;
			}
			/*
			 * Запрет установки сошек на рельсы
			 * Если только слот не указан напрямую
			 */
//			if (!array_key_exists(Attachment::MOUNT_BIPOD, $weapon_mounts)) {
//				switch ($item->ubWeaponType) {
//					case Core_Weapon::TYPE_PISTOL:
//					case Core_Weapon::TYPE_MP:
//					case Core_Weapon::TYPE_SMG:
//						return false;
//				}
//			}
		}

		if (array_key_exists(Attachment::TYPE_LASER, $attachment_types)) {
			if (!empty($item->integrated_laser_index)) {
				return false;
			}
			if (array_key_exists(Attachment::TYPE_BATTERIES, $attachment_types)) {
				if (array_key_exists(Attachment::MOUNT_STOCK_FOLDING, $weapon_mounts)) {
					return false;
				}
				if (array_key_exists(Attachment::MOUNT_STOCK_RETRACTABLE, $weapon_mounts)) {
					return false;
				}
			}
		}

		if (array_key_exists(Attachment::MOUNT_STOCK_FOLDING, $attachment_mounts)) {
			if ($item->has_recoil_buffer_in_stock) {
				return false;
			}
		}

		if (array_key_exists(Attachment::TYPE_FLASHLIGHT, $attachment_types)) {
			switch ($item->ubWeaponType) {
				case Core_Weapon::TYPE_SNIPER:
					return false;
			}
		}

		if (array_key_exists(Attachment::TYPE_BARREL_EXTENDER, $attachment_types)) {
			/*
			 * Запрет установки Barrel Extender вместо глушителя или пламегасителя
			 * Если только слот не указан напрямую
			 */
			if (!array_key_exists(Attachment::MOUNT_BARREL_EXTENDER, $weapon_mounts)) {
				if (!Core_Weapon_Data::is_two_handed($item)) {
					return false;
				}
				if ($item->has_sniper_barrel) {
					return false;
				}
				switch ($item->ubWeaponType) {
					case Core_Weapon::TYPE_PISTOL:
					case Core_Weapon::TYPE_MP:
					case Core_Weapon::TYPE_MACHINEGUN:
					case Core_Weapon::TYPE_SNIPER:
						return false;
					case Core_Weapon::TYPE_SHOTGUN:
						if ($item->length_max > 950) {
							return false;
						}
						break;
					default:
						if ($item->length_max > 850) {
							return false;
						}
				}
			}
		}

		return true;
	}

	/**
	 * @param Model_Weapon_Group $model
	 * @param array $attachment_models
	 * @return DTO_Attachments_Info
	 */
	public static function get_info(Model_Weapon_Group $model, array &$attachment_models) {
		if ($model->attachments_info instanceof DTO_Attachments_Info) {
			return $model->attachments_info;
		}

		/*
		 * При определении coolness необходимо смотреть на аттачи, а не на маунты.
		 */
		$possible_attachments = Core_Weapon_Data::get_possible_attachments($model);
		$possible_attachments_all = $possible_attachments;
		$possible_attachments_of_attachments = array();

		$attachments_info = new DTO_Attachments_Info();

		/*
		 * Attachments of Attachments (one layer)
		 */
		foreach ($possible_attachments as $attachment_id => $ap_cost) {
			$attach = Arr::get($attachment_models, $attachment_id);

			if (!($attach instanceof Model_Attachment_Data)) {
				continue;
			}

			$attach_attachments = Core_Weapon_Data::get_possible_attachments($attach);

			$possible_attachments_of_attachments += $attach_attachments;
		}

		/*
		 * Check Attachments of Attachments (one layer)
		 */
		foreach ($possible_attachments_of_attachments as $attachment_id => $ap_cost) {
			$attach = Arr::get($attachment_models, $attachment_id);

			if (!($attach instanceof Model_Attachment_Data)) {
				continue;
			}

			if ($attach->ScopeMagFactor > 1) {
				$attachments_info->sight_has_scope = true;
			}
			if (Attachment::has_type($attach, Attachment::TYPE_SIGHT)) {
				$attachments_info->scope_has_sight = true;
			}
		}

		$possible_attachments_all += $possible_attachments_of_attachments;

		/*
		 * Check all (two layers) possible attachments
		 */
		foreach ($possible_attachments_all as $attachment_id => $ap_cost) {
			$attach = Arr::get($attachment_models, $attachment_id);

			if (!($attach instanceof Model_Attachment_Data)) {
				continue;
			}

			if (Attachment::has_type($attach, Attachment::TYPE_SCOPE)) {
				if ($attach->ScopeMagFactor > $attachments_info->max_scope_magnitude) {
					$attachments_info->max_scope_magnitude = $attach->ScopeMagFactor;
				}
			}
			if (!$attachments_info->has_sight && Attachment::has_type($attach, Attachment::TYPE_SIGHT)) {
				$attachments_info->has_sight = true;
			}
			if (Attachment::has_type($attach, Attachment::TYPE_LASER)
				&& !Attachment::has_type($attach, Attachment::TYPE_SIGHT)
				&& !Attachment::has_type($attach, Attachment::TYPE_SCOPE)) {
				$attachments_info->has_laser = true;
				if (!$attachments_info->has_rifle_laser
					&& Attachment::has_type($attach, Attachment::TYPE_RIFLE)
					&& !Attachment::has_type($attach, Attachment::TYPE_PISTOL)) {
					$attachments_info->has_rifle_laser = true;
				}
			}
			if (!$attachments_info->has_grippod && Attachment::has_type($attach, Attachment::TYPE_BIPOD)
				&& Attachment::has_type($attach, Attachment::TYPE_FOREGRIP)) {
				$attachments_info->has_grippod = true;
			}
			if (!$attachments_info->has_bipod && Attachment::has_type($attach, Attachment::TYPE_BIPOD)
				&& !Attachment::has_type($attach, Attachment::TYPE_FOREGRIP)) {
				$attachments_info->has_bipod = true;
			}
			if (!$attachments_info->has_foregrip && Attachment::has_type($attach, Attachment::TYPE_FOREGRIP)
				&& !Attachment::has_type($attach, Attachment::TYPE_BIPOD)) {
				$attachments_info->has_foregrip = true;
			}
			if (!$attachments_info->has_suppressor && Attachment::has_type($attach, Attachment::TYPE_SUPPRESSOR_SOUND)) {
				$attachments_info->has_suppressor = true;
				$attachments_info->suppressor_effectiveness = $attach->PercentNoiseReduction;
			}
			if (!$attachments_info->has_flash_hider && Attachment::has_type($attach, Attachment::TYPE_SUPPRESSOR_FLASH)) {
				$attachments_info->has_flash_hider = true;
			}
			if (!$attachments_info->has_secondary_weapon && Attachment::has_type($attach, Attachment::TYPE_SECONDARY_WEAPON)) {
				$attachments_info->has_secondary_weapon = true;

				if (!$attachments_info->has_multi_charge_gl && Attachment::has_type($attach, Attachment::TYPE_MULTI_CHARGE_GL)) {
					$attachments_info->has_multi_charge_gl = true;
				}
				if (!$attachments_info->has_integral_secondary_weapon && Attachment::has_type($attach, Attachment::TYPE_INTEGRAL)) {
					$attachments_info->has_integral_secondary_weapon = true;
				}
				if (!$attachments_info->has_under_barrel_weapon && Attachment::has_type($attach, Attachment::TYPE_UNDER_BARREL_WEAPON)) {
					$attachments_info->has_under_barrel_weapon = true;
				}
				if (!$attachments_info->has_above_barrel_weapon && Attachment::has_type($attach, Attachment::TYPE_ABOVE_BARREL_WEAPON)) {
					$attachments_info->has_above_barrel_weapon = true;
				}
			}
		}

		if (!$attachments_info->max_scope_magnitude && !empty($model->integrated_scope_index)) {
			$attach = Arr::get($attachment_models, $model->integrated_scope_index);
			if (($attach instanceof Model_Attachment_Data) && Attachment::has_type($attach, Attachment::TYPE_SCOPE)) {
				$attachments_info->has_integral_scope = true;
				$attachments_info->max_scope_magnitude = $attach->ScopeMagFactor;
			}
		}

		if (!$attachments_info->has_sight && !empty($model->integrated_sight_index)) {
			$attach = Arr::get($attachment_models, $model->integrated_sight_index);
			if (($attach instanceof Model_Attachment_Data) && Attachment::has_type($attach, Attachment::TYPE_SIGHT)) {
				$attachments_info->has_sight = true;
			}
		}

		if (!$attachments_info->has_bipod && !empty($model->integrated_bipod_index)) {
			$attach = Arr::get($attachment_models, $model->integrated_bipod_index);
			if (($attach instanceof Model_Attachment_Data) && Attachment::has_type($attach, Attachment::TYPE_BIPOD)) {
				$attachments_info->has_bipod = true;
			}
		}

		if (!$attachments_info->has_foregrip && !empty($model->integrated_foregrip_index)) {
			$attach = Arr::get($attachment_models, $model->integrated_foregrip_index);
			if (($attach instanceof Model_Attachment_Data) && Attachment::has_type($attach, Attachment::TYPE_FOREGRIP)) {
				$attachments_info->has_foregrip = true;
			}
		}

		if (!empty($model->integrated_laser_index)) {
			$attach = Arr::get($attachment_models, $model->integrated_laser_index);
			if (($attach instanceof Model_Attachment_Data) && Attachment::has_type($attach, Attachment::TYPE_LASER)) {
				$attachments_info->has_integral_laser = true;
			}
		}

		if (!empty($model->integrated_suppressor_index)) {
			$attach = Arr::get($attachment_models, $model->integrated_suppressor_index);
			if ($attach instanceof Model_Attachment_Data) {
				if (Attachment::has_type($attach, Attachment::TYPE_SUPPRESSOR_SOUND)) {
					$attachments_info->has_integral_suppressor = true;
				} elseif (Attachment::has_type($attach, Attachment::TYPE_SUPPRESSOR_FLASH)) {
					$attachments_info->has_integral_flash_hider = true;
				}
			}
		}

		if ($attachments_info->max_scope_magnitude > 1) {
			$attachments_info->has_scope = true;
		}

		return $model->attachments_info = $attachments_info;
	}

} // End Attachment
