<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Core
 * User: legion
 * Date: 14.11.14
 * Time: 16:44
 */
abstract class Force_Form_Core extends Force_Attributes {

	use Force_Control_Name;
	use Force_Control_Label;
	use Force_Control_Buttons;
	use Force_Control_Horizontal;
	use Force_Control_Simple;
	use Force_Form_Controls;
//	use Force_Form_Steps;

	protected $_data_array = array();
	protected $_show_menu = false;
	protected $_menu = null;
	protected $_hidden = array();
	protected $_is_preset_for_admin = false;

	protected $_button_submit = null;
	protected $_button_submit_and_stay = null;
	protected $_button_cancel = null;

	protected $_show_buttons_label = false;
	protected $_back_url = null;

	protected $_has_required_fields = false;
	protected $_show_required_message = true;

	public function __construct(array $controls = array(), $form_action = null, $form_method = 'post') {
		$this->add_controls($controls);
		$this->form_action($form_action);
		$this->form_method($form_method);
		switch ($this->get_form_method()) {
			case 'post':
				$this->_data_array = &$_POST;
				break;
			case 'get':
				$this->_data_array = &$_GET;
				break;
			default:
				$this->_data_array = &$_REQUEST;
		}
	}

	public function __toString() {
		return $this->render();
	}

	/*
	 * SET
	 */

	public function __set($name, $value) {
		$this->_set_buttons($name, $value);
		$this->_set_controls($name, $value);
	}

	protected function _get_form_body(&$controls, $level = 0) {
		$form_body = '';

//		if ($level == 1) {
//			Helper_Error::var_dump($controls, 'level 1 controls');
//		}

		foreach ($controls as $control_id => $control) {
			if ($control instanceof Force_Form_Container) {
				if ($this->_is_preset_for_admin) {
					$control->preset_for_admin();
				}

				if ($this->is_show_menu() && ($level == 0)) {
					if ($this->_menu instanceof Force_Menu_Form) {
						$this->_menu->item($control->get_name())
							->link('#' . $control->get_name())
							->i18n($control->get_label());
					}
				}

				$container_controls = $control->get_controls();
				$container_body = $this->_get_form_body($container_controls, $level + 1);
				$form_body .= $control->render($container_body);
			} else {
				if (is_null($control)) {
					$control = Force_Form_Input::factory($control_id);
				} elseif (is_string($control) && !empty($control)) {
					$control = Force_Form_Input::factory($control);
				}

				if (!($control instanceof Force_Form_Control)) {
					continue;
				}

				if ($this->_is_preset_for_admin) {
					$control->preset_for_admin();
				}

				if ($control instanceof Force_Form_Combine) {
					if ($this->is_show_menu() && ($level == 0)) {
						if ($this->_menu instanceof Force_Menu_Form) {
							$this->_menu->item($control->get_name())
								->link('#' . $control->get_name())
								->i18n($this->get_label());
						}
					}
				}

				if ($control->is_form_horizontal_undefined()) {
					$control->form_horizontal($this->get_form_horizontal_label_width());
				}

				if ($control instanceof Force_Form_Image) {
					$this->_add_max_file_size_param();
				}

				if ($this->is_ready_to_apply()) {
					$_name = $control->get_name();
					if (!$control->is_read_only() && !empty($_name)) {
						$value = Arr::get($this->_data_array, $_name);
						$control->apply_value_before_save($this, $value);
						// apply_value_before_save сам обновляет value
//						$control->value($value);
					}
				}

				if (!$this->_has_required_fields && $control->is_required()) {
					$this->_has_required_fields = true;
				}

				$form_body .= $control->render();
			}
		}

		return $form_body;
	}

