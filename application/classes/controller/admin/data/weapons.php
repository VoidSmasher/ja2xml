<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Data_Weapons
 * User: legion
 * Date: 05.05.18
 * Time: 17:16
 */
class Controller_Admin_Data_Weapons extends Controller_Admin_Template {

	use Controller_Common_Weapons_Data;

	public function action_index() {
		$builder = Core_Weapon_Data::get_weapons_builder()
			->order_by('calibre_name')
			->order_by('szWeaponName');

		$filter = Force_Filter::factory(array(
			Core_Weapon::get_filter_index()
				->where('weapons_mod.uiIndex'),
			Core_Calibre::get_filter_control()
				->where('weapons_mod.ubCalibre'),
			Force_Filter_Select::factory('weapon_type', 'Тип', Core_Weapon::get_type_list())
				->multiple()
				->multiple_reset_button()
				->where('weapons_mod.ubWeaponType'),
			Force_Filter_Select::factory('stock', 'Приклад', Core_Attachment_Data::get_stock_list(true))
				->multiple()
				->multiple_reset_button()
				->where('integrated_stock_index'),
			Force_Filter_Input::factory('weapon_name', __('common.name'))
				->where('szWeaponName', 'LIKE'),
		))->apply($builder);

		Core_Weapon::check_filter_index($filter);
		Core_Calibre::check_filter_control($filter);

		$list = Force_List::factory()->preset_for_admin();

		$list->button('Weapons', Force_URL::current()
			->controller('weapons')
			->get_url());
		$list->button('Items', Force_URL::current()
			->controller('items')
			->get_url());
		$list->button('Attachments', Force_URL::current()
			->controller('attachments_weapons')
			->get_url());

		$list->column('uiIndex');
		$list->column('name_short')->label('Short Name');
		$list->column('name_long')->label('Long Name');
		$list->column('weapon_image')->label('image');
		$list->column('ubWeaponType')->label('weapon type');
		$list->column('rarity');
		$list->column('calibre_name');
		$list->column('integrated_attachments')->label('Integrated Attachments');
		$list->column('ubShotsPerBurst');
		$list->column('burst_length');
		$list->column('fire_rate_burst');
		$list->column('fire_rate_auto_min');
		$list->column('fire_rate_auto_max');
		$list->column('fire_rate_auto_avg')->label('fire rate auto avg');
		$list->column('mechanism_action')->label('action');
		$list->column('mechanism_trigger')->label('trigger');
		$list->column('mechanism_feature')->label('feature');
		$list->column('mechanism_reload')->label('reload');
		$list->column('length_barrel');
		$list->column('length_max');
		$list->column('length_min');
		$list->column('length_front_and_handle')
			->popover(NULL, 'Length front with handle');
		$list->column('length_front_to_trigger')
			->popover(NULL, 'Length front to trigger');
		$list->column('height_diff_stock_barrel');
		$list->column('weight');
//		$list->column('button_edit')->button_edit();
//		$list->column('button_delete')->button_delete();

		$list->apply($builder, null, false)
//			->button_add()
			->each(function (Model_Weapon_Data $model) {
				$row = Force_List_Row::factory();
				Bonus::clear();

				Core_Weapon::button_index_model($model);

				Core_Item::row_image($model, $row, 'weapon_image');

				$model->format('rarity', Core_Weapon_Data::get_rarity_label($model->rarity));

				$edit_link = Force_URL::current_clean()
					->action('edit')
					->route_param('id', $model->id)
					->back_url()
					->get_url();

				$edit_button_long = Force_Button::factory($model->szWeaponName)
					->link($edit_link)
					->btn_sm()
					->btn_default();

				$edit_button_short = clone $edit_button_long;
				$edit_button_short->label($model->szItemName);

				$model->format('integrated_attachments', Core_Attachment_Data::get_integrated_attachment_labels($model));

				if ($model->year_of_adoption) {
					$edit_button_long->btn_primary();
					if (!empty($model->name_long)) {
						$edit_button_long->label($model->name_long);
						if ($model->szWeaponName != $model->name_long) {
							$edit_button_long->btn_danger();
						} else if ($model->szLongItemName != $model->name_long) {
							$edit_button_long->btn_danger();
						} else if ($model->szBRName != $model->name_long) {
							$edit_button_long->btn_warning();
						}
					}
					$edit_button_short->btn_primary();
					if (!empty($model->name_short)) {
						$edit_button_short->label($model->name_short);
						if ($model->szItemName != $model->name_short) {
							$edit_button_short->btn_danger();
						}
					}
				} else {
					$edit_button_long->btn_default();
					$edit_button_short->btn_default();
				}

				$model->format('name_short', $edit_button_short->render());
				$model->format('name_long', $edit_button_long->render());

				$fire_rate_auto_avg = Core_Weapon_Data::get_fire_rate_auto($model);
				$model->fire_rate_auto_avg = $fire_rate_auto_avg;

				if ($model->no_full_auto) {
					foreach (['fire_rate_auto_min', 'fire_rate_auto_max', 'fire_rate_auto_avg'] as $field) {
						if ($model->{$field} > 0) {
							$row->cell($field)
								->attribute('style', 'color:silver')
								->popover('Full Auto', 'Disabled!', 'top');
						}
					}
				}

				if ($model->burst_length < 1 && $model->fire_rate_burst > 0) {
					if ($model->is_burst_fire_possible()) {
						$row->cell('fire_rate_burst')
							->popover('Burst Fire', 'Possible via Trigger Group', 'top');
					} else {
						$row->cell('fire_rate_burst')
							->attribute('style', 'color:silver')
							->popover('Burst Fire', 'Disabled!', 'top');
					}
				}

				$model->format('ubWeaponType', Core_Weapon::get_type_label($model));

				$model->format('length_max', round($model->length_max));
				$model->format('length_min', round($model->length_min));
				$model->format('length_barrel', round($model->length_barrel));
				$model->format('length_front_and_handle', round($model->length_front_and_handle));
				$model->format('weight', number_format($model->weight, 1));

				$row->cell('length_max')->attribute('style', 'color:#6495ED');
				$row->cell('length_min')->attribute('style', 'color:#6495ED');
				$row->cell('length_front_and_handle')->attribute('style', 'color:#337777');
				$row->cell('length_front_to_trigger')->attribute('style', 'color:#337777');

				return $row;
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		/** @var Model_Weapon_Data $model */
		$model = Core_Weapon_Data::factory()->create();
		$this->page_title = 'New';
		$this->_form($model);
	}

	public function action_edit() {
		/** @var Model_Weapon_Data $model */
		$model = Core_Weapon_Data::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_builder()
			->join('weapons_mod', 'LEFT')->on('weapons_mod.uiIndex', '=', 'data_weapons.uiIndex')
			->join('weapons', 'LEFT')->on('weapons.uiIndex', '=', 'data_weapons.uiIndex')
			->join('items_mod', 'LEFT')->on('items_mod.uiIndex', '=', 'data_weapons.uiIndex')
			->join('items', 'LEFT')->on('items.uiIndex', '=', 'data_weapons.uiIndex')
			->select_column('data_weapons.*')
			->select_column('weapons.szWeaponName', 'original_szWeaponName')
			->select_column('weapons_mod.szWeaponName', 'mod_szWeaponName')
			->select_column('items.szItemName', 'original_szItemName')
			->select_column('items.szLongItemName', 'original_szLongItemName')
			->select_column('items.szBRName', 'original_szBRName')
			->select_column('items.szItemDesc', 'original_szItemDesc')
			->select_column('items.szBRDesc', 'original_szBRDesc')
			->select_column('items_mod.szItemName', 'mod_szItemName')
			->select_column('items_mod.szLongItemName', 'mod_szLongItemName')
			->select_column('items_mod.szBRName', 'mod_szBRName')
			->select_column('items_mod.szItemDesc', 'mod_szItemDesc')
			->select_column('items_mod.szBRDesc', 'mod_szBRDesc')
			->limit(1)
			->select();
		$this->page_title = $model->mod_szLongItemName;
		$this->_form($model);
	}

	public function _form(Model_Weapon_Data $model) {
		$test_data_form = Force_Form::factory([
			Force_Form_Input::factory('range')->label('Range (m)')->number(),
			Force_Form_Input::factory('dx')->label('Diameter X (mm)')->number(),
			Force_Form_Input::factory('dy')->label('Diameter Y (mm)')->number(),
		])->no_cache()
			->button(Force_Button::factory(__('common.submit'))
				->attribute('class', 'btn-apply')
				->btn_primary()
				->modal_close())
			->button(Force_Button::factory(__('common.cancel'))->modal_close());

		$test_data_modal = Force_Modal::factory('test_data_modal')
			->label('Test Data')
			->content($test_data_form)
			->hide_buttons();

		Helper_Modal::add($test_data_modal);

		$test_data_table = json_decode($model->test_data, true);

		$test_data_list = Force_List::factory()
			->attribute('id', 'test_data_table');
		$test_data_list->column('range')->label('Range (m)');
		$test_data_list->column('dx')->label('Diameter X (mm)');
		$test_data_list->column('dy')->label('Diameter Y (mm)');
		$test_data_list->column('moa')->label('MOA');
		$test_data_list->column('edit')->button_place();
		$test_data_list->column('delete')->button_place();

		$test_data_list->apply($test_data_table)
			->each(function (&$row) {
				$range = Arr::get($row, 'range');
				$row['edit'] = Force_Button::preset_edit()
					->attribute('data-range', $range)
					->attribute('data-dx', Arr::get($row, 'dx'))
					->attribute('data-dy', Arr::get($row, 'dy'))
					->modal('test_data_modal');
				$row['delete'] = Force_Button::factory(__('common.delete'))
					->btn_danger()
					->attribute('class', 'btn-test-data-remove')
					->icon('fa fa-close')
					->attribute('data-range', $range);

				$tr = Force_List_Row::factory()->attribute('data-range', $range);
				return $tr;
			});

		$test_data_list->button(Force_Button::preset_add()
			->modal('test_data_modal'));

		$form = Jelly_Form::factory($model)
			->title(Helper_Admin::get_page_title($this->page_title))
			->no_cache()
			->preset_for_admin();

		$name_data = array(
			array(
				'version' => '<b>ORIGINAL</b>',
				'szItemName' => $model->original_szItemName,
				'szWeaponName' => $model->original_szWeaponName,
				'szLongItemName' => $model->original_szLongItemName,
				'szBRName' => $model->original_szBRName,
			),
			array(
				'version' => '<b>MOD</b>',
				'szItemName' => $model->mod_szItemName,
				'szWeaponName' => $model->mod_szWeaponName,
				'szLongItemName' => $model->mod_szLongItemName,
				'szBRName' => $model->mod_szBRName,
			),
		);
		$name_list = Force_List::factory([
			Force_List_Column::factory('version')->button_place()->col_right(),
			'szItemName',
			'szWeaponName',
			'szLongItemName',
			'szBRName',
		])->apply($name_data);

		$form->control = Force_Form_Section::factory('Weapon Name', [
			Force_Form_HTML::factory($name_list->render()),
			Force_Form_Input::factory('name_short', 'Новое короткое имя'),
			Force_Form_Input::factory('name_long', 'Новое длинное имя'),
		]);

		$form->control = Force_Form_Section::factory('szItemDesc', [
			Force_Form_Show_Value::factory('ORIGINAL')->value($model->original_szItemDesc),
			Force_Form_Show_Value::factory('MOD')->value($model->mod_szItemDesc),
			Force_Form_Textarea::factory('description', 'Описание'),
		]);

		$form->control = Force_Form_Section::factory('szBRDesc', [
			Force_Form_Show_Value::factory('ORIGINAL')->value($model->original_szBRDesc),
			Force_Form_Show_Value::factory('MOD')->value($model->mod_szBRDesc),
			Force_Form_Textarea::factory('description_br', 'BR Описание'),
		]);

		$form->control = Force_Form_Section::factory('Дополнительные сведения', [
			Force_Form_Textarea::factory('comment', 'Примечание'),
			Force_Form_Input::factory('constructor', 'Конструктор'),
			Force_Form_Input::factory('manufacturer', 'Производитель'),
			Force_Form_Input::factory('year_of_adoption', 'Начало программы'),
			Force_Form_Input::factory('year_of_withdrawal', 'Окончание программы'),
			Force_Form_Input::factory('amount_built', 'Количество произведено'),
			Force_Form_Select::factory('rarity', 'Редкость', Core_Weapon_Data::get_rarity_list()),
		]);

		$form->control = Force_Form_Section::factory('Габариты', [
			Force_Form_Float::factory('length_max', 'Полная длина'),
			Force_Form_Float::factory('length_min', 'Длина приклад сложен'),
			Force_Form_Float::factory('length_barrel', 'Длина ствола'),
			Force_Form_Float::factory('length_front_and_handle', 'Длина от переднего края до конца рукояти'),
			Force_Form_Float::factory('length_front_to_trigger', 'Длина от переднего края до спуска'),
			Force_Form_Float::factory('height_diff_stock_barrel', 'Разница между осью ствола и осью приклада')
				->description('Вниз - положительная (увеличение отдачи), вверх - отрицательная (уменьшение отдачи)'),
			Force_Form_Float::factory('weight', 'Вес округлённый'),
			Force_Form_Float::factory('weight_empty', 'Вес без патронов'),
			Force_Form_Float::factory('weight_loaded', 'Вес с патронами'),
		]);

		$form->control = Force_Form_Section::factory('Устройство', [
			Force_Form_Select::factory('mechanism_action', 'Механизм запирания и отпирания канала ствола')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_action_list()),
			Force_Form_Select::factory('mechanism_trigger', 'УСМ')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_trigger_list()),
			Force_Form_Select::factory('mechanism_feature', 'Особенности автоматики')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_feature_list()),
			Force_Form_Select::factory('mechanism_reload', 'Механизм питания')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_reload_list()),
			Force_Form_Select::factory('comfort', 'Удобство')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_comfort_list()),
		]);

		self::get_weapon_integrated_devices($form, 'Интегрированные устройства');

		self::get_weapon_data_booleans($form, 'Дополнительные свойства');

		$form->control = Force_Form_Section::factory('Скорострельность и режимы огня', [
			Force_Form_Input::factory('fire_rate_auto_min', 'Скорострельность (авто, min)')->number(),
			Force_Form_Input::factory('fire_rate_auto_max', 'Скорострельность (авто, max)')->number(),
			Force_Form_Input::factory('fire_rate_burst', 'Скорострельность (очередь)')->number(),
			Force_Form_Input::factory('fire_rate_semi', 'Скорострельность (одиночные)')->number(),
			Force_Form_Input::factory('burst_length', 'Длина фиксированной очереди')->number(),
			Force_Form_Checkbox::factory('no_semi_auto'),
			Force_Form_Checkbox::factory('no_full_auto'),
		]);

		$form->control = Force_Form_Section::factory('Показатели', [
			Force_Form_Input::factory('muzzle_velocity', 'Начальная скорость пули')->number(),
			Force_Form_Input::factory('targeting_range', 'Максимальная прицельная дальность')->number(),
			Force_Form_Input::factory('effective_range', 'Максимальная эффективная дальность')->number(),
			Force_Form_Float::factory('moa_claimed', 'Точность в MOA')
				->description('Minute Оf Angle. Заявленная точность.'),
			Force_Form_Float::factory('moa_claimed_range', 'Дистанция')
				->description('Дистанция для заявленной точности.'),
		]);

		$form->control = Force_Form_Section::factory('Пристрелочные данные', [
			Force_Form_Show_Value::factory('moa_test_average')->label('Точность в MOA')
				->description('Средний показатель MOA по тестовым данным. Данные для MOA обновляются при сохранении формы.'),
			Force_Form_HTML::factory($test_data_list),
			Force_Form_Hidden::factory('test_data')->value($model->test_data),
		]);

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();

			$model->name_long = trim($model->name_long);
			$model->name_short = trim($model->name_short);

			if ($model->test_diameter_x <= 0) {
				$model->test_diameter_x = $model->test_diameter_y;
			}
			if ($model->test_diameter_y <= 0) {
				$model->test_diameter_y = $model->test_diameter_x;
			}
			if ($model->test_diameter_x <= 0) {
				$model->test_diameter_x = null;
			}
			if ($model->test_diameter_y <= 0) {
				$model->test_diameter_y = null;
			}

			$test_data = json_decode($model->test_data, true);

			$test_data_moa = 0;

			if (is_array($test_data)) {
				foreach ($test_data as $key => $value) {
					$test_data[$key]['moa'] = Core_Weapon_Data::calculate_moa($key, Arr::get($value, 'dx'), Arr::get($value, 'dy'));
					$test_data_moa += $test_data[$key]['moa'];
				}

				if (count($test_data)) {
					$test_data_moa = round($test_data_moa / count($test_data), 2);
				}

				$test_data = json_encode($test_data);
			} else {
				$test_data = NULL;
			}

			$model->moa_test_average = $test_data_moa;
			$model->test_data = $test_data;

			foreach (self::$weapon_integrated_fields as $index_field) {
				$choices = Core_Attachment_Data::get_attachments_list_by_field($index_field, true);
				$name_field = str_replace('_index', '_name', $index_field);
				$integrated_index = $form->get_value($index_field);

				if (array_key_exists($integrated_index, $choices)) {
					$model->{$index_field} = $integrated_index;
					$model->{$name_field} = $choices[$integrated_index];
				} else {
					$model->{$index_field} = NULL;
					$model->{$name_field} = NULL;
				}
			}
		}

		Helper_Assets::add_scripts_in_footer('assets/ja2/js/weapons/test_data.js');

		$this->template->content[] = $form->render();
	}

	public function action_delete() {
		if (Core_Weapon_Data::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

} // End Controller_Admin_Data_Weapons
