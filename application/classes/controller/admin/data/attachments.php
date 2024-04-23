<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Data_Attachments
 * User: legion
 * Date: 13.10.19
 * Time: 2:21
 */
class Controller_Admin_Data_Attachments extends Controller_Admin_Template {

	use Controller_Common_Attachments_Data;
	use Controller_Common_Attachments_Type;
	use Controller_Common_Mounts_External;
	use Controller_Common_List_Cell;
	use Controller_Common_List_Changes;

	protected static $skip_fields = [
		'id',
		'uiIndex',
		'APCost',
		'is_fixed',
		'is_integrated',
		'attachment_types',
		'attachment_mounts',
		'attachment_bonuses',
	];

	public function action_index() {
		self::_attachment_data();
		self::_attachment_type();
		self::_mounts_external();

		$builder = Core_Attachment_Data::get_attachments_builder()
			->order_by('ScopeMagFactor', 'DESC')
			->order_by('AimBonus', 'DESC')
			->order_by('DayVisionRangeBonus', 'DESC');

		$mounts_list = Attachment::instance()->get_mount_list();

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('id'),
			Force_Filter_Input::factory('name')
				->where('szLongItemName', 'LIKE'),
			Force_Filter_Select::factory('type', 'Type', Attachment::get_types_menu())
				->where('attachment_types', 'LIKE'),
//			Force_Filter_Select::factory('class', 'Class', Core_Slot_Mod::get_weapon_nas_attachment_classes())
//				->where('nasAttachmentClass'),
			Force_Filter_Select::factory('mounts', 'Mounts', $mounts_list)
				->where('data_attachments.attachment_mounts', 'LIKE'),
			Force_Filter_Select::factory('integral', 'Integral', [
				1 => 'Hide Integral Attachments',
				2 => 'Show Integral Attachments',
			])
		))->apply($builder);

		$integral = $filter->get_value('integral');
		switch ($integral) {
			case 1:
				$builder->where('uiIndex', '>', 0);
				break;
			case 2:
				$builder->where('uiIndex', '<', 0);
				break;
		}

		Core_Item::apply_filter_by_id($filter, $builder);

		$collection = $builder->group_by('uiIndex')->select_all();

		$show_fields = array();
		$show_fields_last = array();

		foreach ($collection as $model) {

			$model_mod = clone $model;

			Attachment::calculate_bonuses($model_mod);

			foreach ($model->meta()->fields() as $field_name => $field_data) {
				if (Core_Item::check_field_merge_status($field_data)) {
					if (!empty($model->{$field_name})) {
						$show_fields[$field_name] = $field_name;
					} elseif (!empty($model->{'item_' . $field_name})) {
						$show_fields[$field_name] = $field_name;
					}
					if (!empty($model_mod->{$field_name})) {
						$show_fields[$field_name] = $field_name;
					}
				} elseif (Core_Item::check_field_save_status($field_data)) {
					$show_fields_last[$field_name] = $field_name;
				}
			}
		}

		$list = Force_List::factory()->preset_for_admin()
			->title('Attachments Data');

		/*
		 * SAVE
		 */
		if ($changes_count = $this->has_changes($collection)) {
			$this->_save($collection);

			$button_save = Force_Button::factory(__('common.save'))
				->btn_danger()
				->confirmation('Save data?')
				->submit('#form-save')
				->popover('Changes', $changes_count, 'left');

			$this->template->content[] = Force_Form::factory([
				Force_Form_Hidden::factory('action', 'save'),
			])->attribute('id', 'form-save')->render();
		} else {
			$button_save = Force_Button::factory(__('common.save'))
				->btn_disabled();
		}
		$list->button_html($button_save->render());

		/*
		 * COOLNESS
		 */
		$this->_calculate_coolness($collection);

		$button_coolness = Force_Button::factory('Calculate Coolness')
			->btn_warning()
			->confirmation('Calculate coolness?')
			->submit('#form-coolness');

		$this->template->content[] = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'coolness'),
		])->attribute('id', 'form-coolness')->render();

		$list->button_html($button_coolness->render());

		if (Form::is_post()) {
			Request::current()->redirect(Force_URL::current()->get_url());
		}

		/*
		 * IMPORT
		 */
		$list->button(Force_Button::factory('Import')
			->link(Force_URL::current_clean()->action('import')));

		/*
		 * LIST
		 */
		$list->column('uiIndex');
		$list->column('image');
		$list->column('szLongItemName')->col_main();
		$list->column('bonuses_button')->button_place();
		$list->column('bonuses');
		$list->column('attachment_mounts');
		$list->column('attachment_mounts_external_button')->button_place();
		$list->column('attachment_mounts_external');
		$list->column('attachment_types_button')->button_place();
		$list->column('attachment_types')->col_width('320px');
		$list->column('ubWeight');