	public function render() {
		$menu = '';
		if ($this->is_show_menu()) {
			if (!($this->_menu instanceof Force_Menu_Form)) {
				$this->_menu = Force_Menu_Form::factory();
			}
			if (!empty($this->_back_url)) {
				$this->_menu->back_url($this->_back_url);
			}
		}

		$controls = $this->get_controls();

		$form_body = $this->_get_form_body($controls);

		if ($this->_has_required_fields && $this->_show_required_message) {
			$form_body .= Force_Form_Note::factory()
				->simple()
//				->form_horizontal($this->is_form_horizontal())
				->alert_warning()
				->value(__('form.marked_fields_are_required'))
				->render();
		}

		$buttons_body = '';

		if ($this->_show_buttons) {
			if ($this->_button_cancel instanceof Force_Button) {
				$this->button_before($this->_button_cancel);
			}
			if ($this->_button_submit_and_stay instanceof Force_Button) {
				$this->button_before($this->_button_submit_and_stay);
			}
			if ($this->_button_submit instanceof Force_Button) {
				$this->button_before($this->_button_submit);
			}
			if (!empty($this->_buttons)) {
				$buttons_body = implode("\n", $this->_buttons);
			}
		}

		if (!empty($buttons_body)) {
			$buttons_section = Force_Form_Section::factory(__('form.actions'))
				->name('form-actions');
			if ($this->_show_buttons_label) {
				$buttons_section->show_label();
			} else {
				$buttons_section->hide_label();
			}
			if ($this->_is_preset_for_admin) {
				$buttons_section->preset_for_admin();
			}
			$buttons_body = $buttons_section->render($buttons_body);
		}

		if ($this->is_show_menu()) {
			if (!empty($buttons_body) && $this->_show_buttons_label) {
				$this->_menu->add_divider();
				$this->_menu->item('form-actions')
					->link('#form-actions')
					->i18n(__('form.actions'));
			}
			$menu = $this->_menu->render();
		}

		if (!empty($this->_name)) {
			$this->attribute('name', $this->_name);
		}

		$this->attribute('role', 'form');
		$this->attribute('enctype', 'multipart/form-data');

		/*
		 * Каждый компонент теперь сам определяет свой вид
		 * Параметр form-horizontal передаётся в дочерние
		 * компоненты если этот параметр у них равен null
		 * см. Force_Form_Control
		 */
//		if ($this->is_form_horizontal()) {
//			$this->attribute('class', 'form-horizontal');
//		}

		if ($this->_simple) {
			$html = array();
			$html[] = Form::open($this->get_form_action(), $this->get_attributes());
			$html[] = $form_body;
			$html[] = $buttons_body;
			$html[] = Form::close();

			return implode("\n", $html);
		}

		return View::factory(FORCE_VIEW . 'form/default')
			->set('attributes', $this->get_attributes())
			->set('form_action', $this->get_form_action())
			->set('form_title', $this->get_label())
			->set('form_horizontal', $this->is_form_horizontal())
			->set('show_label', $this->is_show_label())
			->set('show_menu', $this->is_show_menu())
			->bind('form_body', $form_body)
			->bind('buttons_body', $buttons_body)
			->bind('menu', $menu)
			->render();
	}

	/*
	 * FORM SPECIFIC ATTRIBUTES
	 */

	public function form_action($form_action) {
		$this->attribute('action', (string)$form_action);
		return $this;
	}

	public function form_method($form_method) {
		$this->attribute('method', strtolower($form_method));
		return $this;
	}

	public function get_form_action() {
		return $this->get_attribute('action');
	}

	public function get_form_method() {
		return $this->get_attribute('method');
	}

	public function no_cache() {
		$this->attribute('autocomplete', 'off', true);
		return $this;
	}

	/*
	 * MENU
	 */

	public function show_menu($value = true) {
		$this->_show_menu = boolval($value);
		return $this;
	}

	public function hide_menu() {
		$this->_show_menu = false;
		return $this;
	}

	public function is_show_menu() {
		return $this->_show_menu;
	}

	/*
	 * TITLE
	 */

	public function title($title) {
		$this->_label = (string)$title;
		return $this;
	}

	/*
	 * BUTTONS
	 */

	public function button_submit($caption = null, array $attributes = array()) {
		if (empty($caption)) {
			$caption = __('form.button.save');
		}
		if (is_string($caption)) {
			if ($this->_button_submit instanceof Force_Button) {
				$this->_button_submit
					->label($caption)
					->attributes($attributes);
			} else {
				$this->_button_submit = Force_Button::factory($caption)
					->attributes($attributes)
					->attribute('type', 'submit')
					->btn_primary();
			}
		}
		if ($caption instanceof Force_Button) {
			$this->_button_submit = $caption;
		}
		return $this;
	}

