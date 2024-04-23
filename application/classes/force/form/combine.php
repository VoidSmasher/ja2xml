<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Combine
 * User: legion
 * Date: 24.01.17
 * Time: 20:55
 */
class Force_Form_Combine extends Force_Form_Combine_Controls {

	protected $_view = 'combine/block';
	protected $_icon_class = 'fa-minus';
	protected $_allow_combine = false;

	protected $_show_selector_labels = true;

	public function __construct($name = null, $label = null, array $controls = array(), $value = null) {
		$this->name($name);
		$this->value($value);
		$this->label($label);
		$this->attribute('class', 'combine-body');

		$this->_allow_string_controls = false;
		$this->_control_name_as_control_id = true;
		$this->add_controls($controls);
	}

	public static function factory($name = null, $label = null, array $controls = array(), $value = null) {
		return new self($name, $label, $controls, $value);
	}

	public function __set($name, $value) {
		$this->_set_controls($name, $value);
	}

	protected function _render_simple() {
		return false;
	}

	public function render() {
		$selector = $this->_render_combine_selector();

		/*
		 * Здесь собираем рендеры компонентов для работы селектора на js
		 */
		$selector_list = array();
		$selector_controls = array();

		$controls = $this->get_controls();
		$control_id = 'cmbnew';

		foreach ($controls as $name => $control) {
			if ($control instanceof Force_Form_Control) {
				$type = $control->get_type();
				$control_render = $this->_render_combine_group($control, $type, $control_id, $selector);

				$selector_list[] = $type;
				$selector_controls[$type] = array(
					'type' => $type,
					'icon' => $control->get_icon(),
					'render' => $this->_render_group($control_id, $control_render),
					'params' => $control->get_js_params(),
				);
			}
		}

		/*
		 * Здесь собираем уже сами контролы - те, что были загружены через value()
		 */
		$control_id = 0;

		$combine_controls = $this->get_combine_controls();
		$combine_ready = [
			'selector' => $this->_render_group($control_id, $selector),
		];

		foreach ($combine_controls as $name => $control) {
			$control_id++;
			if ($control instanceof Force_Form_Control) {
				$type = $control->get_type();
				$control_render = $this->_render_combine_group($control, $type, $control_id, $selector);
				$combine_ready[$name] = $this->_render_group($control_id, $control_render);
			}
		}

		$combine_render = implode("\n", $combine_ready);

		Helper_Assets::add_js_vars('combine_' . $this->get_name(), [
			'last_control_id' => $control_id,
			'types' => $selector_list,
			'controls' => $selector_controls,
			'selector' => $selector,
			'name' => $this->get_name(),
		]);

		Helper_Assets::add_scripts('assets/common/js/form.combine.js');

		$section = Force_Form_Section::factory($this->get_label())
			->name($this->get_name())
			->attributes($this->get_attributes())
			->form_horizontal($this->is_form_horizontal());

		if ($this->_is_preset_for_admin) {
			$section->preset_for_admin();
		}

		return $section->render($combine_render);
	}

	protected function _render_group($control_id, $group_body) {
		return View::factory(self::CONTROLS_VIEW . 'combine/group')
			->set('group_attributes', [
				'class' => 'combine-group',
				'data-name' => $this->get_name(),
				'data-id' => $control_id,
			])
			->set('group_body', $group_body)
			->render();
	}

	protected function _render_combine_group(Force_Form_Control $control, $type, $control_id, $selector) {
		$hidden_name = $this->_get_hidden_name($this->get_name(), $type, $control_id);
		$hidden_value = '1';

		$control->name($this->_get_control_name($this->get_name(), $type, $control_id));
		$control->attribute('class', 'combine-control');
		$control->form_horizontal($this->is_form_horizontal());

		$this->_update_control_label($control, $control->is_show_label());

		return View::factory(self::CONTROLS_VIEW . 'combine/control')
			->set('control', $control->render())
			->set('control_id', $control_id)
			->set('selector', $selector)
			->set('hidden_name', $hidden_name)
			->set('hidden_value', $hidden_value)
			->set('hidden_attributes', [
				'class' => 'combine-hidden',
			])
			->render();
	}

	protected function _render_combine_selector() {
		$attributes = [
			'class' => 'combine-selector',
		];
		$controls = $this->get_controls();
		foreach ($controls as $control) {
			if ($control instanceof Force_Form_Control) {
				$this->_update_control_label($control, $this->is_show_selector_labels());
			}
		}
		return View::factory(self::CONTROLS_VIEW . 'combine/selector')
			->set('attributes', $attributes)
			->set('controls', $this->get_controls())
			->render();
	}

	/*
	 * PARSE VALUE
	 */

	public function value($json) {
		if (is_string($json)) {
			$this->_value = $this->_parse_json($json, true);
		}

		return $this;
	}

//	public function get_value() {
//		return json_encode($this->_value, JSON_UNESCAPED_UNICODE);
//	}

