<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Image
 * User: legion
 * Date: 24.07.14
 * Time: 19:42
 */
class Force_Form_Image extends Force_Form_Control {

	use Force_Attributes_Image;
	use Force_Attributes_Title;

	protected $_use_cdn = false;
	protected $_use_session = false;
	protected $_use_zoom = false;

	protected $_image_type = null;
	protected $_image_type_to_display = null;
	protected $_is_allow_remove_file = true;

	protected $_image = null;
	protected $_default_image = null;

	protected $_view = 'image';
	protected $_icon_class = 'fa-image';

	protected $_title = null;
	protected $_use_title = false;

	public function __construct($name = null, $label = null, $image_type = null, $file_name = null) {
		$this->name($name);
		$this->label($label);
		$this->image_type($image_type);
		$this->value($file_name);
		$this->attribute('class', 'form-control');
		$this->title_attribute('class', 'form-control');
		$this->title_attribute('placeholder', __('common.description'));
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, $image_type = null, $file_name = null) {
		return new self($name, $label, $image_type, $file_name);
	}

	protected function _render_simple() {
		return FORM::file($this->get_name(), $this->get_attributes());
	}

	public function render() {
		$this->attribute('type', 'file');
		$this->attribute('class', 'input-fake');
		$this->attribute('name', $this->get_name());
		$this->attribute('value', '');

		if (!$this->_simple) {
			$this->_use_zoom();
		}

		$this->_view_data = [
			'image_types' => $this->_get_image_types(),
			'zoom' => $this->_use_zoom,
			'image' => $this->_image,
			'default_image' => $this->_default_image,
			'is_allow_remove' => $this->_is_allow_remove_file,
			'name_remove' => $this->get_name_remove_image(),
			'image_attributes' => $this->get_image_attributes(),
			'use_title' => $this->is_use_title(),
			'title_name' => $this->get_title_name(),
			'title_value' => $this->get_title(),
			'title_attributes' => $this->get_title_attributes(),
		];

		return parent::render();
	}

	/*
	 * RENDER PARAMS
	 */

	protected function _get_image_types() {
		if (!empty($this->_image_type_to_display)) {
			$image_types = array(
				$this->_image_type_to_display,
			);
		} else {
			if (empty($this->_image_type)) {
				$this->_image_type = $this->_name;
			}

			if (empty($this->_image_type)) {
				$image_types = array();
			} else {
				$crop_types = Kohana::$config->load('images.' . $this->_image_type . '.crop.types');
				if (empty($crop_types)) {
					$crop_types = array();
				}
				$image_types = array_merge(array($this->_image_type), $crop_types);
			}
		}

		return $image_types;
	}

	protected function _get_view() {
		$view_dir = 'image';
		$view = self::CONTROLS_VIEW . $view_dir;

		if ($this->_simple) {
			$view .= '/simple';
		} else {
			if ($this->_use_cdn) {
				$view .= '/cdn';
				if (!empty($description)) {
					$this->_description .= "\n";
				}
				if (Helper_Image::check_cdn_write_permission()) {
					$this->_description = $this->_description . __('form.image.cdn.description');
				} else {
					$this->_description = $this->_description . Helper_Bootstrap::get_warning_message(__('form.image.cdn.description.read.only'));
				}
			} elseif ($this->_use_session) {
				$view .= '/public';
			}
		}

		return $view;
	}

	protected function _use_zoom() {
		if ($this->_use_zoom) {
			Helper_Assets::add_styles('assets/admin/css/jquery.imageZoom.css');
			Helper_Assets::add_scripts(array(
				'assets/admin/js/imagezoom/imagezoom.js',
				'assets/admin/js/href.image_zoom.js',
			));
		}
	}

	/*
	 * FACTORY PARAMS
	 */

	public function image_type($image_type) {
		$this->_image_type = (string)$image_type;
		return $this;
	}

	public function image_type_to_display($image_type) {
		$this->_image_type_to_display = (string)$image_type;
		return $this;
	}

	public function get_image_type() {
		return $this->_image_type;
	}

	public function image($src) {
		$this->_image = (string)$src;
		return $this;
	}

	public function default_image($filename_in_static_path) {
		$this->_default_image = (string)$filename_in_static_path;
		return $this;
	}

	/*
	 * REMOVE
	 */

