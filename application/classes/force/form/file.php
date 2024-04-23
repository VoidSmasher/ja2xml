<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_File
 * User: telepat
 * Date: 10.05.17
 * Time: 18:45
 */
class Force_Form_File extends Force_Form_Control {

	use Force_File_Core;

	protected $_view = 'file';
	protected $_icon_class = 'fa-file-o';

	protected $_is_allow_remove_file = true;

	public function __construct($name = null, $label = null, $file_type = null, $file_name = null) {
		$this->name($name);
		$this->label($label);
		$this->value($file_name);
		$this->file_type($file_type);
		$this->attribute('class', 'form-control');
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, $file_type = null, $file_name = null) {
		return new self($name, $label, $file_type, $file_name);
	}

	protected function _render_simple() {
		return FORM::file($this->get_name(), $this->get_attributes());
	}

	public function render() {
		$this->attribute('type', 'file');
		$this->attribute('class', 'input-fake');
		$this->attribute('name', $this->get_name());
		$this->attribute('value', '');

		$accept = $this->get_accepted_mime();
		if (!empty($accept)) {
			$this->attribute('accept', implode(', ', $accept));
		}

		$this->_view_data = [
			'path' => $this->get_dir(),
			'name_remove' => $this->get_name_remove_file(),
			'is_allow_remove' => $this->_is_allow_remove_file,
		];

		return parent::render();
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$remove = (boolean)$form->get_value($this->get_name_remove_file(), false);
		$old_filename = DOCROOT . $this->get_dir() . $old_value;
		$old_file_removed = false;

		/*
		 * Удаляем старый файл по запросу
		 */
		if ($remove) {
			$remove = file_exists($old_filename);
			if ($remove) {
				$old_file_removed = @unlink($old_filename);
			} else {
				$old_file_removed = true;
			}
			$this->value(false);
		}

		/*
		 * Загружаем новый файл, если он был передан
		 */
		$file = Arr::get($_FILES, $this->get_name());
		$name = Arr::get($file, 'name');
		if (is_array($file) && !empty($name)) {
			$this->file($file);
			$new_value = $this->upload($old_value);
			$new_value = basename($new_value);

			if ($new_value && ($new_value != $old_value) && file_exists($old_filename)) {
				@unlink($old_filename);
			}
		}

		if (empty($new_value) && !$old_file_removed) {
			$new_value = $old_value;
		}

		$this->value((string)$new_value);
		return $this->get_value();
	}

	public function apply_before_save(Force_Form_Core $form, Jelly_Model $model) {
		if ($this->is_read_only()) {
			return false;
		}

		$name = $this->get_name();
		$file_type = $this->get_file_type();
		if (empty($file_type)) {
			$file_type = $model->meta()->model() . '_' . $name;
			$this->file_type($file_type);
		}

		$filename = $this->apply_value_before_save($form, null, $model->{$name});

		$model->set($name, $filename);
		return $filename;
	}

	/*
	 * REMOVE
	 */

	public function get_name_remove_file() {
		return $this->get_name() . '_remove_file';
	}

	public function allow_remove_file($value = true) {
		$this->_is_allow_remove_file = boolval($value);
		return $this;
	}

} // End Force_Form_File
