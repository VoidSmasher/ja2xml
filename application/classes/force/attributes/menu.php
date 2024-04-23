<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes_Menu
 * User: legion
 * Date: 15.11.14
 * Time: 12:47
 */
trait Force_Attributes_Menu {

	/*
	 * Доступ только через функции.
	 */
	private $_menu_attributes = array();

	/*
	 * SET
	 */

	public function menu_attribute($key, $value = null, $overwrite = null) {
		Force_Attributes_Core::set_attribute($this->_menu_attributes, $key, $value, $overwrite);
		return $this;
	}

	public function menu_attributes(array $attributes, $overwrite = null) {
		Force_Attributes_Core::set_attributes($this->_menu_attributes, $attributes, $overwrite);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_menu_attribute($key, $default = null) {
		return Force_Attributes_Core::get_attribute($this->_menu_attributes, $key, $default);
	}

	public function get_menu_attributes() {
		return Force_Attributes_Core::get_attributes($this->_menu_attributes);
	}

	public function get_menu_attributes_merge(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::get_attributes_merge($this->_menu_attributes, $attributes, $overwrite);
	}

	/*
	 * REMOVE
	 */

	public function remove_menu_attribute($key) {
		Force_Attributes_Core::remove_attribute($this->_menu_attributes, $key);
		return $this;
	}

	/*
	 * CLASSES
	 */

	public function get_menu_attribute_class_as_array() {
		return Force_Attributes_Core::get_attribute_class_as_array($this->_menu_attributes);
	}

	public function remove_menu_attribute_class($class) {
		Force_Attributes_Core::remove_attribute_class($this->_menu_attributes, $class);
		return $this;
	}

	public function replace_menu_attribute_class($classes_replaced, $by_class, $clean_classes = false) {
		Force_Attributes_Core::replace_attribute_class($this->_menu_attributes, $classes_replaced, $by_class, $clean_classes);
		return $this;
	}

	/*
	 * STYLES
	 */

	public function get_menu_attribute_style_as_array() {
		return Force_Attributes_Core::get_attribute_style_as_array($this->_menu_attributes);
	}

	public function remove_menu_attribute_style($style_key) {
		Force_Attributes_Core::remove_attribute_style($this->_menu_attributes, $style_key);
		return $this;
	}

	/*
	 * EXISTS
	 */

	public function menu_attribute_exists($key) {
		return Force_Attributes_Core::attribute_exists($this->_menu_attributes, $key);
	}

	/*
	 * RENDER
	 */

	public function render_menu_attributes(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::render_attributes($this->_menu_attributes, $attributes, $overwrite);
	}

} // End Force_Attributes_Menu
