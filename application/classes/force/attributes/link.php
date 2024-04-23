<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes_Link
 * User: legion
 * Date: 28.05.18
 * Time: 12:45
 */
trait Force_Attributes_Link {

	/*
	 * Доступ только через функции.
	 */
	private $_link_attributes = array();

	/*
	 * SET
	 */

	public function link_attribute($key, $value = null, $overwrite = null) {
		Force_Attributes_Core::set_attribute($this->_link_attributes, $key, $value, $overwrite);
		return $this;
	}

	public function link_attributes(array $attributes, $overwrite = null) {
		Force_Attributes_Core::set_attributes($this->_link_attributes, $attributes, $overwrite);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_link_attribute($key, $default = null) {
		return Force_Attributes_Core::get_attribute($this->_link_attributes, $key, $default);
	}

	public function get_link_attributes() {
		return Force_Attributes_Core::get_attributes($this->_link_attributes);
	}

	public function get_link_attributes_merge(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::get_attributes_merge($this->_link_attributes, $attributes, $overwrite);
	}

	/*
	 * REMOVE
	 */

	public function remove_link_attribute($key) {
		Force_Attributes_Core::remove_attribute($this->_link_attributes, $key);
		return $this;
	}

	/*
	 * CLASSES
	 */

	public function get_link_attribute_class_as_array() {
		return Force_Attributes_Core::get_attribute_class_as_array($this->_link_attributes);
	}

	public function remove_link_attribute_class($class) {
		Force_Attributes_Core::remove_attribute_class($this->_link_attributes, $class);
		return $this;
	}

	public function replace_link_attribute_class($classes_replaced, $by_class, $clean_classes = false) {
		Force_Attributes_Core::replace_attribute_class($this->_link_attributes, $classes_replaced, $by_class, $clean_classes);
		return $this;
	}

	/*
	 * STYLES
	 */

	public function get_link_attribute_style_as_array() {
		return Force_Attributes_Core::get_attribute_style_as_array($this->_link_attributes);
	}

	public function remove_link_attribute_style($style_key) {
		Force_Attributes_Core::remove_attribute_style($this->_link_attributes, $style_key);
		return $this;
	}

	/*
	 * EXISTS
	 */

	public function link_attribute_exists($key) {
		return Force_Attributes_Core::attribute_exists($this->_link_attributes, $key);
	}

	/*
	 * RENDER
	 */

	public function render_link_attributes(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::render_attributes($this->_link_attributes, $attributes, $overwrite);
	}

} // End Force_Attributes_Link
