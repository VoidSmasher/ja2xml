<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Jelly_Model
 * User: legion
 * Date: 23.04.12
 * Time: 15:18
 */
class Jelly_Model extends Jelly_Core_Model {

	/*
	 * ORIGINAL
	 */

	public function get_original($name) {
		return (array_key_exists($name, $this->_original)) ? $this->_original[$name] : null;
	}

	public function get_original_array() {
		return $this->_original;
	}

	/*
	 * CHANGED
	 */

	public function get_changed($name) {
		return (array_key_exists($name, $this->_changed)) ? $this->_changed[$name] : null;
	}

	public function get_changed_array() {
		return $this->_changed;
	}

	/*
	 * RETRIEVED
	 */

	public function get_retrieved($name) {
		return (array_key_exists($name, $this->_retrieved)) ? $this->_retrieved[$name] : null;
	}

	public function get_retrieved_array() {
		return $this->_retrieved;
	}

	/*
	 * UNMAPPED
	 */

	public function get_unmapped($name) {
		return (array_key_exists($name, $this->_unmapped)) ? $this->_unmapped[$name] : null;
	}

	public function get_unmapped_array() {
		return $this->_unmapped;
	}

	/*
	 * ADDITIONAL META PARSING
	 */

	public function get_value_from_rules($field, $param, $default = false) {
		$rules = $this->meta()->field($field)->rules;
		return Helper_Jelly::get_rule_value_from_jelly_rules($rules, $param, $default);
	}

	public function get_field_label($field, $default = null) {
		if (is_null($default)) {
			$default = $field;
		}
		$label = $default;

		if ($this->meta()->field($field) instanceof Jelly_Field) {
			$label = $this->meta()->field($field)->label;
		}

		return $label;
	}

	public function get_field_description($field) {
		$description = null;

		if ($this->meta()->field($field) instanceof Jelly_Field) {
			$field = $this->meta()->field($field);
			if (isset($field->description)) {
				$description = $field->description;
			}
		}

		return $description;
	}

	public function get_field_choices($field, array $remove_choices_array = null, $i18n_prefix = null) {
		$choices = array();
		$field = $this->meta()->field($field);
		if ($field instanceof Jelly_Field_Enum) {
			$choices = $field->choices;
		}

		if (is_array($remove_choices_array)) {
			foreach ($remove_choices_array as $choice) {
				if (array_key_exists($choice, $choices)) {
					unset($choices[$choice]);
				}
			}
		}

		if (!empty($i18n_prefix)) {
			foreach ($choices as $index => $choice) {
				$choices[$index] = __($i18n_prefix . $choice);
			}
		}

		return $choices;
	}

	public function get_field_author_choices($field) {
		$choices = array();
		$field = $this->meta()->field($field);
		if ($field instanceof Jelly_Field_Enum) {
			$choices = $field->author_choices;
		}
		return $choices;
	}

	/*
	 * IMAGES
	 */

	protected function _get_image_from_cdn($field_name, $alt, $as_html_tag = FALSE, $attributes = null, $image_type = null) {
		if (empty($image_type)) {
			$image_type = $this->meta()->model() . '_' . $field_name;
		}

		$image_name = $this->{$field_name};

		if ($as_html_tag) {
			return Helper_Image::get_cdn_image($image_name, $image_type, $alt, $attributes);
		} else {
			return Helper_Image::get_cdn_filename($image_name, $image_type);
		}
	}

	protected function _remove_image($field_name, $only_file = true, $image_type = null) {
		if (empty($image_type)) {
			$image_type = $this->meta()->model() . '_' . $field_name;
		}

		$result = true;
		if (!empty($this->{$field_name})) {
			$file_removed = Helper_Image::remove_file($this->{$field_name}, $image_type);
			if (!$only_file && $file_removed) {
				$this->{$field_name} = NULL;
				try {
					$this->save();
				} catch (Jelly_Validation_Exception $e) {
					$result = !Helper_Error::add_from_jelly($this, $e->errors());
				}
			}
		}
		return $result;
	}

	/*
	 * Add a formatted field
	 */

	public function format($field_name, $value) {
		$this->{$field_name . Force_List::FORMAT} = $value;
		return true;
	}

} // End Jelly_Model
