<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes_Modal
 * User: legion
 * Date: 16.08.17
 * Time: 9:02
 */
trait Force_Attributes_Modal {

	/*
	 * Доступ только через функции.
	 */
	private $_modal_attributes = array();

	/*
	 * SET
	 */

	public function modal_attribute($key, $value = null, $overwrite = null) {
		Force_Attributes_Core::set_attribute($this->_modal_attributes, $key, $value, $overwrite);
		return $this;
	}

	public function modal_attributes(array $attributes, $overwrite = null) {
		Force_Attributes_Core::set_attributes($this->_modal_attributes, $attributes, $overwrite);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_modal_attribute($key, $default = null) {
		return Force_Attributes_Core::get_attribute($this->_modal_attributes, $key, $default);
	}

	public function get_modal_attributes() {
		return Force_Attributes_Core::get_attributes($this->_modal_attributes);
	}

	public function get_modal_attributes_merge(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::get_attributes_merge($this->_modal_attributes, $attributes, $overwrite);
	}

	/*
	 * REMOVE
	 */

	public function remove_modal_attribute($key) {
		Force_Attributes_Core::remove_attribute($this->_modal_attributes, $key);
		return $this;
	}

	/*
	 * CLASSES
	 */

	public function get_modal_attribute_class_as_array() {
		return Force_Attributes_Core::get_attribute_class_as_array($this->_modal_attributes);
	}

	public function remove_modal_attribute_class($class) {
		Force_Attributes_Core::remove_attribute_class($this->_modal_attributes, $class);
		return $this;
	}

	public function replace_modal_attribute_class($classes_replaced, $by_class, $clean_classes = false) {
		Force_Attributes_Core::replace_attribute_class($this->_modal_attributes, $classes_replaced, $by_class, $clean_classes);
		return $this;
	}

	/*
	 * STYLES
	 */

	public function get_modal_attribute_style_as_array() {
		return Force_Attributes_Core::get_attribute_style_as_array($this->_modal_attributes);
	}

	public function remove_modal_attribute_style($style_key) {
		Force_Attributes_Core::remove_attribute_style($this->_modal_attributes, $style_key);
		return $this;
	}

	/*
	 * EXISTS
	 */

	public function modal_attribute_exists($key) {
		return Force_Attributes_Core::attribute_exists($this->_modal_attributes, $key);
	}

	/*
	 * RENDER
	 */

	public function render_modal_attributes(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::render_attributes($this->_modal_attributes, $attributes, $overwrite);
	}

} // End Force_Attributes_Modal
