<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Control
 * User: legion
 * Date: 15.07.14
 * Time: 3:40
 */
abstract class Force_Form_Control extends Force_Control {

	use Force_Attributes_Group;
	use Force_Control_Horizontal;
	use Force_Control_Required;

	const CONTROLS_VIEW = 'force/form/controls/';

	protected $_view = 'input';
	protected $_view_data = null;
	protected $_custom_view = null;

	protected $_description = '';
	protected $_allow_null_value = true;
	protected $_allow_combine = true;
	protected $_read_only = false;
	protected $_show_error = true;

	protected $_label_updated = false;

	protected $_icon_class = 'fa-minus';
	protected $_icon_fw = true;

	protected $_is_preset_for_admin = false;

	abstract protected function _render_simple();

	public function render() {
		$this->_update_label();
		$this->_check_for_error();

		/* ===== SIMPLE RENDER ===== */
		if ($this->_simple) {
			return $this->_render_simple();
		}

		$attributes = $this->get_attributes();
		$name = $this->get_name();

		if ($this->is_form_horizontal()) {
			$this->group_attribute('class', 'form-horizontal');
		}

		$this->_view_data['group_attributes'] = $this->render_group_attributes();
		$this->_view_data['attributes'] = $attributes;
		$this->_view_data['show_label'] = $this->_show_label;
		$this->_view_data['label'] = $this->get_label();
		$this->_view_data['name'] = $name;
		$this->_view_data['icon'] = $this->get_icon();
		$this->_view_data['description'] = $this->get_description();
		$this->_view_data['form_horizontal'] = $this->is_form_horizontal();

		if (!array_key_exists('value', $this->_view_data)) {
			$this->_view_data['value'] = $this->get_value();
		}

		$label_attributes = [
			'class' => 'control-label',
		];
		if ($id = Arr::get($attributes, 'id', $name)) {
			$label_attributes['for'] = $id;
		}
		$div_attributes = [];
		if ($this->is_form_horizontal()) {
			$label_attributes['class'] .= ' col-sm-' . $this->get_form_horizontal_label_width();
			$div_attributes['class'] = 'col-sm-' . (12 - $this->get_form_horizontal_label_width());
			if (!$this->is_show_label()) {
				$div_attributes['class'] .= ' col-sm-offset-' . $this->get_form_horizontal_label_width();
			}
		}
		$this->_view_data['label_attributes'] = $label_attributes;
		$this->_view_data['div_attributes'] = $div_attributes;

		return View::factory($this->_get_view(), $this->_view_data)->render();
	}

	/*
	 * HELPERS
	 */

	protected function _update_label() {
		if ($this->_label_updated) {
			return false;
		}
		if (empty($this->_label)) {
			$this->_label = $this->_name;
		}
		$this->_apply_required();
		$this->_label_updated = true;

		if ($this->_show_label) {
			$this->attribute('id', $this->get_name(), false);
		}

		return true;
	}

	protected function _check_for_error() {
		if ($this->_show_error && Helper_Error::has_error($this->get_name())) {
			if ($this->_simple) {
				$this->attribute('class', 'error has-error');
			} else {
				$this->group_attribute('class', 'error has-error');
			}
		}
		return true;
	}

	protected function _get_view() {
		if (!empty($this->_custom_view)) {
			$view = $this->_custom_view;
		} else {
			$view = self::CONTROLS_VIEW . $this->_view;
		}
		return $view;
	}

	/*
	 * NULL VALUE
	 */

	public function allow_null_value($value = true) {
		$this->_allow_null_value = boolval($value);
		return $this;
	}

	public function is_allow_null_value() {
		return $this->_allow_null_value;
	}

	public function is_read_only() {
		return $this->_read_only;
	}

	/*
	 * DESCRIPTION
	 */

	public function description($text, $delimiter = PHP_EOL) {
		$this->_description = Helper_String::to_string($text, $delimiter);
		return $this;
	}

	public function get_description() {
		return $this->_description;
	}

	/*
	 * VIEW
	 */