	public function button_submit_and_stay($caption = null, array $attributes = array()) {
		if (empty($caption)) {
			$caption = __('form.button.save_and_continue');
		}
		if (is_string($caption)) {
			if ($this->_button_submit_and_stay instanceof Force_Button) {
				$this->_button_submit_and_stay
					->label($caption)
					->attributes($attributes);
			} else {
				$this->_button_submit_and_stay = Force_Button::factory($caption)
					->attributes($attributes)
					->attribute('type', 'submit')
					->attribute('name', 'save_and_continue')
					->attribute('value', 1)
					->btn_primary();
			}
		}
		if ($caption instanceof Force_Button) {
			$this->_button_submit_and_stay = $caption;
		}
		return $this;
	}

	public function button_cancel($link = null, array $attributes = null, $caption = null) {
		if (empty($link)) {
			$link = Force_URL::get_index_uri();
		}
		$this->_back_url = $link;
		if ($this->_button_cancel instanceof Force_Button) {
			$this->_button_cancel
				->link($link);

			if (!empty($attributes)) {
				$this->_button_cancel->attributes($attributes);
			}
		} else {
			if (empty($caption)) {
				$caption = __('common.cancel');
			}
			$this->_button_cancel = $this->_get_button($caption, $link, $attributes);
		}
		return $this;
	}

	public function show_buttons_label($value = true) {
		$this->_show_buttons_label = boolval($value);
		return $this;
	}

	public function hide_buttons_label() {
		$this->_show_buttons_label = false;
		return $this;
	}

	/*
	 * MESSAGE ABOUT REQUIRED FIELDS
	 */

	protected function show_required_message($value = true) {
		$this->_show_required_message = boolval($value);
	}

	protected function hide_required_message() {
		$this->_show_required_message = false;
	}

	/*
	 * SPECIAL HIDDEN PARAMS
	 */

	protected function _add_max_file_size_param() {
		$max_file_size = Upload::get_max_file_size();
		$this->_hidden['MAX_FILE_SIZE'] = Form::hidden('MAX_FILE_SIZE', $max_file_size);
	}

	/*
	 * GET VALUE
	 */

	public function get_value($name, $default = null) {
		return Arr::get($this->_data_array, $name, $default);
	}

	public function get_values($default = null) {
		$old_names = $this->get_control_names(true);
		$new_names = array();
		foreach ($old_names as $index => $name) {
			$arr_name = strpos($name, '[');
			if ($arr_name !== FALSE) {
				$arr_name = substr($name, 0, $arr_name);
				$new_names[$arr_name] = $arr_name;
			} else {
				$new_names[$name] = $name;
			}
		}

		return Arr::extract($this->_data_array, $new_names, $default);
	}

	/*
	 * PREDEFINED SETUP
	 */

	public function preset_for_admin($show_submit_and_stay_button = true, $show_cancel_button = true, $show_submit = true) {
		$this->_is_preset_for_admin = true;
		$this->show_menu();
		$this->show_buttons_label();
		if (empty($this->get_label())) {
			$this->title(Helper_Admin::get_page_title());
		}
		if ($show_submit) {
			$this->button_submit();
		}
		if ($show_submit_and_stay_button) {
			$this->button_submit_and_stay();
		}
		if ($show_cancel_button) {
			$this->button_cancel();
		}
		$this->form_horizontal();
		return $this;
	}

	public function preset_for_admin_show($show_cancel_button = true) {
		$this->_is_preset_for_admin = true;
		$this->show_menu();
		$this->show_buttons_label();
		if (empty($this->get_label())) {
			$this->title(Helper_Admin::get_page_title());
		}
		if ($show_cancel_button) {
			$this->button_cancel();
		}
		$this->form_horizontal();
		return $this;
	}

	/*
	 * HELPERS
	 */

	public function is_post() {
		return Form::is_post();
	}

	public function is_ready_to_apply() {
		switch ($this->get_form_method()) {
			case 'post':
				return $this->is_post();
			default:
				return true;
		}
	}

	public function redirect($index_path = null, $edit_path = null) {
		if (Helper_Error::has_errors()) {
			return false;
		}

		$save_and_continue = (boolean)$this->get_value('save_and_continue', false);

		if ($save_and_continue) {
			if (empty($edit_path)) {
				$edit_path = Request::current()->uri();
			}
			Request::current()->redirect($edit_path);
		} else {
			if (empty($index_path)) {
				$index_path = Force_URL::current_clean()
					->action('index')
					->get_url();
			}
			Force_URL::back_to_index($index_path);
		}
		return true;
	}

} // End Force_Form_Core