	protected function _parse_json($json, $add_controls = false) {
		if ($add_controls) {
			$this->remove_combine_controls();
		}

		$saved_data = json_decode($json, true, 65535);

		if (!is_array($saved_data)) {
			$saved_data = array();
		}

		if (empty($saved_data)) {
			return null;
		}

		$control_id = 1;

		$parsed_data = array();

		foreach ($saved_data as $index => $node) {
			$type = Arr::get($node, 'type');

			$control = $this->_create_control_by_type($type);

			if (!($control instanceof Force_Form_Control)) {
				continue;
			}

			$control->parse_array($node);

			$name = $this->_get_control_name($this->get_name(), $type, $control_id);

			if ($add_controls) {
				$this->add_combine_control($control, $name);
			}

			$parsed_data[$name] = $node;

			$control_id++;
		}

		if (empty($parsed_data)) {
			return null;
		} else {
			return $parsed_data;
		}
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		if (empty($new_value) || !is_array($new_value)) {
			$new_value = array();
		}
		if (empty($old_value) || !is_array($old_value)) {
			$old_value = array();
		}

		$data = array();

		foreach ($new_value as $_control_name => $value) {
			$control_name_parts = explode('-', $_control_name);
			$control_id = Arr::get($control_name_parts, 0);
			$type = Arr::get($control_name_parts, 1);
			$control_name = $this->_get_control_name($this->get_name(), $type, $control_id);

			$new_control_value = trim($form->get_value($control_name));

			$control = $this->_create_control_by_type($type);

			if (!($control instanceof Force_Form_Control)) {
				continue;
			}

			$control->name($control_name);

			$old_control = Arr::get($old_value, $control_name, [
				'type' => '',
				'value' => '',
			]);

			$old_control_value = Arr::get($old_control, 'value');

			$new_control_value = $control->apply_value_before_save($form, $new_control_value, $old_control_value);

			if (!empty($new_control_value)) {
				$data[] = $control->as_array();
			}
		}

		if (!empty($data)) {
			$value = json_encode($data, JSON_UNESCAPED_UNICODE);
		} else {
			$value = '';
		}

//		$this->value($value);
//		return $this->get_value();
		$this->_value = $data;
		return $value;
	}

	public function apply_before_save(Force_Form_Core $form, Jelly_Model $model) {
		if ($this->is_read_only()) {
			return false;
		}

		$name = $this->get_name();
		$saved_data = $this->_parse_json($model->{$name});
		$new_data = $form->get_value($name, array());

		$value = $this->apply_value_before_save($form, $new_data, $saved_data);

		if ($this->is_required() && empty($value)) {
			Helper_Error::add(__('field.error.not_empty'), $name, $this->get_label());
		}

		$model->set($name, $value);
		return $value;
	}

	/*
	 * CONTROLS
	 */

	protected function _create_control_by_type($type) {
		$allowed_controls = $this->get_controls();

		if (!array_key_exists($type, $allowed_controls)) {
			return false;
		}

		$given_control = $allowed_controls[$type];

		if (!($given_control instanceof Force_Form_Control)) {
			return false;
		}

		return clone $given_control;
	}

	public static function check_control(&$control) {
		$allowed = false;
		if ($control instanceof Force_Form_Control) {
			$allowed = ($control->is_allow_combine() && !$control->is_read_only());
		}
		return $allowed;
	}

	protected static function _check_control(&$control) {
		if (!self::check_control($control)) {
			if ($control instanceof Force_Form_Control || $control instanceof Force_Form_Container) {
				throw new Exception(get_class($control) . ' is not allowed for Force_Form_Combine');
			} else {
				throw new Exception(var_export($control, true) . ' is not a valid control for Force_Form_Combine');
			}
		}
		return true;
	}

	public function add_control($control, $control_id = null) {
		if (!self::_check_control($control)) {
			return $this;
		}

		$control_id = $control->get_type();
		parent::add_control($control, $control_id);
		return $this;
	}

	/*
	 * TRANSFORM
	 */

	public static function transform_to_array($json) {
		$saved_data = json_decode($json, true, 65535);

		if (!is_array($saved_data)) {
			$saved_data = array();
		}

		$result = array();

		foreach ($saved_data as $data) {
			if (!is_array($data)) {
				continue;
			}

			$type = Arr::get($data, 'type');

			$control = self::create_control_by_type($type);

			if (!($control instanceof Force_Form_Control)) {
				continue;
			}

			$control->parse_array($data);

			$data = $control->as_array();
			$data['value'] = $control->transform_to_html();

			$result[] = $data;
		}

		return $result;
	}

	/*
	 * SELECTOR LABELS
	 */

	public function show_selector_labels($value = true) {
		$this->_show_selector_labels = boolval($value);
		return $this;
	}

	public function hide_selector_labels() {
		$this->_show_selector_labels = false;
		return $this;
	}

	public function is_show_selector_labels() {
		return $this->_show_selector_labels;
	}

} // End Force_Form_Combine