	public function set_view($path) {
		$this->_custom_view = $path;
		return $this;
	}

	/*
	 * ICON
	 */

	public function icon($icon_class, $fw = true) {
		if (!empty($icon_class)) {
			$this->_icon_class = $icon_class;
		}
		$this->_icon_fw = $fw;
		return $this;
	}

	public function get_icon() {
		return Helper_Bootstrap::get_icon($this->_icon_class, $this->_icon_fw);
	}

	/*
	 * TYPE
	 */

	public function get_type() {
		if (empty($this->_type)) {
			$class = get_class($this);
			$class = strtr($class, array(
				'Force_Form_' => '',
			));
			$this->_type = strtolower($class);
		}
		return $this->_type;
	}

	public static function get_control_class_by_type($type) {
		$class = 'Force_Form_' . $type;
		if (!class_exists($class)) {
			$class = Force_Form_Video::get_control_class_by_type($type);
		}
		return $class;
	}

	public static function create_control_by_type($type) {
		$class = 'Force_Form_' . $type;
		if (class_exists($class)) {
			$control = new $class($type);
		} else {
			$control = Force_Form_Video::create_control_by_type($type);
		}
		return $control;
	}

	/*
	 * COMBINE
	 */

	public function is_allow_combine() {
		return $this->_allow_combine;
	}

	public function as_array() {
		return [
			'type' => $this->get_type(),
			'value' => $this->get_value(),
		];
	}

	public function parse_array(array $data) {
		$this->value(Arr::get($data, 'value'));
		return $this;
	}

	/*
	 * FORM APPLY
	 */

	/*
	 * Идеология следующая:
	 *
	 * apply_value_before_save() обычно не ругается в Helper_Error,
	 * его задача только вернуть значение готовое для сохранения в таблицу.
	 *
	 * Проверка на пустое, если значение необходимо, выполняется в apply_before_save()
	 * Вся ответственность за добавление значения в модель лежит на apply_before_save()
	 *
	 * Однако не стоит забывать, что проверку на пустое модель выполнит сама в момент сохранения,
	 * если поле настроено соответствующим образом. Здесь же проверка на пустое выполняется если
	 * был вызван метод ->required() и неважно был ли он вызван вручную или его вызвал
	 * Jelly_Form_Generator после проверки полей модели. В любом случае задвоения ошибки не будет,
	 * так как в Helper_Error она добавляется во всех случаях одинаково с указанием имени поля.
	 */
	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$this->value($new_value);
		return $this->get_value();
	}

	public function apply_before_save(Force_Form_Core $form, Jelly_Model $model) {
		if ($this->is_read_only()) {
			return false;
		}

		$name = $this->get_name();
		$value = $form->get_value($name);

		$value = $this->apply_value_before_save($form, $value, $model->{$name});

		if ($this->is_required() && empty($value)) {
			Helper_Error::add(__('field.error.not_empty'), $name, $this->get_label());
		}

		$model->set($name, $value);
		return $value;
	}

	public function apply_value_after_save($value) {
		return $value;
	}

	/*
	 * !!! Возвращаемое значение определяет нужно ли пересохранять модель или нет
	 * ??? Только вот вроде как модель не совсем тупая и сама знает надо ли ей
	 * пересохранятся или нет - смотрит по изменениям
	 */
	public function apply_after_save(Force_Form_Core $form, Jelly_Model $model) {
		$name = $this->get_name();
		$value = $form->get_value($name);
		$this->apply_value_after_save($value);
		return false;
	}

	/*
	 * JAVASCRIPT
	 */

	public function get_js_params() {
		return [];
	}

	/*
	 * VALUE to HTML
	 */

	public function transform_to_html($value = null, array $attributes = null) {
		if (is_null($value)) {
			$value = $this->get_value();
		}
		return (string)$value;
	}

	/*
	 * PREDEFINED SETUP
	 */

	public function preset_for_admin() {
		$this->_is_preset_for_admin = true;
		return $this;
	}

} // End Force_Form_Control