//		$list->column('ubCoolness_bonus')->label('Cool ness bns')->col_control();
		$list->column('ubCoolness')->label('Cool ness mod')->col_control();
		$list->column('ubCoolness_original')->label('Cool ness orig')->col_control();
		$list->column('min_range');
//		$list->column('AttachmentClass');
//		$list->column('nasAttachmentClass');

		/*
		 * Сортировка полей
		 */
		foreach ($collection->meta()->fields() as $field_name => $field_data) {
			if (array_key_exists($field_name, $show_fields)) {
				$list->column($field_name);
			}
		}
		foreach ($collection->meta()->fields() as $field_name => $field_data) {
			if (in_array($field_name, [
				'szItemName',
				'szLongItemName',
				'szItemDesc',
				'szBRName',
				'szBRDesc',
			])) {
				continue;
			}
			if (array_key_exists($field_name, $show_fields_last)) {
				$list->column($field_name);
			}
		}

		/*
		 * LIST APPLY
		 */
		$list->apply($collection, null, false)
//			->button_add()
			->each(function (Model_Attachment_Data $model) {
				$row = Force_List_Row::factory();
				Bonus::clear();

				/*
				 * BUTTON INDEX
				 */
				$button_index = Core_Attachment_Data::button_index($model->uiIndex);

				$this->changes_for_index($model, $button_index, 'item_');

				$model->format('uiIndex', $button_index->render());

				$button_data = self::get_button_attachment_data($model);
				$model->format('bonuses_button', $button_data->render());

				$button_data = self::get_button_mounts_external($model);
				$model->format('attachment_mounts_external_button', $button_data->render());

				$button_data = self::get_button_attachment_type($model);
				$model->format('attachment_types_button', $button_data->render());

				$model->format('min_range', Helper::round_to_five($model->MinRangeForAimBonus * 7));

				Core_Item::row_image($model, $row);

				$model->format('ubWeight', Core_Attachment_Data::get_weight($model));

				$edit_link = Force_URL::current_clean()
					->action('edit')
					->route_param('id', $model->id)
					->back_url()
					->get_url();

				if (!empty($model->szLongItemName)) {
					$attachment_name = $model->szLongItemName;
				} else {
					$attachment_name = $model->item_szLongItemName;
				}

				$edit_button = Force_Button::factory($attachment_name)
					->link($edit_link)
					->btn_sm()
					->btn_primary();

				$bonuses = Attachment::get_bonuses($model);

				$bonus_labels = array();
				foreach ($bonuses as $bonus) {
					$bonus_labels[] = Attachment::get_bonus_label($bonus);
				}
				if ($model->depends_on_items) {
					$bonus_labels[] = Force_Label::factory('Depends on: ' . $model->depends_on_items)
						->color_red()
						->render();
				}

				Attachment::calculate_bonuses($model);

				$model->format('bonuses', implode(' ', $bonus_labels));
				$model->format('attachment_mounts', Attachment::get_mount_labels($model));
				$model->format('attachment_mounts_external', Attachment::get_external_mount_labels($model));
				$model->format('attachment_types', Attachment::get_type_labels($model));

				$this->cell_duo_original($row, $model, 'ubCoolness', '#77AD64');

				$labels = array(
					'AttachmentClass' => Core_Item::get_AttachmentClass_label($model->AttachmentClass),
					'nasAttachmentClass' => Core_Item::get_nasAttachmentClass_label($model->nasAttachmentClass),
					'nasLayoutClass' => Core_Item::get_nasLayoutClass_label($model->nasLayoutClass),
					'is_integrated' => Force_Label::factory($model->is_integrated)->preset_boolean_yes_no_hidden(),
					'is_fixed' => Force_Label::factory($model->is_fixed)->preset_boolean_yes_no_hidden(),
					'AttachmentSystem' => Force_Label::factory($model->AttachmentSystem)->preset_boolean_yes_no_hidden(),
				);

				$this->check_for_changes($row, $model, $edit_button, $labels);

				$model->format('szLongItemName', $edit_button->render());
				Core_Item::convert_stance_modifiers($model);

				Bonus::clear();
				return $row;
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Attachment_Data::factory()->create();
		$this->_form($model, false);
	}

	/**
	 * @throws HTTP_Exception_404
	 * @throws Kohana_Exception
	 */
	public function action_edit() {
		$id = $this->request->param('id');

		$model = Core_Attachment_Data::get_attachments_builder()
			->join('items', 'LEFT')->on('items.uiIndex', '=', 'data_attachments.uiIndex')
			->select_column('items.szItemName', 'original_szItemName')
			->select_column('items.szLongItemName', 'original_szLongItemName')
			->select_column('items.szBRName', 'original_szBRName')
			->select_column('items.szItemDesc', 'original_szItemDesc')
			->select_column('items.szBRDesc', 'original_szBRDesc')
			->where('id', '=', $id)
			->limit(1)
			->select();

		if (!$model->loaded()) {
			throw new HTTP_Exception_404();
		}

		$this->_form($model, true);
	}

	public function _form($model, $is_edit = true) {
		$attachment_classes = Core_Class_Attachment::factory()->get_list_as_array();
		$nas_attachment_classes = Core_Class_NasAttachment::factory()->get_list_as_array();
		$nas_layout_classes = Core_Class_NasLayout::factory()->get_list_as_array();

		$form = Jelly_Form::factory($model)->preset_for_admin();

		if (!$is_edit) {
			$form->control = Force_Form_Input::factory('uiIndex');
		}

//		function get_control(Jelly_Model $model, Force_Form_Section $section, $field, $label = null) {
//			$field_data = $model->meta()->field($field);
//			if (!($field_data instanceof Jelly_Field)) {
//				return false;
//			}
//
//			$field_status = Core_Item::get_field_save_status($field_data);
//
//			switch ($field_status) {
//				case Core_Item::FIELD_OVERWRITE_NOT_EMPTY:
//					$section->control = Force_Form_Show_Value::factory($label)->value($model->{'item_' . $field});
//					break;
//			}
//
//			if ($field_data instanceof Jelly_Field_Float) {
//				$control = Force_Form_Float::factory($field, $label);
//			} elseif ($field_data instanceof Jelly_Field_Text) {
//				$control = Force_Form_Textarea::factory($field, $label);
//			} else {
//				$control = Force_Form_Input::factory($field, $label);
//			}
//
//			$section->control = $control;
//			return true;
//		}

		$name_data = array(
			array(
				'version' => '<b>ORIGINAL</b>',
				'szItemName' => $model->original_szItemName,
				'szLongItemName' => $model->original_szLongItemName,
				'szBRName' => $model->original_szBRName,
			),
			array(
				'version' => '<b>MOD</b>',
				'szItemName' => $model->item_szItemName,
				'szLongItemName' => $model->item_szLongItemName,
				'szBRName' => $model->item_szBRName,
			),
		);
		$name_list = Force_List::factory([
			Force_List_Column::factory('version')->button_place()->col_right(),
			'szItemName',
			'szLongItemName',
			'szBRName',
		])->apply($name_data);

		$form->control = Force_Form_Section::factory('Weapon Name', [
			Force_Form_HTML::factory($name_list->render()),
			Force_Form_Input::factory('szItemName', 'Новое короткое имя'),
			Force_Form_Input::factory('szLongItemName', 'Новое длинное имя'),
		]);

		$form->control = Force_Form_Section::factory('szItemDesc', [
			Force_Form_Show_Value::factory('ORIGINAL')->value($model->original_szItemDesc),
			Force_Form_Show_Value::factory('MOD')->value($model->item_szItemDesc),
			Force_Form_Textarea::factory('szItemDesc', 'Описание'),
		]);

		$form->control = Force_Form_Section::factory('szBRDesc', [
			Force_Form_Show_Value::factory('ORIGINAL')->value($model->original_szBRDesc),
			Force_Form_Show_Value::factory('MOD')->value($model->item_szBRDesc),
			Force_Form_Textarea::factory('szBRDesc', 'BR Описание'),
		]);

		$form->control = Force_Form_Section::factory('Attachment', [
			Force_Form_Select::factory('is_fixed')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('is_integrated')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('AttachmentClass')->add_option(null, '---')->add_options($attachment_classes),
			Force_Form_Select::factory('nasAttachmentClass')->add_option(null, '---')->add_options($nas_attachment_classes),
			Force_Form_Select::factory('nasLayoutClass')->add_option(null, '---')->add_options($nas_layout_classes),
			Force_Form_Select::factory('Attachment')->add_options(Helper_Form::get_boolean_options(1, null)),
//			'HiddenAddon',
			Force_Form_Select::factory('HiddenAttachment')->add_options(Helper_Form::get_boolean_options(1, null)),
			'APCost',
		]);

		$attachment_types = Attachment::instance()->get_type_list();
		$attachment_type_values = Attachment::get_types($model);
		$attachment_type_prefix = 'attachment_type_';
		$form->control = Helper::form_checkboxes($attachment_types, $attachment_type_values, 'Attachment Types', $attachment_type_prefix);
		$form->control = Force_Form_HTML::factory()->attribute('class', 'clearfix');

		$form->control = Force_Form_Section::factory('Item', [
			Force_Form_Show_Value::factory('item_ubWeight', 'Weight Original')->value($model->item_ubWeight),
			Force_Form_Input::factory('ubWeight', 'Weight'),
			Force_Form_Show_Value::factory('item_ItemSize', 'Item Size Original')->value($model->item_ItemSize),
			Force_Form_Input::factory('ItemSize', 'Item Size'),
			'usPrice',
			'bReliability',
			'bRepairEase',
			Force_Form_Select::factory('Damageable')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('Repairable')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('WaterDamages')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('Metal')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('Sinks')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('Electronic')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('Inseparable')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Select::factory('ShowStatus')->add_options(Helper_Form::get_boolean_options(1, null)),
		]);

		$form->control = Force_Form_Section::factory('Bonuses', [
			Force_Form_Select::factory('BlockIronSight')->add_options(Helper_Form::get_boolean_options(1, null)),
			Force_Form_Input::factory('BurstSizeBonus'),
			Force_Form_Input::factory('ItemSizeBonus'),
			Force_Form_Input::factory('MagSizeBonus'),
		]);

		$form->control = Force_Form_Section::factory('Aim To Hit Bonuses', [
			Force_Form_Input::factory('ToHitBonus'),
			Force_Form_Input::factory('BurstToHitBonus'),
			Force_Form_Input::factory('AutoFireToHitBonus'),
			Force_Form_Input::factory('Bipod'),
			Force_Form_Float::factory('PercentAccuracyModifier'),
			Force_Form_Float::factory('AimBonus'),
			Force_Form_Input::factory('MinRangeForAimBonus'),
		]);

		$form->control = Force_Form_Section::factory('Vision Bonuses', [
			Force_Form_Input::factory('VisionRangeBonus'),
			Force_Form_Input::factory('NightVisionRangeBonus'),
			Force_Form_Input::factory('DayVisionRangeBonus'),
			Force_Form_Input::factory('CaveVisionRangeBonus'),
			Force_Form_Input::factory('BrightLightVisionRangeBonus'),
			Force_Form_Float::factory('PercentTunnelVision'),
			Force_Form_Float::factory('ScopeMagFactor'),
		]);

		$form->control = Force_Form_Section::factory('Range Bonuses', [
			Force_Form_Input::factory('RangeBonus'),
			Force_Form_Float::factory('PercentRangeBonus'),
		]);

		$form->control = Force_Form_Section::factory('Laser and Flashlight Bonuses', [
			Force_Form_Float::factory('ProjectionFactor'),
			Force_Form_Input::factory('BestLaserRange'),
			Force_Form_Input::factory('FlashLightRange'),
		]);

		$form->control = Force_Form_Section::factory('Stealth Bonuses', [
			Force_Form_Float::factory('CamoBonus'),
			Force_Form_Float::factory('UrbanCamoBonus'),
			Force_Form_Float::factory('DesertCamoBonus'),
			Force_Form_Float::factory('SnowCamoBonus'),
			Force_Form_Float::factory('StealthBonus'),
			Force_Form_Float::factory('PercentNoiseReduction'),
			Force_Form_Select::factory('HideMuzzleFlash')->add_options(Helper_Form::get_boolean_options(1, null)),
		]);

		$form->control = Force_Form_Section::factory('AP Bonuses', [
			Force_Form_Float::factory('PercentBurstFireAPReduction'),
			Force_Form_Float::factory('PercentAutofireAPReduction'),
			Force_Form_Float::factory('PercentReadyTimeAPReduction'),
			Force_Form_Float::factory('PercentReloadTimeAPReduction'),
			Force_Form_Float::factory('PercentAPReduction'),
		]);

		$form->control = Force_Form_Section::factory('Damage Bonuses', [
			Force_Form_Float::factory('DamageBonus'),
			Force_Form_Float::factory('MeleeDamageBonus'),
		]);

		$form->control = Force_Form_Section::factory('Recoil Bonuses', [
			Force_Form_Float::factory('PercentRecoilModifier'),
			Force_Form_Float::factory('RecoilModifierX'),
			Force_Form_Float::factory('RecoilModifierY'),
		]);

		$form->control = Force_Form_Section::factory('Leveling', [
			Force_Form_Select::factory('BigGunList')->add_options(Helper_Form::get_boolean_options(1, null)),
			'BR_NewInventory',
			'BR_UsedInventory',
			'ubCoolness',
			'depends_on_items',
		]);

		$form->control = Force_Form_Section::factory('Stances', [
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
		]);

		$controls = $form->get_control_names(true);
		$controls = array_flip($controls);
		$rest_fields = array();

		foreach ($model->meta()->fields() as $field_name => $field_data) {
			if (in_array($field_name, [
				'id',
				'uiIndex',
				'HiddenAddon',
				'AttachmentClass',
				'attachment_bonuses',
			])) {
				continue;
			}
			if (!array_key_exists($field_name, $controls)) {
				$rest_fields[] = $field_name;
			}
		}

		if (!empty($rest_fields)) {
			$form->control = Force_Form_Section::factory('Rest Fields', $rest_fields);
		}

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();

			$model->szLongItemName = trim($model->szLongItemName);
			$model->szItemName = trim($model->szItemName);
			$model->szItemDesc = trim($model->szItemDesc);
			$model->szBRDesc = trim($model->szBRDesc);

			$model->szBRName = $model->szLongItemName;

			$depends_on_items = explode(',', $model->depends_on_items);
			foreach ($depends_on_items as $key => $depends_on_item) {
				$depends_on_items[$key] = trim($depends_on_item);
			}
			$model->depends_on_items = implode(',', $depends_on_items);

			$attachment_type_values = array();
			foreach ($attachment_types as $type) {
				$value = $form->get_value($attachment_type_prefix . $type);
				if (!is_null($value)) {
					$attachment_type_values[] = $type;
				}
			}
			if (empty($attachment_type_values)) {
				$model->attachment_types = NULL;
			} else {
				$model->attachment_types = json_encode($attachment_type_values);
			}
		}

		$this->template->content[] = $form->render();
	}

	/*
	 * SAVE
	 */

	protected function has_changes(Jelly_Collection $collection) {
		foreach ($collection as $model) {
			Attachment::calculate_bonuses($model);

			foreach ($model->meta()->fields() as $field_name => $field_data) {
				$save_status = Core_Item::get_field_save_status($field_data);
				if (!$save_status) {
					continue;
				}

				$value_mod = $model->{$field_name};

				if ($save_status == Core_Item::FIELD_OVERWRITE_BOOLEAN) {
					$labels[$field_name] = Force_Label::factory($value_mod)->preset_boolean_yes_no_hidden();
				}

				if ($model->uiIndex > 0) {
					$value = $model->{'item_' . $field_name};
				} else {
					$value = $model->get_original($field_name);
				}

				$field_status = Core_Item::get_field_save_status($field_data);
				switch ($field_status) {
					/*
					 * Если полю назначен параметр FIELD_OVERWRITE_NOT_EMPTY
					 * и значение поля пустое - то это значит что значение
					 * не будет перенесено из data_attachments в items_mod
					 */
					case Core_Item::FIELD_OVERWRITE_NOT_EMPTY:
						if (empty($value_mod)) {
							$value_mod = $value;
						}
						break;
				}

				if ($value != $value_mod) {

					if (empty($value_mod)) {
						$value_mod = 0;
					}

					$this->changes['' . $model->uiIndex][$field_name] = $value_mod;
				}
			}
		}

		return $this->get_changes_count();
	}

	protected function _save(Jelly_Collection $collection) {
		if (!Form::is_post()) {
			return false;
		}

		if ($this->request->post('action') !== 'save') {
			return false;
		}

		$attachments = array();
		/*
		 * Собираем массив аттачей и сохраняем вычисляемые данные в таблицу data_attachments
		 */
		foreach ($collection as $model) {
			Attachment::calculate_bonuses($model);

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}

			/*
			 * Пропускаем устройства интегрированные в оружие
			 */
			if ($model->uiIndex < 0) {
				continue;
			}

			$attachments[$model->uiIndex] = $model;
		}

		$items_array = array();

		if (!empty($attachments)) {
			$items = Core_Item_Mod::factory()->get_builder()
				->where('uiIndex', 'IN', array_keys($attachments))
				->select_all();

			foreach ($items as $item) {
				$items_array[$item->uiIndex] = $item;
			}
		}

		foreach ($attachments as $uiIndex => $attachment) {
			if (!array_key_exists($uiIndex, $items_array)) {
				$item = Core_Item_Mod::factory()->create();
				$item->id = $attachment->id;
				$item->uiIndex = $attachment->uiIndex;
				$items_array[$uiIndex] = $item;
			}
		}

		foreach ($items_array as $item) {
			/*
			 * Пропускаем позиции которых нет в attachments.
			 */
			if (!array_key_exists($item->uiIndex, $attachments)) {
				continue;
			}

			$attachment = $attachments[$item->uiIndex];

			foreach ($attachment->meta()->fields() as $field_name => $field_data) {
				$field_status = Core_Item::get_field_save_status($field_data);

				/*
				 * Работаем только с теми полями, которым в модели назначен PARAM_STATUS.
				 */
				switch ($field_status) {
					/*
					 * Если полю назначен параметр FIELD_OVERWRITE_NOT_EMPTY
					 * и значение поля пустое - то это значит что значение
					 * не будет перенесено из data_attachments в items_mod
					 */
					case Core_Item::FIELD_OVERWRITE_NOT_EMPTY:
						if (!empty($attachment->{$field_name})) {
							$item->{$field_name} = $attachment->{$field_name};
						}
						break;
					/*
					 * На данном этапе нет никакой разницы между INCREMENT и OVERWRITE
					 */
					case Core_Item::FIELD_INCREMENT:
					case Core_Item::FIELD_OVERWRITE:
					case Core_Item::FIELD_OVERWRITE_BOOLEAN:
						$item->{$field_name} = $attachment->{$field_name};
						break;
				}
			}

			try {
				$item->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

	protected function _calculate_coolness(Jelly_Collection $collection) {
		if (!Form::is_post()) {
			return false;
		}

		if ($this->request->post('action') !== 'coolness') {
			return false;
		}

		$bonus = Bonus::instance('ubCoolness');

		$depended_on_items = array();

		$item_coolness = array();

		foreach ($collection as $model) {
			/** @var $model Model_Attachment_Data */
			if ($model->uiIndex < 1) {
				continue;
			}

			$coolness = NULL;

			$check_type = true;
			$check_weapon = true;

			if ($model->depends_on_items) {
				$depended_on_items[$model->uiIndex] = $model;
				continue;
			}

			if (Attachment::has_type($model, Attachment::TYPE_BATTERIES)
				&& !Attachment::has_external_mount($model, Attachment::MOUNT_BATTERIES)) {
				continue;
			}

			if (Attachment::has_type($model, Attachment::TYPE_INTEGRAL)) {
				$coolness = NULL;
				$check_type = false;
				$check_weapon = false;
			}

			if ($check_type) {
				if (Attachment::has_type($model, Attachment::TYPE_MAG_ADAPTER)) {
					$coolness = 5;
					$bonus->set_bonus($coolness, Attachment::TYPE_MAG_ADAPTER);

					if (Attachment::has_type($model, Attachment::TYPE_RIFLE)) {
						$coolness += 1;
						$bonus->set_bonus(1, Attachment::TYPE_RIFLE);
					}
				} elseif (Attachment::has_type($model, Attachment::TYPE_STOCK)) {
					$coolness = 5;
					$bonus->set_bonus($coolness, Attachment::TYPE_STOCK);
				} elseif (Attachment::has_type($model, Attachment::TYPE_KNIFE)) {
					$coolness = 2;
					$bonus->set_bonus($coolness, Attachment::TYPE_KNIFE);
				} elseif (Attachment::has_type($model, Attachment::TYPE_FOREGRIP)) {
					$coolness = 6;
					$bonus->set_bonus($coolness, Attachment::TYPE_FOREGRIP);
					if (Attachment::has_type($model, Attachment::TYPE_BIPOD)) {
						$coolness += 1;
						$bonus->set_bonus(1, Attachment::TYPE_BIPOD);
					}
				} elseif (Attachment::has_type($model, Attachment::TYPE_SUPPRESSOR_SOUND)) {
					$coolness = 4;
					$bonus->set_bonus($coolness, Attachment::TYPE_SUPPRESSOR_SOUND);
					if (Attachment::has_type($model, Attachment::TYPE_RIFLE)) {
						$coolness += 2;
						$bonus->set_bonus(2, Attachment::TYPE_RIFLE);
					} elseif (Attachment::has_type($model, Attachment::TYPE_SNIPER)) {
						$coolness += 3;
						$bonus->set_bonus(3, Attachment::TYPE_SNIPER);
					}
				} elseif (Attachment::has_type($model, Attachment::TYPE_SCOPE)) {
					if ($model->ScopeMagFactor > 8) {
						$coolness = 8;
					} elseif ($model->ScopeMagFactor > 7) {
						$coolness = 7;
					} elseif ($model->ScopeMagFactor > 5) {
						$coolness = 6;
					} elseif ($model->ScopeMagFactor > 3) {
						$coolness = 5;
					} elseif ($model->ScopeMagFactor > 2) {
						$coolness = 4;
					} elseif ($model->ScopeMagFactor > 1) {
						$coolness = 3;
					}
					$bonus->set_bonus($coolness, Attachment::TYPE_SCOPE);
				} elseif (Attachment::has_type($model, Attachment::TYPE_LASER)) {
					$coolness = 7;
					$bonus->set_bonus(7 - $coolness, Attachment::TYPE_LASER);
					if (Attachment::has_type($model, Attachment::TYPE_FLASHLIGHT)) {
						$coolness += 1;
						$bonus->set_bonus(1, Attachment::TYPE_FLASHLIGHT);
					}
					if (Attachment::has_type($model, Attachment::TYPE_RIFLE)
						&& !Attachment::has_type($model, Attachment::TYPE_PISTOL)) {
						$coolness += 1;
						$bonus->set_bonus(1, Attachment::TYPE_RIFLE);
					}
					if (Attachment::has_type($model, Attachment::TYPE_FLASHLIGHT_PROJECTOR)) {
						$coolness -= 2;
						$bonus->set_bonus(-2, Attachment::TYPE_FLASHLIGHT_PROJECTOR);
					}
				}

				if (Attachment::has_type($model, Attachment::TYPE_BIPOD)) {
					if ($coolness < 3) {
						$coolness = 3;
						$bonus->set_bonus(3 - $coolness, Attachment::TYPE_BIPOD);
					}
				}

				if (Attachment::has_type($model, Attachment::TYPE_SIGHT)) {
					if ($coolness < 5) {
						$coolness = 5;
						$bonus->set_bonus(5 - $coolness, Attachment::TYPE_SIGHT);
					}

					if (Attachment::has_bonus($model, Attachment::BONUS_SIGHT_COLLIMATOR_SMALL)) {
						$coolness += 2;
						$bonus->set_bonus(2, Attachment::BONUS_SIGHT_COLLIMATOR_SMALL);
					}
					if (Attachment::has_bonus($model, Attachment::BONUS_SIGHT_COLLIMATOR_LARGE)) {
						$coolness += 3;
						$bonus->set_bonus(3, Attachment::BONUS_SIGHT_COLLIMATOR_LARGE);
					}
				}

				if (Attachment::has_type($model, Attachment::TYPE_SIGHT)
					|| Attachment::has_type($model, Attachment::TYPE_SCOPE)
				) {
					if (Attachment::has_external_mount($model, Attachment::MOUNT_LASER)
						|| Attachment::has_type($model, Attachment::TYPE_LASER)) {
						$coolness += 1;
						$bonus->set_bonus(1, Attachment::TYPE_LASER);
					}
				}

				if (Attachment::has_type($model, Attachment::TYPE_OLD)) {
					$coolness -= 1;
					$bonus->set_bonus(-1, Attachment::TYPE_OLD);
				}
			}

			if ($check_weapon) {
				$weapon = Core_Weapon_Mod::factory()->get_builder()
					->join('attachments_mod')->on('attachments_mod.itemIndex', '=', 'weapons_mod.uiIndex')
					->join('items_mod')->on('items_mod.uiIndex', '=', 'weapons_mod.uiIndex')
					->where('attachmentIndex', '=', $model->uiIndex)
					->order_by('items_mod.ubCoolness', 'asc')
					->select_column('ubCoolness')
					->limit(1)
					->select();
				/** @var $weapon Model_Item_Mod */

				$weapon_coolness = $weapon->ubCoolness;

				if ($weapon_coolness > $coolness) {
					$coolness = $weapon_coolness;
					$bonus->set_bonus($weapon_coolness - $coolness, 'Weapon');
				}
			}

			if ($check_type) {
				if (Attachment::has_type($model, Attachment::TYPE_INTERNAL)) {
					$coolness += 3;
					$bonus->set_bonus(3, Attachment::TYPE_INTERNAL);
				}
				if (Attachment::has_type($model, Attachment::TYPE_TARGETING)) {
					if ($coolness < 2) {
						$bonus->set_bonus(2 - $coolness, Attachment::TYPE_TARGETING);
						$coolness = 2;
					}
				} elseif (Attachment::has_type($model, Attachment::TYPE_UNDER_BARREL_WEAPON)) {
					if ($coolness < 5) {
						$bonus->set_bonus(5 - $coolness, Attachment::TYPE_UNDER_BARREL_WEAPON);
						$coolness = 5;
					}
				}
			}

			if ($coolness < 1) {
				$coolness = NULL;
			} elseif ($coolness > 10) {
				$bonus->set_bonus(10 - $coolness, 'Maximum: 10');
				$coolness = 10;
			}

			$item_coolness[$model->uiIndex] = $coolness;

			$model->ubCoolness = $coolness;

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		foreach ($depended_on_items as $model) {
			/** @var $model Model_Attachment_Data */

			$depended_items = explode(',', $model->depends_on_items);

			$coolness = NULL;

			foreach ($depended_items as $depended_item) {
				if (array_key_exists($depended_item, $item_coolness)) {
					$depended_coolness = $item_coolness[$depended_item];
					if ($coolness) {
						$coolness = min($coolness, $depended_coolness);
					} else {
						$coolness = $depended_coolness;
					}
				}
			}

			if (is_null($coolness)) {
				$items = Core_Item_Mod::factory()->get_builder()
					->where('uiIndex', 'IN', $depended_items)
					->select_column('ubCoolness')
					->select_all();

				foreach ($items as $item) {
					/** @var $item Model_Item_Mod */
					if ($coolness) {
						$coolness = min($coolness, $item->ubCoolness);
					} else {
						$coolness = $item->ubCoolness;
					}
				}
			}

			$model->ubCoolness = $coolness;

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

	public function action_import() {
		$model = Core_Attachment_Data::factory()->create();

		$form = Jelly_Form::factory($model)->preset_for_admin(false);
		$form->control = Force_Form_Input::factory('uiIndex');
		$form->control = Force_Form_Checkbox::factory('Replace');
		$form->button_submit('Import');

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();
			$uiIndex = $form->get_value('uiIndex');
			$replace = $form->get_value('Replace');

			if ($this->import($model, $replace)) {
				$model = Core_Attachment_Data::factory()->get_builder()
					->where('uiIndex', '=', $uiIndex)
					->limit(1)
					->select();

				$edit_path = Force_URL::current_clean()
					->action('edit')
					->route_param('id', $model->id)
					->get_url();

				Request::current()
					->redirect($edit_path);
			}
		}

		$this->template->content[] = $form->render();
	}

	public function import(Model_Attachment_Data $model, $replace = false) {
		$uiIndex = $model->uiIndex;

		$check_data = Core_Attachment_Data::factory()->get_builder()
			->where('uiIndex', '=', $uiIndex)
			->count();

		if ($check_data && !$replace) {
			Helper_Error::add('Item is already imported.');
			return false;
		} else {
			/**
			 * @var Model_Attachment_Data $model
			 */
			$model = Core_Attachment_Data::factory()->get_builder()
				->where('uiIndex', '=', $uiIndex)
				->limit(1)
				->select();
		}

		$item = Core_Item_Mod::factory()->get_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!$item->loaded()) {
			Helper_Error::add('Item not found');
			return false;
		}

		$fields = $model->meta()->fields();

		foreach ($fields as $field_name => $field_data) {
			$model->{$field_name} = $item->{$field_name};
		}

		$model->APCost = 20;

		try {
			$model->save();
		} catch (Exception $e) {
			Helper_Error::add($e->getMessage());
		}

		return $model->saved();
	}

	/**
	 * @param Model_Attachment_Data $model
	 * @param array $labels
	 * @param Model_Attachment_Data $model_mod
	 * @param Force_Button $edit_button
	 * @param Force_List_Row $row
	 */
	private function check_for_changes(Force_List_Row $row, Model_Attachment_Data $model, Force_Button $edit_button, array $labels) {
		$model_mod = clone $model;

		foreach ($model->meta()->fields() as $field_name => $field_data) {
			$save_status = Core_Item::get_field_save_status($field_data);
			if (!$save_status) {
				continue;
			}

			$field_label = $field_data->label;
			$value_mod = $model->{$field_name};
			$description = $field_data->description;
			$description = nl2br($description);

			if ($save_status == Core_Item::FIELD_OVERWRITE_BOOLEAN) {
				$labels[$field_name] = Force_Label::factory($value_mod)->preset_boolean_yes_no_hidden();
			}

			if ($model->uiIndex > 0) {
				$value = $model->{'item_' . $field_name};
			} else {
				$value = $model_mod->{$field_name};
			}

			$field_status = Core_Item::get_field_save_status($field_data);
			switch ($field_status) {
				/*
				 * Если полю назначен параметр FIELD_OVERWRITE_NOT_EMPTY
				 * и значение поля пустое - то это значит что значение
				 * не будет перенесено из data_attachments в items_mod
				 */
				case Core_Item::FIELD_OVERWRITE_NOT_EMPTY:
					if (empty($value_mod)) {
						$value_mod = $value;
					}
					break;
			}

			if ($value != $value_mod) {

				if (empty($value)) {
					$value = 0;
				}
				if (empty($value_mod)) {
					$value_mod = 0;
					$model->format($field_name, $value_mod);
				}

				$edit_button->btn_danger();

				$data = array();

				foreach ($labels as $label_field => $_label) {
					if (($_label instanceof Force_Label) && ($label_field == $field_name)) {
						$_label->color_red();
					}
				}

				switch ($field_name) {
					case Ja2_Item::STAND_MODIFIERS:
					case Ja2_Item::CROUCH_MODIFIERS:
					case Ja2_Item::PRONE_MODIFIERS:
						$json = json_decode($value, true);
						if (!empty($json)) {
							$data[] = 'ORIGINAL';
							foreach ($json as $stance_field => $stance_value) {
								$data[] = $stance_field . ' = ' . $stance_value;
							}
						} else {
							$data[] = 'ORIGINAL: NULL';
						}
						$json = json_decode($value_mod, true);
						if (!empty($json)) {
							$data[] = 'MOD';
							foreach ($json as $stance_field => $stance_value) {
								$data[] = $stance_field . ' = ' . $stance_value;
							}
						} else {
							$data[] = 'MOD: NULL';
						}
						break;
					default:
						$data[] = 'ORIGINAL: ' . $value;
						$data[] = 'MOD: ' . $value_mod;
						break;
				}

				$data = Helper_String::to_string($data, '<br/>');

				if (!empty($description) && !empty($data)) {
					$description .= '<br/><br/>';
				}

				$description .= $data;

				$row->cell($field_name)
					->attribute('style', 'color:red')
					->popover($field_label, $description);
			} else {
				if (!empty($value_mod)) {
					$row->cell($field_name)->popover($field_label, $description);
				}
			}
		}

		foreach ($labels as $label_field => $label) {
			if ($label instanceof Force_Label) {
				$model->format($label_field, $label->render());
			}
		}
	}

} // End Controller_Admin_Data_Attachments