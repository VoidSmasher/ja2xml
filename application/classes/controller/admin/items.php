<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Items
 * User: legion
 * Date: 01.07.18
 * Time: 3:20
 */
class Controller_Admin_Items extends Controller_Admin_Template {

	use Controller_Common_Weapons_Data;
	use Controller_Common_List_Cell;
	use Controller_Common_List_Changes;

	public function action_index() {
		$this->_weapon_data();

		$builder = Core_Item_Mod::get_weapons_builder()
//			->order_by('calibre_name')
			->order_by('ubCoolness')
			->order_by('szLongItemName');

		$attachments = Core_Attachment_Data::get_attachments_list();
		$attachment_models = Core_Attachment_Data::get_attachments_list_of_models(true);

		$filter = Force_Filter::factory(array(
			Core_Weapon::get_filter_index()
				->where('weapons_mod.uiIndex'),
			Core_Calibre::get_filter_control()
				->where('weapons_mod.ubCalibre'),
			Force_Filter_Select::factory('weapon_type', 'Тип', Core_Weapon::get_type_list())
				->multiple()
				->multiple_reset_button()
				->where('weapons_mod.ubWeaponType'),
			Force_Filter_Select::factory('action', 'Механизм', Core_Weapon_Data::get_mechanism_action_list())
				->multiple()
				->multiple_reset_button()
				->where('data_weapons.mechanism_action'),
			Force_Filter_Select::factory('stock', 'Приклад', Core_Attachment_Data::get_stock_list(true))
				->multiple()
				->multiple_reset_button()
				->where('data_weapons.integrated_stock_index'),
			Force_Filter_Select::factory('coolness', 'Coolness', [
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5,
				6 => 6,
				7 => 7,
				8 => 8,
				9 => 9,
			])
				->where('items_mod.ubCoolness'),
			Core_Attachment_Data::get_filter_control_has_integrated(),
			Core_Weapon::get_filter_control_name()
				->where('items_mod.szLongItemName', 'LIKE'),
		))->apply($builder);

//		$builder->where('PercentNoiseReduction', 'IS NOT', NULL);

		Core_Weapon::check_filter_index($filter);
		Core_Calibre::check_filter_control($filter);
		Core_Weapon::check_filter_control_name($filter);
		Core_Attachment_Data::check_filter_control_has_integrated($filter, $builder);

		$collection = $builder->select_all();

		$list = Force_List::factory()->preset_for_admin();

		$list->button('Weapons', Force_URL::current()
			->controller('weapons')
			->get_url());
		$list->button('Data', Force_URL::current()
			->controller('data_weapons')
			->get_url());
		$list->button('Attachments', Force_URL::current()
			->controller('attachments_weapons')
			->get_url());

		/*
		 * SAVE
		 */
		$this->template->content[] = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'save'),
		])->attribute('id', 'form-save')->render();

		$button_save = Force_Button::factory(__('common.save'))
			->confirmation('Save data?')
			->submit('#form-save');

		if ($changes_count = $this->has_changes($collection, $attachment_models)) {
			$this->_save($builder);

			$button_save
				->btn_danger()
				->popover('Changes', $changes_count, 'left');
		} else {
			$button_save
				->btn_warning()
				->popover('Changes', 'Attachment changes are possible');
		}
		$list->button_html($button_save->render());

		if (Form::is_post()) {
			Request::current()->redirect(Force_URL::current()->get_url());
		}

		/*
		 * DISPLAY FIELDS
		 */
		$show_fields = array();

		foreach ($collection as $model) {
			foreach ($model->meta()->fields() as $field_name => $field_data) {
				if (in_array($field_name, [
					'id',
					'szItemName',
					'szItemDesc',
					'szBRName',
					'szBRDesc',
					'usItemClass',
					'nasAttachmentClass',
					'nasLayoutClass',
					'ubClassIndex',
					'ItemFlag',
					'ubCursor',
					'ubGraphicType',
					'ubGraphicNum',
					'ubWeight',
					'ubPerPocket',

					'usPrice',
					'bReliability',
					'bRepairEase',
					'DamageChance',
					'DirtIncreaseFactor',
//					'Unaerodynamic',
					'DefaultAttachment',

					'ShowStatus',
					'SciFi',
					'BR_ROF',
					'RocketRifle',
					'Attachment',
					'AttachmentClass',
					'ubCoolness',
//					'bReliability',
//					'bRepairEase',
					'Damageable',
					'Repairable',
					'WaterDamages',
					'Metal',
					'Sinks',
					'Electronic',
					'BigGunList',
					'FingerPrintID',
					'spreadPattern',
					'usOverheatingCooldownFactor',
					'BR_NewInventory',
					'BR_UsedInventory',
				])) {
					continue;
				}

				$new_values = array(
					'Unaerodynamic' => Core_Item_Mod::calculate_unaredynamic($model),
				);

				$value_mod = $model->{$field_name};
				if (array_key_exists($field_name, $new_values)) {
					$value_new = $new_values[$field_name];
				} else {
					$value_new = $value_mod;
				}

				if (!empty($value_new)) {
					$show_fields[$field_name] = $field_name;
				}
			}
		}

		$list->column('uiIndex');
		$list->column('szLongItemName');
		$list->column('weapon_image')->label('Image');
		$list->column('rarity');
		$list->column('calibre_name')->label('Calibre');
		$list->column('weapon_qualities')->label('Quality');
		$list->column('integrated_attachments')->label('Integrated Attachments');
		$list->column('integrated_mounts')->label('Integrated Mounts');
		$list->column('TwoHanded')->label('Two hand')->col_number();
		$list->column('ubCoolness_bonus')->label('Cool ness bns')->col_number();
		$list->column('ubCoolness_new')->label('Cool ness new')->col_number();
		$list->column('ubCoolness')->label('Cool ness old')->col_number();
		$list->column('bReliability_new')->label('Relia bility new')->col_number();
		$list->column('bReliability')->label('Relia bility old')->col_number();
		$list->column('bRepairEase_new')->label('Repair Ease new')->col_number();
		$list->column('bRepairEase')->label('Repair Ease old')->col_number();
		$list->column('usOverheatingCooldownFactor_new')->label('Over heating Cooldown Factor new')->col_number();
		$list->column('usOverheatingCooldownFactor')->label('Over heating Cooldown Factor old')->col_number();
		$list->column('DamageChance_bonus')->label('Damage Chance bns')->col_number();
		$list->column('DamageChance_new')->label('Damage Chance new')->col_number();
		$list->column('DamageChance')->label('Damage Chance old')->col_number();
		$list->column('DirtIncreaseFactor_bonus')->label('Dirt Increase Factor bns')->col_number();
		$list->column('DirtIncreaseFactor_new')->label('Dirt Increase Factor new')->col_number();
		$list->column('DirtIncreaseFactor')->label('Dirt Increase Factor old')->col_number();
		$list->column('length_max')->label('length')->col_number();
		$list->column('ItemSize_new')->label('Size new')->col_number();
		$list->column('ItemSize')->label('Size old')->col_number();
		$list->column('weight')->col_number();
