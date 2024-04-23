<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Attachments_Attachments
 * User: legion
 * Date: 19.07.2020
 * Time: 8:57
 */
trait Controller_Common_Attachments_Attachments {

	private static $attachments = array();
	private static $attachments_list = array();
	private static $attachments_loaded = false;
	private static $attachments_used = array();
	private static $attach_unknown;
	private static $list_of_weapons = false;
	private static $compare_with_original = false;
	private static $has_warnings = false;
	private static $data = array();
	private static $data_attachments = array();
	private static $data_mod = array();
	private static $data_original = array();
	private static $columns = array();

	private static $forbidden_classes = array();

	private static $changes_default = array();
	private static $changes_possible_set = array();
	private static $changes_possible_remove = array();

	/**
	 * @param $item_index
	 * @param $attach_index
	 * @return Model_Attachment_Data|Model_Attachment_Mod
	 */
	private static function get_attach($item_index, $attach_index) {
		if (!(self::$attach_unknown instanceof Core_Attachment_Data)) {
			self::$attach_unknown = Core_Attachment_Data::factory()->create();
			self::$attach_unknown->attach_name = 'Unknown';
		}

		$attachments = self::get_attachments();

		if (array_key_exists($attach_index, $attachments)) {
			$attach = $attachments[$attach_index];
		} elseif (isset(self::$attachments_used[$item_index][$attach_index])) {
			$attach = self::$attachments_used[$item_index][$attach_index];
		} else {
			$attach = self::$attach_unknown;
		}

		return $attach;
	}

