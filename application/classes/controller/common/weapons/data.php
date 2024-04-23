<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_Data
 * User: legion
 * Date: 01.07.18
 * Time: 4:06
 */
trait Controller_Common_Weapons_Data {

	private static $weapon_boolean_fields = array(
		'is_two_handed',
		'is_secondary_weapon',
		'has_hp_iron_sights',
		'has_hp_scope_mount',
		'has_hk_trigger',
		'has_mag_stanag',
		'has_drum_mag',
		'has_calico_mag',
		'has_long_mag',
		'has_bolt_hold_open',
		'has_balanced_automatic',
		'has_compensator',
		'has_muzzle_break',
		'has_recoil_reducing_stock',
		'has_recoil_buffer_in_stock',
		'has_ported_barrel',
		'has_heavy_barrel',
		'has_sniper_barrel',
		'has_floating_barrel',
		'has_replaceable_barrel',
		'has_cheek_piece',
		'has_adjustable_cheek_piece',
		'has_adjustable_butt_stock',
		'has_adjustable_grip',
	);

	private static $weapon_integrated_fields = array(
		'integrated_suppressor_index',
		'integrated_laser_index',
		'integrated_sight_index',
		'integrated_scope_index',
		'integrated_bipod_index',
		'integrated_foregrip_index',
		'integrated_stock_index',
	);

	private static $weapon_bonus_fields = array(
		'accuracy_bonus',
		'range_bonus',
		'ready_bonus',
		'handling_bonus',
		'sp4t_bonus',
		'aptrm_bonus',
		'reload_bonus',
		'burst_ap_bonus',
		'afsp5ap_bonus',
		'recoil_x_bonus',
		'recoil_y_bonus',
	);

	private static $weapon_mechanism_fields = array(
		'mechanism_action',
		'mechanism_trigger',
		'mechanism_feature',
		'mechanism_reload',
	);

	/**
	 * @param Jelly_Model $model
	 * @param Force_Button $button_data
	 * @return Force_Button
	 */
	protected static function get_button_two_handed(Jelly_Model $model, Force_Button $button_data) {
		$button = clone $button_data;
		$button
			->remove_attribute_class([
				'btn',
				'btn-default',
				'btn-primary',
				'btn-danger',
			])
			->color_blue();

		$two_handed = $two_handed_item = ($model->TwoHanded) ? 2 : 1;
		$two_handed_data = Core_Weapon_Data::is_two_handed($model) ? 2 : 1;
		if ($two_handed_item != $two_handed_data) {
			$two_handed = $two_handed_data;
			$button->color_red();
		} elseif (!$model->TwoHanded) {
			$button->color_gray();
		}
		$button->label($two_handed . 'H');

		return $button;
	}

	private static function get_weapon_bonus($bonus, $bonus_percent = NULL) {
		if ($bonus_percent) {
			$value = $bonus_percent . '%';
		} else {
			$value = ($bonus == 0) ? '' : $bonus;
		}
		return $value;
	}

	/**
	 * @param Jelly_Model $model
	 * @param $caption
	 * @param null $caption_new
	 * @param bool $show_danger
	 * @return Force_Button
	 */
	protected static function get_button_weapon_data(Jelly_Model $model, $caption, $caption_new = NULL, $show_danger = true) {
		$uiIndex = $model->uiIndex;

		$button = Force_Button::factory($caption)
			->modal('weapon_data_modal')
			->attribute('data-id', $uiIndex);

		if ($model->length_front_to_trigger > 0) {
			$button->btn_primary();
		} else {
			$button->btn_default();
		}

		if (!empty($caption_new) && ($caption != $caption_new)) {
			$button->label($caption_new);
			if ($show_danger) {
				$button->btn_danger();
			}
		}

		$button
			->link('#')
			->btn_xs();

		return $button;
	}

	public function action_json_weapon_data() {
		$uiIndex = $this->request->param('id');

		$model = Core_Weapon_Mod::get_weapons_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!$model->loaded()) {
			echo json_encode([
				'status' => 'Failed to load model',
			]);
			return;
		}

		$data_input = array();
		$data_select = array();
		$data_checkbox = array();

