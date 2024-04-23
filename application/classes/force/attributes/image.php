<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes_Image
 * User: legion
 * Date: 14.08.17
 * Time: 21:42
 */
trait Force_Attributes_Image {

	/*
	 * Доступ только через функции.
	 */
	private $_image_attributes = array();

	/*
	 * SET
	 */

	public function image_attribute($key, $value = null, $overwrite = null) {
		Force_Attributes_Core::set_attribute($this->_image_attributes, $key, $value, $overwrite);
		return $this;
	}

	public function image_attributes(array $attributes, $overwrite = null) {
		Force_Attributes_Core::set_attributes($this->_image_attributes, $attributes, $overwrite);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_image_attribute($key, $default = null) {
		return Force_Attributes_Core::get_attribute($this->_image_attributes, $key, $default);
	}

	public function get_image_attributes() {
		return Force_Attributes_Core::get_attributes($this->_image_attributes);
	}

	public function get_image_attributes_merge(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::get_attributes_merge($this->_image_attributes, $attributes, $overwrite);
	}

	/*
	 * REMOVE
	 */

	public function remove_image_attribute($key) {
		Force_Attributes_Core::remove_attribute($this->_image_attributes, $key);
		return $this;
	}

	/*
	 * CLASSES
	 */

	public function get_image_attribute_class_as_array() {
		return Force_Attributes_Core::get_attribute_class_as_array($this->_image_attributes);
	}

	public function remove_image_attribute_class($class) {
		Force_Attributes_Core::remove_attribute_class($this->_image_attributes, $class);
		return $this;
	}

	public function replace_image_attribute_class($classes_replaced, $by_class, $clean_classes = false) {
		Force_Attributes_Core::replace_attribute_class($this->_image_attributes, $classes_replaced, $by_class, $clean_classes);
		return $this;
	}

	/*
	 * STYLES
	 */

	public function get_image_attribute_style_as_array() {
		return Force_Attributes_Core::get_attribute_style_as_array($this->_image_attributes);
	}

	public function remove_image_attribute_style($style_key) {
		Force_Attributes_Core::remove_attribute_style($this->_image_attributes, $style_key);
		return $this;
	}

	/*
	 * EXISTS
	 */

	public function image_attribute_exists($key) {
		return Force_Attributes_Core::attribute_exists($this->_image_attributes, $key);
	}

	/*
	 * RENDER
	 */

	public function render_image_attributes(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::render_attributes($this->_image_attributes, $attributes, $overwrite);
	}

} // End Force_Attributes_Image
