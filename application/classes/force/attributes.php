<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes
 * User: legion
 * Date: 26.09.14
 * Time: 18:23
 */
abstract class Force_Attributes {

	/*
	 * Доступ только через функции.
	 */
	private $_attributes = array();

	/*
	 * SET
	 */

	public function attribute($key, $value = null, $overwrite = null) {
		Force_Attributes_Core::set_attribute($this->_attributes, $key, $value, $overwrite);
		return $this;
	}

	public function attributes(array $attributes, $overwrite = null) {
		Force_Attributes_Core::set_attributes($this->_attributes, $attributes, $overwrite);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_attribute($key, $default = null) {
		return Force_Attributes_Core::get_attribute($this->_attributes, $key, $default);
	}

	public function get_attributes() {
		return Force_Attributes_Core::get_attributes($this->_attributes);
	}

	public function get_attributes_merge(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::get_attributes_merge($this->_attributes, $attributes, $overwrite);
	}

	/*
	 * REMOVE
	 */

	public function remove_attribute($key) {
		Force_Attributes_Core::remove_attribute($this->_attributes, $key);
		return $this;
	}

	/*
	 * CLASSES
	 */

	public function get_attribute_class_as_array() {
		return Force_Attributes_Core::get_attribute_class_as_array($this->_attributes);
	}

	public function remove_attribute_class($class) {
		Force_Attributes_Core::remove_attribute_class($this->_attributes, $class);
		return $this;
	}

	public function replace_attribute_class($classes_replaced, $by_class, $clean_classes = false) {
		Force_Attributes_Core::replace_attribute_class($this->_attributes, $classes_replaced, $by_class, $clean_classes);
		return $this;
	}

	/*
	 * STYLES
	 */

	public function get_attribute_style_as_array() {
		return Force_Attributes_Core::get_attribute_style_as_array($this->_attributes);
	}

	public function remove_attribute_style($style_key) {
		Force_Attributes_Core::remove_attribute_style($this->_attributes, $style_key);
		return $this;
	}

	/*
	 * EXISTS
	 */

	public function attribute_exists($key) {
		return Force_Attributes_Core::attribute_exists($this->_attributes, $key);
	}

	/*
	 * RENDER
	 */

	public function render_attributes(array $attributes = array(), $overwrite = null) {
		return Force_Attributes_Core::render_attributes($this->_attributes, $attributes, $overwrite);
	}

	/*
	 * POPOVER
	 */

	public function popover($title, $content, $position = 'bottom', $trigger = 'hover') {
		if (!empty($title)) {
			$this->attribute('title', $title);
		}
		$this->attribute('data-toggle', 'popover');
		$this->attribute('data-container', 'body');
		$this->attribute('data-placement', $position);
		$this->attribute('data-trigger', $trigger);
		$this->attribute('data-content', $content);
		$this->attribute('data-html', true);
		return $this;
	}

} // End Force_Attributes