		if (Request::current()->controller() == 'weapons') {
			foreach (self::$weapon_bonus_fields as $bonus_field) {
				$value = self::get_weapon_bonus($model->{$bonus_field}, $model->{$bonus_field . '_percent'});
				$data_input[$bonus_field] = $value;
			}

			$data_select['weapon_class'] = Core_Weapon::get_weapon_class($model);
			$data_select['weapon_type'] = Core_Weapon::get_weapon_type($model);
		}

		foreach (self::$weapon_mechanism_fields as $mechanism_field) {
			$data_select[$mechanism_field] = $model->{$mechanism_field};
		}

		foreach (self::$weapon_integrated_fields as $integrated_field) {
			$data_select[$integrated_field] = $model->{$integrated_field};
		}

//		$data_select['rarity'] = $model->rarity;

		foreach (self::$weapon_boolean_fields as $boolean_field) {
			switch ($boolean_field) {
				case 'is_two_handed':
					$data_checkbox[$boolean_field] = Core_Weapon_Data::is_two_handed($model);
					break;
				default:
					$data_checkbox[$boolean_field] = $model->{$boolean_field};
			}
		}

		/*
		 * Qualities
		 */
		$weapon_qualities = Weapon::instance()->get_quality_list();
		foreach ($weapon_qualities as $weapon_quality) {
			$data_checkbox[$weapon_quality] = 0;
		}

		$weapon_quality_values = Weapon::get_qualities($model);
		if (is_array($weapon_quality_values)) {
			foreach ($weapon_quality_values as $weapon_quality) {
				$data_checkbox[$weapon_quality] = 1;
			}
		}

		/*
		 * Mounts
		 */
		$attachment_mounts = Attachment::instance()->get_mount_list();
		foreach ($attachment_mounts as $attachment_mount) {
			$data_checkbox[$attachment_mount] = 0;
		}

		$attachment_mount_values = Attachment::get_mounts($model);
		if (is_array($attachment_mount_values)) {
			foreach ($attachment_mount_values as $attachment_mount) {
				$data_checkbox[$attachment_mount] = 1;
			}
		}