//		$list->column('Unaerodynamic');

		foreach ($collection->meta()->fields() as $field_name => $field_data) {
			if (array_key_exists($field_name, $show_fields)) {
				$list->column($field_name);
			}
		}

		$list->apply($collection, null, false)
			->each(function (Model_Item_Mod $model, array $attachments, array $attachment_models) {
				$row = Force_List_Row::factory();
				Bonus::clear();

				/*
				 * BUTTON INDEX
				 */
				$button_index = Core_Weapon::button_index($model->uiIndex);

				$this->changes_for_index($model, $button_index);

				$model->format('uiIndex', $button_index->render());

				/*
				 * IMAGE
				 */
				$model->format('weapon_image', Core_Item::get_image($model->uiIndex));
				$row->cell('weapon_image')->attribute('style', 'background-color:#CA9');

				$model->format('rarity', Core_Weapon_Data::get_rarity_label($model->rarity));

				$button_data = self::get_button_weapon_data($model, $model->szLongItemName, $model->weapon_name);
				$model->format('szLongItemName', $button_data->render());

				$button_two_handed = self::get_button_two_handed($model, $button_data);
				$model->format('TwoHanded', $button_two_handed->render());

				$model->format('weapon_qualities', Weapon::get_quality_labels($model));
				$model->format('integrated_attachments', Core_Attachment_Data::get_integrated_attachment_labels($model));
				$model->format('integrated_mounts', Attachment::get_mount_labels($model));

				Core_Weapon_Data::popover_possible_attachments($attachments, $model, $row, 'integrated_attachments');
				Core_Weapon_Data::popover_possible_attachments($attachments, $model, $row, 'integrated_mounts');

				$size = Core_Item::calculate_size($model);
				$this->cell_duo_new($row, $model, 'ItemSize', $size, '#6495ED');

				$size = Core_Item::calculate_coolness($model, $attachment_models);
				$this->cell_duo_new($row, $model, 'ubCoolness', $size, '#77AD64', true);

				$size = Core_Item::calculate_reliability($model);
				$this->cell_duo_new($row, $model, 'bReliability', $size, '#9564ED');

				$size = Core_Item::calculate_repair_ease($model);
				$this->cell_duo_new($row, $model, 'bRepairEase', $size, '#6495ED');

				$size = Core_Item::calculate_overheating_cooldown($model);
				$this->cell_duo_new($row, $model, 'usOverheatingCooldownFactor', $size, 'red');

				$size = Core_Item::calculate_damage_chance($model);
				$this->cell_duo_new($row, $model, 'DamageChance', $size, 'orange', true);

				$size = Core_Item::calculate_dirt_increase_factor($model);
				$this->cell_duo_new($row, $model, 'DirtIncreaseFactor', $size, 'brown', true);

				$model->format('length_max', round($model->length_max));

				$weight = Core_Weapon_Data::get_weight($model);
				$weight = number_format($weight, 1);
				$model->format('weight', $weight);
				$ubWeight = $model->ubWeight / 10;

				if ($weight != $ubWeight) {
					$row->cell('weight')->attribute('style', 'color:red');
				}

				$new_values = array(
					'Unaerodynamic' => Core_Item_Mod::calculate_unaredynamic($model),
				);

				$labels = array(
					'Unaerodynamic' => Force_Label::factory($model->Unaerodynamic)->preset_boolean_yes_no_hidden(),
					'HiddenAttachment' => Force_Label::factory($model->HiddenAttachment)->preset_boolean_yes_no_hidden(),
					'BlockIronSight' => Force_Label::factory($model->BlockIronSight)->preset_boolean_yes_no_hidden(),
					'HideMuzzleFlash' => Force_Label::factory($model->HideMuzzleFlash)->preset_boolean_yes_no_hidden(),
					'ShowStatus' => Force_Label::factory($model->ShowStatus)->preset_boolean_yes_no_hidden(),
					'Inseparable' => Force_Label::factory($model->Inseparable)->preset_boolean_yes_no_hidden(),
				);

				foreach ($model->meta()->fields() as $field_name => $field_data) {
					if (empty($model->{$field_name}) && !array_key_exists($field_name, $new_values)) {
						continue;
					}

					$label = $field_data->label;
					$value_mod = $model->{$field_name};
					if (array_key_exists($field_name, $new_values)) {
						$value_new = $new_values[$field_name];
					} else {
						$value_new = $value_mod;
					}

					if ($value_new != $value_mod) {
						if (empty($value_new)) {
							$value_new = 0;
						}
						if (empty($value_mod)) {
							$value_mod = 0;
						}

						foreach ($labels as $label_field => $label) {
							if (($label instanceof Force_Label) && ($label_field == $field_name)) {
								if ($value_new) {
									$label->label($value_new)->preset_boolean_yes_no_hidden();
								} else {
									$label->label($value_new)->preset_boolean_yes_no();
								}
								$label->color_red();
							}
						}

						$model->format($field_name, $value_new);

						$data = array();

						switch ($field_name) {
							default:
								$data[] = 'MOD: ' . $value_mod;
								$data[] = 'NEW: ' . $value_new;
								break;
						}

						$data = Helper_String::to_string($data, '<br/>');

						$row->cell($field_name)
							->attribute('style', 'color:red')
							->popover($label, $data);
					}
				}

				foreach ($labels as $label_field => $label) {
					if ($label instanceof Force_Label) {
						$model->format($label_field, $label->render());
					}
				}

				Core_Item::convert_stance_modifiers($model);

				Bonus::clear();
				return $row;
			}, $attachments, $attachment_models);

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	/*
	 * SAVE
	 */

	protected function has_changes(Jelly_Collection $collection, array $attachment_models) {
		foreach ($collection as $model) {
			Bonus::clear();

			$this->set_value($model, 'ItemSize', Core_Item::calculate_size($model));
			$this->set_value($model, 'TwoHanded', (Core_Weapon_Data::is_two_handed($model) == 1) ? 1 : NULL);

			$this->set_value($model, 'Unaerodynamic', Core_Item_Mod::calculate_unaredynamic($model));
			$this->set_value($model, 'ubCoolness', Core_Item::calculate_coolness($model, $attachment_models));

			$this->set_value($model, 'BR_ROF', Core_Weapon_Data::calculate_br_rof($model));

			if (!empty($model->weapon_name)) {
				$this->set_value($model, 'szLongItemName', $model->weapon_name);
				$this->set_value($model, 'szBRName', $model->weapon_name);
			}
			if (!empty($model->weapon_name_short)) {
				$this->set_value($model, 'szItemName', $model->weapon_name_short);
			}
			if (!empty($model->weapon_description)) {
				$this->set_value($model, 'szItemDesc', $model->weapon_description);
			}
			if (!empty($model->weapon_description_br)) {
				$this->set_value($model, 'szBRDesc', $model->weapon_description_br);
			}

			$weight = Core_Weapon_Data::get_weight($model);
			$this->set_value($model, 'ubWeight', round($weight * 10));

			Bonus::clear();
		}

		return $this->get_changes_count();
	}

	protected function _save(Jelly_Builder $builder) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'save') {
			return false;
		}

		$attachments_collection = Core_Attachment_Data::factory()->get_list();
		$attachments = array();
		foreach ($attachments_collection as $attachment_model) {
			$attachments[$attachment_model->uiIndex] = $attachment_model;
		}

		/*
		 * Эти поля будут обнулены перед добавлением бонусов от integrated attachments.
		 */
		$attachment_fields = array(
			'PercentNoiseReduction',
			'HideMuzzleFlash',
			'Bipod',
			'RangeBonus',
			'PercentRangeBonus',
			'ToHitBonus',
			'BestLaserRange',
			'AimBonus',
			'MinRangeForAimBonus',
//			'MagSizeBonus',
//			'RateOfFireBonus',
//			'BulletSpeedBonus',
			'BurstToHitBonus',
			'AutoFireToHitBonus',
//			'APBonus',
			'CamoBonus',
			'UrbanCamoBonus',
			'DesertCamoBonus',
			'SnowCamoBonus',
			'StealthBonus',
			'PercentBurstFireAPReduction',
			'PercentAutofireAPReduction',
			'PercentReadyTimeAPReduction',
			'PercentReloadTimeAPReduction',
			'PercentAPReduction',

			'DamageBonus',
			'MeleeDamageBonus',

			'VisionRangeBonus',
			'NightVisionRangeBonus',
			'DayVisionRangeBonus',
			'CaveVisionRangeBonus',
			'BrightLightVisionRangeBonus',
			'PercentTunnelVision',
			'ScopeMagFactor',
			'ProjectionFactor',
			'RecoilModifierX',
			'RecoilModifierY',
			'PercentRecoilModifier',
			'PercentAccuracyModifier',
			'FlashLightRange',
			'STAND_MODIFIERS',
			'CROUCH_MODIFIERS',
			'PRONE_MODIFIERS',
		);

		$incrementable_fields = array(
			'RangeBonus',
			'PercentRangeBonus',
			'ToHitBonus',
			'AimBonus',
			'BurstToHitBonus',
			'AutoFireToHitBonus',
			'CamoBonus',
			'UrbanCamoBonus',
			'DesertCamoBonus',
			'SnowCamoBonus',
			'StealthBonus',
			'PercentBurstFireAPReduction',
			'PercentAutofireAPReduction',
			'PercentReadyTimeAPReduction',
			'PercentReloadTimeAPReduction',
			'PercentAPReduction',

			'DamageBonus',
			'MeleeDamageBonus',

			'VisionRangeBonus',
			'NightVisionRangeBonus',
			'DayVisionRangeBonus',
			'CaveVisionRangeBonus',
			'BrightLightVisionRangeBonus',
			'RecoilModifierX',
			'RecoilModifierY',
			'PercentRecoilModifier',
			'PercentAccuracyModifier',
		);

		$items = $builder->select_all();

		foreach ($items as $model) {
			if ($model->nasAttachmentClass != Core_Item::NAS_ATTACHMENT_CLASS_UNDER_BARREL) {
				foreach ($attachment_fields as $attachment_field) {
					$model->{$attachment_field} = NULL;
				}

				Core_Attachment_Data::stack_integrated_attachments($model, $attachments, $attachment_fields, $incrementable_fields);
			}

			$this->save_changes($model);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Admin_Items