	public function get_name_remove_image() {
		return $this->get_name() . '_remove_image';
	}

	public function allow_remove_image($value = true) {
		$this->_is_allow_remove_file = boolval($value);
		return $this;
	}

	/*
	 * TITLE
	 */

	public function use_title($value = true) {
		$this->_use_title = boolval($value);
		return $this;
	}

	public function is_use_title() {
		return $this->_use_title;
	}

	public function get_title_name() {
		return $this->get_name() . '-title';
	}

	public function get_title() {
		return $this->_title;
	}

	public function apply_title_before_save(Force_Form_Core $form) {
		$this->title($form->get_value($this->get_title_name()));
		return $this->_title;
	}

	public function title($value) {
		$this->_title = trim($value);
		return $this;
	}

	/*
	 * CDN
	 */

	public function use_cdn($value = true) {
		$this->_use_cdn = boolval($value);
		return $this;
	}

	public function is_use_cdn() {
		return $this->_use_cdn;
	}

	/*
	 * SESSION IMAGE
	 */

	public function use_session($value = true) {
		$this->_use_session = boolval($value);
		return $this;
	}

	public function is_use_session() {
		return $this->_use_session;
	}

	/*
	 * IMAGE
	 */

	public function move_image_to_cdn(Jelly_Model $model, $name) {
		if (!empty($model->{$name})) {
			$image_type = $this->get_image_type();
			if (!empty($image_type)) {
				try {
					Helper_Image::copy_image_to_cdn($model->{$name}, $image_type);
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}
			}
		}
		return true;
	}

	public function session_image(Jelly_Model $model, $name) {
		if (!empty($model->{$name})) {
			$image_type = $this->get_image_type();
			if (!empty($image_type)) {
				Helper_Image::add_session_image($model->{$name}, $image_type);
			}
		}
		return true;
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$this->apply_title_before_save($form);

		$image_type = $this->get_image_type();

		if (empty($image_type)) {
			return false;
		}

		$required = $this->is_required();

		$remove = (boolean)$form->get_value($this->get_name_remove_image(), false);
		$old_filename = $old_value;
		$old_file_removed = false;
		$new_filename = '';

		if ($remove) {
			$old_file_removed = Helper_Image::remove_image($old_filename, $image_type);
		} else {
			if (!array_key_exists($this->get_name(), $_FILES)) {
				return false;
			}

			$new_filename = Helper_Image::upload($_FILES[$this->get_name()], $old_filename, $image_type, $this->get_name(), $this->get_label(), $required);
		}

		if (empty($new_filename) && !$old_file_removed) {
			$new_filename = $old_filename;
		}

		$this->value((string)$new_filename);
		return $this->get_value();
	}

	public function apply_before_save(Force_Form_Core $form, Jelly_Model $model) {
		if ($this->is_read_only()) {
			return false;
		}

		$name = $this->get_name();
		$image_type = $this->get_image_type();
		if (empty($image_type)) {
			$image_type = $model->meta()->model() . '_' . $name;
			$this->image_type($image_type);
		}

		$filename = $this->apply_value_before_save($form, null, $model->{$name});

		$model->set($name, $filename);
		return $filename;
	}

	/*
	 * !!! Возвращаемое значение определяет нужно ли пересохранять модель или нет
	 */
	public function apply_after_save(Force_Form_Core $form, Jelly_Model $model) {
		if ($this->is_use_session()) {
			$this->session_image($model, $this->get_name());
		}
		if ($this->is_use_cdn()) {
			$this->move_image_to_cdn($model, $this->get_name());
		}
		return false;
	}

	/*
	 * ARRAY
	 */

	public function as_array() {
		$data = parent::as_array();
		$data['image_type'] = $this->get_image_type();
		$data['title'] = $this->get_title();
		return $data;
	}

	public function parse_array(array $data) {
		parent::parse_array($data);
		$this->image_type(Arr::get($data, 'image_type'));
		$this->title(Arr::get($data, 'title'));
		return $this;
	}

	/*
	 * VALUE to HTML
	 */

	public function transform_to_html($value = null, array $attributes = null) {
		if (is_null($value)) {
			$value = $this->get_value();
		}
		return $value;
//		return Helper_Image::get_image($value, $this->get_image_type(), $this->get_label(), $attributes);
	}

} // End Force_Form_Image