		echo json_encode([
			'status' => 'ok',
			'input' => $data_input,
			'select' => $data_select,
			'checkbox' => $data_checkbox,
		]);
	}

	protected function _weapon_data() {
		$this->_weapon_data_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/modal_ajax_data.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'weapon_data'),
			Force_Form_Hidden::factory('id', 0),
		])->button_submit()
			->no_cache()
			->button(Force_Button::factory('Отмена')->modal_close());

		if (Request::current()->controller() == 'weapons') {
			$class_choices = Core_Weapon::get_class_list();
			$type_choices = Core_Weapon::get_type_list();

			$section_bonus = Force_Form_Section::factory('')
				->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label();

			foreach (self::$weapon_bonus_fields as $bonus_field) {
				$section_bonus->control = Force_Form_Float::factory($bonus_field)
					->group_attribute('class', 'col-sm-2');
			}

			$section_bonus->control = Force_Form_Select::factory('weapon_class')
				->add_options($class_choices)
				->group_attribute('class', 'col-sm-2');

			$section_bonus->control = Force_Form_Select::factory('weapon_type')
				->add_options($type_choices)
				->group_attribute('class', 'col-sm-2');

			$form->control = $section_bonus;

			self::get_weapon_mechanism_feature($form);
		}

		self::get_weapon_integrated_devices($form);

		self::get_weapon_data_booleans($form);

		switch (Request::current()->controller()) {
			case 'attachments_weapons':
			case 'items':
				$weapon_qualities = Weapon::instance()->get_quality_list();
				self::_get_weapon_data_checkboxes($form, $weapon_qualities, '', 4);
				$attachment_mounts = Attachment::instance()->get_mount_list();
				self::_get_weapon_data_checkboxes($form, $attachment_mounts);
				break;
		}

		$action = Force_URL::current()
			->clean_query()
			->action('weapon_data')
			->get_url();

		$this->template->modal[] = Force_Modal::factory('weapon_data_modal')
			->attribute('data-action', $action)
			->label('Бонусы оружия')
			->content($form->render())
			->modal_lg()
			->hide_buttons()
			->render();
	}

	/**
	 * @param $form Force_Form|Jelly_Form
	 * @param string $section_name
	 * @return bool
	 */
	private static function get_weapon_mechanism_feature($form, $section_name = '') {
		if (!($form instanceof Force_Form) && !($form instanceof Jelly_Form)) {
			return false;
		}

		$section = Force_Form_Section::factory($section_name, [
			Force_Form_Select::factory('mechanism_action')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_action_list())
				->group_attribute('class', 'col-sm-3'),
			Force_Form_Select::factory('mechanism_trigger')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_trigger_list())
				->group_attribute('class', 'col-sm-3'),
			Force_Form_Select::factory('mechanism_feature')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_feature_list())
				->group_attribute('class', 'col-sm-3'),
			Force_Form_Select::factory('mechanism_reload')
				->add_option('', 'Default')
				->add_options(Core_Weapon_Data::get_mechanism_reload_list())
				->group_attribute('class', 'col-sm-3'),
		])->attribute('class', 'row')
			->attribute('style', 'padding:0');

		if (empty($section_name)) {
			$section->hide_label();
		}

		$form->control = $section;
		$form->control = Force_Form_HTML::factory()->attribute('class', 'clearfix');
		return true;
	}

	/**
	 * @param $form
	 * @param string $section_name
	 * @param bool $form_horizontal
	 * @return bool
	 */
	private static function get_weapon_integrated_devices($form, $section_name = '', $form_horizontal = false) {
		if (!($form instanceof Force_Form) && !($form instanceof Jelly_Form)) {
			return false;
		}

		$section = Force_Form_Section::factory($section_name);

		foreach (self::$weapon_integrated_fields as $field) {
			$control = Force_Form_Select::factory($field)
				->form_horizontal($form_horizontal)
				->add_option(NULL, '---');

			$control->add_options(Core_Attachment_Data::get_attachments_list_by_field($field, true));

			$label = str_replace('_index', '', $field);
			$label = str_replace('_', ' ', $label);
			$label = ucwords($label);
			$control->label($label);

//			if (empty($section_name)) {
			switch ($field) {
				case 'integrated_suppressor_index':
				case 'integrated_laser_index':
				case 'integrated_sight_index':
				case 'integrated_scope_index':
					$control->group_attribute('class', 'col-sm-3');
					break;
				case 'integrated_bipod_index':
				case 'integrated_foregrip_index':
				case 'integrated_stock_index':
					$control->group_attribute('class', 'col-sm-4');
					break;
			}
//			}

//			$control->group_attribute('class', 'col-sm-3');

			$section->control = $control;
		}

		/*
		// rarity
		$section->control = Force_Form_Select::factory('rarity')
			->label('Rarity')
			->add_options(Core_Weapon_Data::get_rarity_list())
			->group_attribute('class', 'col-sm-3');
		*/

		if (empty($section_name)) {
			$section
				->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label();
		}

		$form->control = $section;
		$form->control = Force_Form_HTML::factory()->attribute('class', 'clearfix');
		return true;
	}

	/**
	 * @return bool
	 * @throws HTTP_Exception_404
	 * @throws Kohana_Exception
	 */
	private function _weapon_data_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'weapon_data') {
			return false;
		}

		$uiIndex = $this->request->post('id');

		$model = Core_Weapon_Data::factory()->get_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!($model instanceof Jelly_Model)) {
			throw new HTTP_Exception_404();
			return false;
		}

		if (!$model->loaded()) {
			throw new HTTP_Exception_404();
			return false;
		}

		if (Request::current()->controller() == 'weapons') {
			self::set_weapon_bonus($model, 'accuracy_bonus');
			self::set_weapon_bonus($model, 'range_bonus');
			self::set_weapon_bonus($model, 'ready_bonus');
			self::set_weapon_bonus($model, 'handling_bonus');
			self::set_weapon_bonus($model, 'sp4t_bonus');
			self::set_weapon_bonus($model, 'aptrm_bonus');
			self::set_weapon_bonus($model, 'reload_bonus');

			// У этих нет значения в процентах
			$model->burst_ap_bonus = $this->request->post('burst_ap_bonus');
			$model->afsp5ap_bonus = $this->request->post('afsp5ap_bonus');

			self::set_weapon_bonus($model, 'recoil_x_bonus');
			self::set_weapon_bonus($model, 'recoil_y_bonus');

			/*
			 * MECHANISM
			 */
			foreach (self::$weapon_mechanism_fields as $field) {
				$model->{$field} = $this->request->post($field);
			}
		}

		$model->weapon_class = $this->request->post('weapon_class');
		$model->weapon_type = $this->request->post('weapon_type');