	protected function _list(Jelly_Collection $collection_item_mod, Jelly_Collection $collection_item_data, Jelly_Collection $collection_attach, Jelly_Collection $collection_attach_mod, $compare_with_original, $list_of_weapons = false) {

		self::$list_of_weapons = $list_of_weapons;
		self::$compare_with_original = $compare_with_original;

		// Создаём список предметов, к которым будут прикрепляться аттачи
		foreach ($collection_item_mod as $model) {
			$item_index = $model->uiIndex;
			$item_name = $model->szLongItemName;

			self::$data[$item_index]['item_image'] = Core_Item::get_image($item_index);

			if ($list_of_weapons) {
				/** @var Force_Button $button_data */
				$button_data = self::get_button_weapon_data($model, $item_name, $model->weapon_name, false);
				self::$data[$item_index]['item_name'] = $button_data->render();
				self::$data[$item_index]['itemIndex'] = Core_Weapon::button_index($item_index)->render();
				self::$data[$item_index]['integrated_attachments'] = Core_Attachment_Data::get_integrated_attachment_labels($model);
				self::$data[$item_index]['integrated_mounts'] = Attachment::get_mount_labels($model);
				self::$data[$item_index]['two_handed'] = self::get_button_two_handed($model, $button_data)->render();
			} else {
				self::$data[$item_index]['item_name'] = $item_name;
				self::$data[$item_index]['itemIndex'] = Core_Weapon::button_index($item_index, 'id')->render();
			}
		}

		if (!$list_of_weapons) {
			foreach ($collection_item_data as $model) {
				$item_index = $model->uiIndex;

				self::$data[$item_index]['integrated_mounts_external'] = Attachment::get_external_mount_labels($model);
			}
		}

		// Создаём аттачи
		if ($compare_with_original) {
			self::read_attach_collection($collection_attach, true);
		}

		self::read_attach_collection($collection_attach_mod, false);

		foreach ($collection_item_mod as $model) {
			self::possible_attachments($model);
			self::default_attachments($model);
		}

		$attachments_list = self::get_attachments_list();

		$this->_generate($collection_item_data);

		$this->_clear($collection_item_data);

		$this->_save($collection_item_mod, $collection_attach_mod);

		$list = Force_List::factory()->preset_for_admin()
			->hide_items_per_page_selector()
			->title('Attachments');

		if ($list_of_weapons) {
			$list->button('Weapons', Force_URL::current()
				->controller('weapons')
				->get_url());
			$list->button('Items', Force_URL::current()
				->controller('items')
				->get_url());
			$list->button('Data', Force_URL::current()
				->controller('data_weapons')
				->get_url());
		}

		/*
		 * Добавляем кнопку генерирования аттачей
		 */
		$button_generate = Force_Button::factory('Сгенерировать');

		$button_generate
			->submit('#form-generate')
			->confirmation('Generate data?')
			->btn_success();

		$this->template->content[] = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'generate'),
		])->simple()->attribute('id', 'form-generate');

		$list->button_html($button_generate->render());

		/*
		 * Добавляем кнопку удаления конфликтов
		 */
		$button_clear = Force_Button::factory('Очистить');

		if (self::$has_warnings) {
			$button_clear
				->submit('#form-clear')
				->confirmation('Clear data?')
				->btn_warning();

			$this->template->content[] = Force_Form::factory([
				Force_Form_Hidden::factory('action', 'clear'),
			])->simple()->attribute('id', 'form-clear');
		} else {
			$button_clear->btn_disabled();
		}
		$list->button_html($button_clear->render());

		/*
		 * Добавляем кнопку сохранения
		 */
		$button_save = Force_Button::factory(__('common.save'));

		if (!empty(self::$changes_default) || !empty(self::$changes_possible_set) || !empty(self::$changes_possible_remove)) {
			$button_save
				->submit('#form-save')
				->confirmation('Save data?');

			if (self::$has_warnings) {
				$button_save->btn_warning();
			} else {
				$button_save->btn_danger();
			}

			$this->template->content[] = Force_Form::factory([
				Force_Form_Hidden::factory('action', 'save'),
			])->simple()->attribute('id', 'form-save');
		} else {
			$button_save->btn_disabled();
		}
		$list->button_html($button_save->render());

		$list->column('itemIndex')->label('uiIndex')->col_control();
		$list->column('item_name')->label('name')->col_no_wrap();
		$list->column('item_image')->label('image')->col_control();
		if ($list_of_weapons) {
			$list->column('integrated_attachments')->label('Integrated Attachments');
			$list->column('integrated_mounts')->label('Integrated Mounts');
			$list->column('two_handed')->label('Two hand')->col_control();
		} else {
			$list->column('integrated_mounts_external')->label('Integrated Mounts');
		}
		$list->column('button_append_default')->button_place();
		$list->column('default_attachments')->label('Default Attachments')->col_no_wrap();
		$list->column('button_append')->button_place();

		/*
		 * Добавляем колонки для аттачей
		 */
		foreach ($attachments_list as $attach_index => $attach_name) {
			if (array_key_exists($attach_index, self::$columns)) {
				$list->column($attach_index)->label($attach_name);
				unset(self::$columns[$attach_index]);
			}
		}

		/*
		 * Если ещё остались колонки, то добавляем и их
		 * Сюда попадут неучтённые/неизвестные аттачи
		 */
		foreach (self::$columns as $attach_index => $attach_name) {
			$list->column($attach_index)->label($attach_name);
		}

		$list->apply(self::$data, null, false)
			->each(function (&$data) {
				$row = Force_List_Row::factory();

				$button = $data['button_append'];
				if ($button instanceof Force_Button) {
					$data['button_append'] = $button->render();
				}

				$button = $data['button_append_default'];
				if ($button instanceof Force_Button) {
					$data['button_append_default'] = $button->render();
				}

				$row->cell('item_image')
					->attribute('style', 'background-color:#CA9');

				return $row;
			});

		return $list;
	}

	private static function default_attachments(Model_Item_Mod $model) {

		$item_index = $model->uiIndex;
		$item_name = $model->szLongItemName;

		$button_append_default = self::get_button_append_default($item_index);

		/*
		 * Добавляем кнопки для data_weapons.default_attachments
		 */
		$default_buttons = array();

		$default_remove = array();

		$changes_info = array();

		/*
		 * item.DefaultAttachment
		 */
		if (self::$compare_with_original) {
			$default_attachments_original = Core_Item_Mod::get_default_attachments($model, 'DefaultAttachment_original');

			foreach ($default_attachments_original as $attach_index) {
				$attach = self::get_attach($item_index, $attach_index);

				$attachment = Attachment_Data::factory($item_index, $attach_index);
				$attachment->item_name = $item_name;
				$attachment->attach_name = $attach->attach_name;
				$attachment->is_default = true;
				$attachment->is_original = true;
				$attachment->is_restore = true;

				$default_buttons[$attach_index] = $attachment;
			}
		}

		/*
		 * item_mod.DefaultAttachment
		 */
		$default_attachments_mod = Core_Item_Mod::get_default_attachments($model);

		foreach ($default_attachments_mod as $attach_index) {
			$attach = self::get_attach($item_index, $attach_index);

			$attachment = Attachment_Data::load($default_buttons, $item_index, $attach_index, true);
			$attachment->item_name = $item_name;
			$attachment->attach_name = $attach->attach_name;
			$attachment->is_default = true;
			$attachment->is_mod = true;
			$attachment->is_restore = true;

			$default_buttons[$attach_index] = $attachment;
			$default_remove[$attach_index] = $attachment->attach_name;
		}

		/*
		 * data.default_attachments
		 */
		$default_attachments = Core_Weapon_Data::get_default_attachments($model);

		foreach ($default_attachments as $attach_index) {
			if (isset($default_remove[$attach_index])) {
				unset($default_remove[$attach_index]);
			}

			$attach = self::get_attach($item_index, $attach_index);

			$attachment = Attachment_Data::load($default_buttons, $item_index, $attach_index, true);
			$attachment->item_name = $item_name;
			$attachment->attach_name = $attach->attach_name;
			$attachment->is_restore = false;
			$attachment->is_fixed = $attach->is_fixed;

			if ($attachment->is_fixed) {
				self::$forbidden_classes[$item_index][$attach->nasAttachmentClass] = $attach->nasAttachmentClass;
			} else {
				/*
				 * Конфликты всегда показываются в первую очередь
				 * Если конфликта нет - помечаем как новый аттач
				 */
				if (self::$list_of_weapons && $model instanceof Model_Weapon_Group) {
					$checked = Attachment::check_weapon($model, $attach);
				} else {
					$checked = Attachment::check_item($model, $attach);
				}

				if (!$checked) {
					$attachment->warning();
					$button_append_default->btn_warning();
					self::$has_warnings = true;
					$changes_info[] = '! ' . $attachment->attach_name;
				}
			}

			if (self::$compare_with_original) {
				if (!$attachment->is_original && $attachment->is_mod) {
					$attachment->success();
				} elseif ($attachment->is_original && !$attachment->is_mod) {
					$attachment->danger();
				}
			}

			if (!$attachment->is_mod) {
				// Добавление
				$changes_info[] = '+ ' . $attachment->attach_name;
				$attachment->success();
			}

			$default_buttons[$attach_index] = $attachment;
		}

		foreach ($default_remove as $attach_index => $attach_name) {
			// Удаление
			$changes_info[] = '- ' . $attach_name;
		}

		if ($default_attachments != $default_attachments_mod) {
			self::$changes_default[$item_index] = $item_index;
			$button_append_default->btn_danger();
		}

		if (!empty($changes_info)) {
			$button_append_default->popover('Changes', Helper_String::to_string($changes_info, '<br/>'));
		}

		self::$data[$item_index]['default_attachments'] = implode(' ', $default_buttons);
	}

	private static function read_attach_collection(Jelly_Collection $collection_attach, $is_original = false) {
		foreach ($collection_attach as $model) {
			$attachment = Attachment_Data::load(self::$data, $model->itemIndex, $model->attachmentIndex);
			$attachment->item_name = $model->szLongItemName;
			$attachment->attach_name = $model->attach_name;
			$attachment->ap_cost = $model->APCost;
			$attachment->is_restore = true;

			if ($is_original) {
				$attachment->is_original = true;
				$attachment->ap_cost_original = $model->APCost;
			} else {
				$attachment->is_mod = true;
				$attachment->ap_cost_mod = $model->APCost;
				self::$changes_possible_remove[$attachment->item_index][$attachment->attach_index] = $attachment->attach_name;
			}

			self::$data[$attachment->item_index][$attachment->attach_index] = $attachment;
			self::$attachments_used[$attachment->item_index][$attachment->attach_index] = $model;
			self::$columns[$attachment->attach_index] = $attachment->attach_name;
		}
	}

	public static function possible_attachments(Model_Item_Mod $model) {
		$possible_attachments = Core_Weapon_Data::get_possible_attachments($model);

		$item_index = $model->uiIndex;
		$item_name = $model->szLongItemName;

		$changes_info = array();

		$array_possible_attachments_data[$item_index] = $possible_attachments;

		$button_append = self::get_button_append($item_index);

		/*
		 * Плодим кнопки доступных аттачей
		 */
		foreach ($possible_attachments as $attach_index => $ap_cost) {
			if (isset(self::$changes_possible_remove[$item_index][$attach_index])) {
				unset(self::$changes_possible_remove[$item_index][$attach_index]);
			}

			$attach = self::get_attach($item_index, $attach_index);

			$attachment = Attachment_Data::load(self::$data, $item_index, $attach_index);
			$attachment->item_name = $item_name;
			$attachment->attach_name = $attach->attach_name;
			$attachment->ap_cost = $attach->APCost;
			$attachment->is_fixed = $attach->is_fixed;
			$attachment->is_restore = false;

			/*
			 * Конфликты всегда показываются в первую очередь
			 * Если конфликта нет - помечаем как новый аттач
			 */
			if (self::$list_of_weapons && $model instanceof Model_Weapon_Group) {
				$checked = Attachment::check_weapon($model, $attach);
			} else {
				$checked = Attachment::check_item($model, $attach);
			}

			if (!$checked) {
				$attachment->warning();
				$button_append->btn_warning();
				$changes_info[] = '! ' . $attachment->attach_name;
				self::$has_warnings = true;
			}

			/*
			 * Изменения
			 */

			if (self::$compare_with_original) {
				if (!$attachment->is_original && $attachment->is_mod) {
					$attachment->success();
				} elseif ($attachment->is_original && !$attachment->is_mod) {
					$attachment->danger();
				}
			}

			if (!$attachment->is_mod) {
				// Добавление
				$changes_info[] = '+ ' . $attachment->attach_name . ': ' . $attachment->ap_cost;
				self::$changes_possible_set[$item_index][$attach_index] = $attachment->ap_cost;
				$attachment->success();
				$button_append->btn_danger();
			} elseif ($attachment->ap_cost != $attachment->ap_cost_mod) {
				// Другой AP cost
				$changes_info[] = '=/= ' . $attachment->attach_name . ': ' . $attachment->ap_cost_mod . ' => ' . $attachment->ap_cost;
				self::$changes_possible_set[$item_index][$attach_index] = $attachment->ap_cost;
				$button_append->btn_danger();
			}

			self::$data[$item_index][$attach_index] = $attachment;
			self::$columns[$attachment->attach_index] = $attachment->attach_name;
		}

		if (empty(self::$changes_possible_remove[$item_index])) {
			unset(self::$changes_possible_remove[$item_index]);
		}

		if (isset(self::$changes_possible_remove[$item_index])) {
			foreach (self::$changes_possible_remove[$item_index] as $attach_index => $attach_name) {
				// Удаление
				$changes_info[] = '- ' . $attach_name;
				$button_append->btn_danger();
			}
		}

		if (!empty($changes_info)) {
			$button_append->popover('Changes', Helper_String::to_string($changes_info, '<br/>'));
		}
	}

	protected function _generate(Jelly_Collection $collection_item_data) {
		if (!Form::is_post()) {
			return;
		}

		$action = $this->request->post('action');
		if ($action != 'generate') {
			return;
		}

		$attachments = self::get_attachments();

		foreach ($collection_item_data as $model) {
			$possible_attachments = Core_Weapon_Data::get_possible_attachments($model);

			/*
			 * Чистка мусора
			 */
			foreach ($possible_attachments as $attach_index => $ap_cost) {
				if (!array_key_exists($attach_index, $attachments)) {
					unset($possible_attachments[$attach_index]);
				}
			}

			/*
			 * Генерируем и удаляем аттачи, в зависимоти от прохождения ими проверки
			 */
			foreach ($attachments as $attach_index => $attach) {
				if (self::$list_of_weapons && $model instanceof Model_Weapon_Group) {
					$checked = Attachment::check_weapon($model, $attach);
				} else {
					$checked = Attachment::check_item($model, $attach);
				}

				if ($checked) {
					if (!array_key_exists($attach_index, $possible_attachments)) {
						$possible_attachments[$attach_index] = Core_Attachment_Data::DEFAULT_AP_COST;
					}
				} else {
					if (array_key_exists($attach_index, $possible_attachments)) {
						unset($possible_attachments[$attach_index]);
					}
				}
			}

			Core_Weapon_Data::set_possible_attachments($model, $possible_attachments);

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());
	}

	public function _clear(Jelly_Collection $collection_item_data) {
		if (!Form::is_post()) {
			return;
		}

		$action = $this->request->post('action');
		if ($action != 'clear') {
			return;
		}

		$attachments = self::get_attachments();

		foreach ($collection_item_data as $model) {
			$item_index = $model->uiIndex;
			$possible_attachments = Core_Weapon_Data::get_possible_attachments($model);

			foreach ($possible_attachments as $attach_index => $ap_cost) {
				if (array_key_exists($attach_index, $attachments)) {
					$attach = $attachments[$attach_index];
				} else {
					unset($possible_attachments[$attach_index]);
					continue;
				}

				$attach_is_fixed = $attach->is_fixed;

				if (!$attach_is_fixed) {
					if (self::$list_of_weapons && $model instanceof Model_Weapon_Group) {
						$checked = Attachment::check_weapon($model, $attach, Arr::get(self::$forbidden_classes, $item_index, []));
					} else {
						$checked = Attachment::check_item($model, $attach, Arr::get(self::$forbidden_classes, $item_index, []));
					}

					if (!$checked) {
						unset($possible_attachments[$attach_index]);
					}
				}
			}

			Core_Weapon_Data::set_possible_attachments($model, $possible_attachments);

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());
	}

	public function _save(Jelly_Collection $collection_item_mod, Jelly_Collection $collection_attach_mod) {
		if (!Form::is_post()) {
			return;
		}

		$action = $this->request->post('action');
		if ($action != 'save') {
			return;
		}

		foreach ($collection_item_mod as $model) {
			$item_index = $model->uiIndex;

			/*
			 * Сохраняем default_attachments
			 */
			if (array_key_exists($item_index, self::$changes_default)) {

				$model->DefaultAttachment = $model->default_attachments;

				try {
					$model->save();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}
			}
		}

		foreach ($collection_attach_mod as $model) {
			$item_index = $model->uiIndex;
			$attach_index = $model->attachmentIndex;

			/*
			 * Удаляем possible_attachments
			 */
			if (array_key_exists($item_index, self::$changes_possible_remove)
				&& array_key_exists($attach_index, self::$changes_possible_remove[$item_index])) {
				try {
					$model->delete();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}
			}

			/*
			 * Переопределяем possible_attachments
			 */
			if (array_key_exists($item_index, self::$changes_possible_set)
				&& array_key_exists($attach_index, self::$changes_possible_set[$item_index])) {
				$attach_ap_cost = self::$changes_possible_set[$item_index][$attach_index];

				$model = Core_Attachment_Mod::set_attachment($item_index, $attach_index, $attach_ap_cost, $model);

				try {
					$model->save();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}

				if ($model->saved()) {
					unset(self::$changes_possible_set[$item_index][$attach_index]);
				}
			}
		}

		/*
		 * Добавляем possible_attachments
		 */
		foreach (self::$changes_possible_set as $item_index => $attach) {
			foreach ($attach as $attach_index => $attach_ap_cost) {
				$model = Core_Attachment_Mod::set_attachment($item_index, $attach_index, $attach_ap_cost);

				try {
					$model->save();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());
	}

	public function _form(Jelly_Model $item_data) {
		$item_attachments = Core_Weapon_Data::get_possible_attachments($item_data);

		$attachments = Core_Attachment_Data::get_attachments_builder()
			->where('uiIndex', '>', 0)
			->order_by('item_szLongItemName')
			->select_all();

		$attachments_array = array();

		/*
		 * Form up attachments_array and check attachments
		 */
		foreach ($attachments as $index => $attachment) {
			$attach_index = $attachment->uiIndex;
			/*
			 * Здесь убираем все фиксированные аттачи, типа гранатомёта OICW
			 * Их нельзя добавлять к другому оружию
			 */
			if ($attachment->is_fixed) {
				continue;
			}
			/*
			 * Пропускаем уже назначенные аттачи
			 */
			if (array_key_exists($attach_index, $item_attachments)) {
				continue;
			}

			if ($item_data instanceof Model_Weapon_Data) {
				$checked = Attachment::check_weapon($item_data, $attachment);
			} else {
				$checked = Attachment::check_item($item_data, $attachment);
			}

			if ($checked) {
				$attachments_array[$attach_index] = Core_Attachment_Data::get_name_long($attachment);
			}
		}

		$item_name = $item_data->uiIndex . ' ' . $item_data->szLongItemName;

		if (empty($attachments_array)) {
			$this->template->content[] = '<div class="jumbotron"><p>Nothing to add</p>';
			$this->template->content[] = Force_Button::factory(__('common.back_to_list'))
				->link(Force_URL::get_index_uri());
			$this->template->content[] = '</div>';
			return;
		}

		$form = Force_Form::factory([
			Force_Form_Show_Value::factory('item')->value($item_name),
			Force_Form_Select::factory('attachment')
				->label('Attachment')
				->add_options($attachments_array),
			Force_Form_Input::factory('APCost')
				->value(20)
				->label('AP Cost')
		])->preset_for_admin();

		if ($form->is_ready_to_apply()) {
			$attach_index = $form->get_value('attachment');
			$attach_ap_cost = $form->get_value('APCost');

			$attach = Core_Attachment_Mod::get_attachment($item_data->uiIndex, $attach_index);

			$attach->itemIndex = $item_data->uiIndex;
			$attach->attachmentIndex = $attach_index;
			$attach->APCost = $attach_ap_cost;

			Core_Weapon_Data::set_possible_attachment($item_data, $attach_index, $attach_ap_cost);

			try {
				$item_data->save();
				$attach->save();
				$form->redirect();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		$this->template->content = $form->render();
	}

	public function _form_default(Jelly_Model $item_data, Model_Item_Mod $item_mod) {

		$item_attachments = Core_Attachment_Mod::get_weapons_builder()
			->where('itemIndex', '=', $item_data->uiIndex)
			->order_by('attach_name')
			->select_all()
			->as_array('attachmentIndex', 'attach_name');

		$item_name = $item_data->uiIndex . ' ' . $item_data->szLongItemName;

		$form = Force_Form::factory([
			Force_Form_Show_Value::factory('item')->value($item_name),
			Force_Form_Select::factory('attachment')
				->label('Attachment')
				->add_options($item_attachments),
		])->preset_for_admin();

		if ($form->is_ready_to_apply()) {
			$attach_index = $form->get_value('attachment');

			Core_Weapon_Data::set_default_attachment($item_data, $attach_index);

			$item_mod->DefaultAttachment = $item_data->default_attachments;

			try {
				$item_data->save();
				$item_mod->save();
				$form->redirect();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		$this->template->content = $form->render();
	}

	public static function get_attachments() {
		self::load_attachments();
		return self::$attachments;
	}

	public static function get_attachments_list() {
		self::load_attachments();
		return self::$attachments_list;
	}

	private static function load_attachments() {
		if (self::$attachments_loaded) {
			return false;
		}

		$attachments_collection = Core_Attachment_Data::get_attachments_builder()
			->order_by('nasAttachmentClass')
			->order_by('item_szLongItemName')
			->select_all();

		foreach ($attachments_collection as $model) {
			self::$attachments_list[$model->uiIndex] = $model->attach_name;
			self::$attachments[$model->uiIndex] = $model;
		}

		return self::$attachments_loaded = true;
	}

	/**
	 * @param $item_index
	 * @return Force_Button
	 */
	protected static function get_button_append($item_index) {
		if (isset(self::$data[$item_index]['button_append']) && self::$data[$item_index]['button_append'] instanceof Force_Button) {
			return self::$data[$item_index]['button_append'];
		}

		$append_link = Force_URL::current_clean()->action('append')
			->route_param('id', $item_index)
			->back_url();

		$button = Force_Button::factory('+')
			->link($append_link->get_url())
			->btn_sm()
			->color_gray();

		self::$data[$item_index]['button_append'] = $button;

		return $button;
	}

	/**
	 * @param $item_index
	 * @return Force_Button
	 */
	protected static function get_button_append_default($item_index) {
		if (isset(self::$data[$item_index]['button_append_default']) && self::$data[$item_index]['button_append_default'] instanceof Force_Button) {
			return self::$data[$item_index]['button_append_default'];
		}

		$append_link = Force_URL::current_clean()->action('default')
			->route_param('id', $item_index)
			->back_url();

		$button = Force_Button::factory('+')
			->link($append_link->get_url())
			->btn_sm()
			->color_gray();

		self::$data[$item_index]['button_append_default'] = $button;

		return $button;
	}

} // End Controller_Common_Attachments_Attachments
