<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes_Title
 * User: legion
 * Date: 14.08.17
 * Time: 21:42
 */
trait Force_Attributes_Title {

	/*
	 * Доступ только через функции.
	 */
	private $_title_attributes = array();

	/*
	 * SET
	 */

	public function title_attribute($key, $value = null, $overwrite = null) {
		Force_Attributes_Core::set_attribute($this->_title_attributes, $key, $value, $overwrite);
		return $this;
	}

	public function title_attributes(array $attributes, $overwrite = null) {
		Force_Attributes_Core::set_attributes($this->_title_attributes, $attributes, $overwrite);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_title_attribute($key, $default = null) {
		return Force_Attributes_Core::get_attribute($this->_title_attributes, $key, $default);
	}

	public function get_title_attributes() {
		return Force_Attributes_Core::get_attributes($this->_title_attributes);
	}

	public function get_title_attributes_merge(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::get_attributes_merge($this->_title_attributes, $attributes, $overwrite);
	}

	/*
	 * REMOVE
	 */

	public function remove_title_attribute($key) {
		Force_Attributes_Core::remove_attribute($this->_title_attributes, $key);
		return $this;
	}

	/*
	 * CLASSES
	 */

	public function get_title_attribute_class_as_array() {
		return Force_Attributes_Core::get_attribute_class_as_array($this->_title_attributes);
	}

	public function remove_title_attribute_class($class) {
		Force_Attributes_Core::remove_attribute_class($this->_title_attributes, $class);
		return $this;
	}

	public function replace_title_attribute_class($classes_replaced, $by_class, $clean_classes = false) {
		Force_Attributes_Core::replace_attribute_class($this->_title_attributes, $classes_replaced, $by_class, $clean_classes);
		return $this;
	}

	/*
	 * STYLES
	 */

	public function get_title_attribute_style_as_array() {
		return Force_Attributes_Core::get_attribute_style_as_array($this->_title_attributes);
	}

	public function remove_title_attribute_style($style_key) {
		Force_Attributes_Core::remove_attribute_style($this->_title_attributes, $style_key);
		return $this;
	}

	/*
	 * EXISTS
	 */

	public function title_attribute_exists($key) {
		return Force_Attributes_Core::attribute_exists($this->_title_attributes, $key);
	}

	/*
	 * RENDER
	 */

	public function render_title_attributes(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::render_attributes($this->_title_attributes, $attributes, $overwrite);
	}

} // End Force_Attributes_Title