//		$model->rarity = $this->request->post('rarity');
//		$model->weight_front_percent = $this->request->post('weight_front_percent');

		foreach (self::$weapon_integrated_fields as $index_field) {
			$choices = Core_Attachment_Data::get_attachments_list_by_field($index_field, true);
			$name_field = str_replace('_index', '_name', $index_field);
			$integrated_index = $this->request->post($index_field);

			if (array_key_exists($integrated_index, $choices)) {
				$model->{$index_field} = $integrated_index;
				$model->{$name_field} = $choices[$integrated_index];
			} else {
				$model->{$index_field} = NULL;
				$model->{$name_field} = NULL;
			}
		}

		/*
		 * BOOLEANS
		 */
		foreach (self::$weapon_boolean_fields as $field) {
			$model->{$field} = (boolean)$this->request->post($field);
		}

		switch (Request::current()->controller()) {
			case 'attachments_weapons':
			case 'items':
				$this->_set_weapon_data_json($model, 'weapon_qualities', Weapon::instance()->get_quality_list());
				$this->_set_weapon_data_json($model, 'attachment_mounts', Attachment::instance()->get_mount_list());
				break;
		}

		try {
			$model->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

	/*
	 * Helpers
	 */

	protected function _set_weapon_data_json(Jelly_Model $model, $field, array $possible_values) {
		$values = array();
		foreach ($possible_values as $possible_value) {
			$value = (boolean)$this->request->post($possible_value);
			if ($value) {
				$values[] = $possible_value;
			}
		}
		if (!empty($values)) {
			$model->{$field} = json_encode($values);
		} else {
			$model->{$field} = NULL;
		}
	}

	private static function get_weapon_data_booleans($form, $section_name = '', $cols = 4) {
		self::_get_weapon_data_checkboxes($form, self::$weapon_boolean_fields, $section_name, $cols);
	}

	private static function _get_weapon_data_checkboxes($form, $fields, $section_name = '', $cols = 4) {
		if (!($form instanceof Force_Form) && !($form instanceof Jelly_Form)) {
			return;
		}

		$col_sm = floor(12 / $cols);

		$bonuses_in_row = ceil(count($fields) / $cols);

		$bonus_section = Force_Form_Section::factory($section_name);

		$col = 1;
		$bonus_number = 1;

		$section = Force_Form_Section::factory('', [])->attribute('class', 'col-sm-' . $col_sm)
			->hide_label();

		foreach ($fields as $bonus) {
			if ($bonus_number > $bonuses_in_row && $col < $cols) {
				$bonus_section->control = $section;
				$col++;
				$bonus_number = 1;
				$section = Force_Form_Section::factory('', [])->attribute('class', 'col-sm-' . $col_sm)
					->hide_label();
			}

			$section->control = Force_Form_Checkbox::factory($bonus, Helper::get_bonus_caption($bonus));

			$bonus_number++;
		}

		$bonus_section->control = $section;

		if (empty($section_name)) {
			$bonus_section
				->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label();
		}

		$form->control = $bonus_section;
		$form->control = Force_Form_HTML::factory()->attribute('class', 'clearfix');
	}

	private function set_weapon_bonus(Jelly_Model $model, $bonus_field) {
		$value = $this->request->post($bonus_field);
		if (strpos($value, '%')) {
			$model->{$bonus_field . '_percent'} = intval($value);
			$model->{$bonus_field} = NULL;
		} else {
			$model->{$bonus_field . '_percent'} = NULL;
			$model->{$bonus_field} = $value;
		}
	}

} // End Controller_Common_Weapons_Data