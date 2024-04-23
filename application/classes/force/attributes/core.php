<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Attributes_Core
 * User: legion
 * Date: 26.09.14
 * Time: 18:23
 */
class Force_Attributes_Core {

	/*
	 * SET
	 */

	public static function set_attributes(array &$attributes, array $attributes_set, $overwrite = null) {
		foreach ($attributes_set as $key => $value) {
			self::set_attribute($attributes, $key, $value, $overwrite);
		}
		return true;
	}

	public static function set_attribute(array &$attributes, $key, $value = null, $overwrite = null) {
		if (is_null($value)) {
			$value = '';
		}
		$key = strtolower(trim($key));
		switch ($key) {
			case 'class':
				self::set_attribute_class($attributes, $value, $overwrite);
				break;
			case 'style':
				self::set_attribute_style($attributes, $value, $overwrite);
				break;
			default:
				/*
				 * Нерешительная установка параметра.
				 * Если такой параметр уже найден, то не трогать его.
				 * Полезно в рендерах, для установки значений по умолчанию.
				 */
				if ($overwrite === false && array_key_exists($key, $attributes)) {
					return false;
				}
				if (is_string($value)) {
					$value = trim($value);
				}
				if (is_string($value) || is_numeric($value) || is_bool($value)) {
					$attributes[$key] = $value;
				}
				break;
		}
		return true;
	}

	/*
	 * GET
	 */

	public static function get_attribute(array &$attributes, $key, $default = null) {
		$key = strtolower(trim($key));
		switch ($key) {
			case 'class':
				return self::get_attribute_class($attributes, $default);
				break;
			case 'style':
				return self::get_attribute_style($attributes, $default);
				break;
			default:
				if (array_key_exists($key, $attributes)) {
					return $attributes[$key];
				}
		}
		return $default;
	}

	public static function get_attributes(array &$attributes) {
		$_attributes = $attributes;
		if (array_key_exists('class', $_attributes)) {
			$_attributes['class'] = self::get_attribute_class($attributes);
		}
		if (array_key_exists('style', $_attributes)) {
			$_attributes['style'] = self::get_attribute_style($attributes);
		}
		return $_attributes;
	}

	public static function get_attributes_merge(array $attributes, array $additional_attributes = array(), $overwrite = null) {
		if (!empty($additional_attributes)) {
			foreach ($additional_attributes as $key => $value) {
				self::set_attribute($attributes, $key, $value, $overwrite);
			}
		}
		return self::get_attributes($attributes);
	}

	/*
	 * REMOVE
	 */

	public static function remove_attribute(array &$attributes, $key) {
		if (array_key_exists($key, $attributes)) {
			unset($attributes[$key]);
		}
		return true;
	}

	/*
	 * CLASSES
	 */

	public static function set_attribute_class(array &$attributes, $class, $clean_classes = false) {
		if ($clean_classes === true || !array_key_exists('class', $attributes)) {
			$attributes['class'] = array();
		}

		if (!empty($class)) {
			if (is_string($class)) {
				$class = explode(' ', $class);
			}
			if (is_array($class)) {
				foreach ($class as $_value) {
					$_value = trim($_value);
					$attributes['class'][$_value] = $_value;
				}
			}
		}

		return true;
	}

	public static function get_attribute_class(array &$attributes, $default = null) {
		$class = $default;
		if (array_key_exists('class', $attributes) && is_array($attributes['class'])) {
			$class = implode(' ', $attributes['class']);
		}
		return $class;
	}

	public static function update_attribute_class(array &$attributes, $class) {
		if (!array_key_exists('class', $attributes)) {
			$attributes['class'] = '';
		}
		if (is_array($attributes['class'])) {
			return self::set_attribute_class($attributes, $class);
		} elseif (!is_string($attributes['class'])) {
			return false;
		}

		$classes = explode(' ', $attributes['class']);

		if (!empty($class)) {
			if (is_string($class)) {
				$class = explode(' ', $class);
			}
			if (is_array($class)) {
				foreach ($class as $_value) {
					$_value = trim($_value);
					$classes[$_value] = $_value;
				}
			}
		}

		$attributes['class'] = implode(' ', $classes);

		return true;
	}

	/**
	 * @param array $attributes
	 *
	 * @return array
	 */
	public static function get_attribute_class_as_array(array &$attributes) {
		if (array_key_exists('class', $attributes) && is_array($attributes['class'])) {
			return $attributes['class'];
		}
		return array();
	}

	public static function remove_attribute_class(array &$attributes, $class) {
		if (empty($class) || !array_key_exists('class', $attributes) || !is_array($attributes['class'])) {
			return false;
		}

		if (is_string($class)) {
			$class = array($class);
		}

		if (is_array($class)) {
			foreach ($class as $_class) {
				$_class = trim($_class);
				if (array_key_exists($_class, $attributes['class'])) {
					unset($attributes['class'][$_class]);
				}
			}
		}

		return true;
	}

	public static function replace_attribute_class(array &$attributes, $classes_replaced, $by_class, $clean_classes = false) {
		if (!$clean_classes) {
			self::remove_attribute_class($attributes, $classes_replaced);
		}
		self::set_attribute_class($attributes, $by_class, $clean_classes);
		return true;
	}

	/*
	 * STYLES
	 */

	public static function set_attribute_style(array &$attributes, $style, $clean_styles = false) {
		if ($clean_styles === true || !array_key_exists('style', $attributes)) {
			$attributes['style'] = array();
		}

		if (!empty($style)) {
			if (is_string($style)) {
				$style = explode(';', $style);
			}
			if (is_array($style)) {
				foreach ($style as $_key => $_value) {
					if (is_string($_value) && !empty($_value)) {
						$parts = explode(':', $_value);
						if (count($parts) == 2) {
							$key = trim($parts[0]);
							$value = trim($parts[1]);
							$attributes['style'][$key] = $value;
						} elseif (count($parts) == 1) {
							$key = trim($_key);
							$value = trim($_value);
							$attributes['style'][$key] = $value;
						}
					}
				}
			}
		}
		return true;
	}

	public static function get_attribute_style(array &$attributes, $default = null) {
		$style = $default;
		if (array_key_exists('style', $attributes) && is_array($attributes['style'])) {
			$styles = array();
			foreach ($attributes['style'] as $key => $value) {
				$styles[] = $key . ':' . $value;
			}
			$style = implode(';', $styles);
		}
		return $style;
	}

	public static function update_attribute_style(array &$attributes, $style) {
		if (!array_key_exists('style', $attributes)) {
			$attributes['style'] = '';
		}
		if (is_array($attributes['style'])) {
			return self::set_attribute_style($attributes, $style);
		} elseif (!is_string($attributes['style'])) {
			return false;
		}

		$styles = explode(';', $attributes['style']);

		if (!empty($style)) {
			if (is_string($style)) {
				$style = explode(';', $style);
			}
			if (is_array($style)) {
				foreach ($style as $_key => $_value) {
					if (is_string($_value)) {
						$parts = explode(':', $_value);
						if (count($parts) == 2) {
							$key = trim($parts[0]);
							$value = trim($parts[1]);
							$attributes['style'][$key] = $value;
						} elseif (count($parts) == 1) {
							$key = trim($_key);
							$value = trim($_value);
							$attributes['style'][$key] = $value;
						}
					}
				}
			}
		}

		$attributes['class'] = implode(';', $styles);

		return true;
	}

	/**
	 * @param array $attributes
	 *
	 * @return array
	 */
	public static function get_attribute_style_as_array(array &$attributes) {
		if (array_key_exists('style', $attributes) && is_array($attributes['style'])) {
			return $attributes['style'];
		}
		return array();
	}

	public static function remove_attribute_style(array &$attributes, $style_key) {
		if (empty($style_key) || !array_key_exists('style', $attributes) || !is_array($attributes['style'])) {
			return false;
		}

		if (is_string($style_key)) {
			$style_key = array($style_key);
		}

		if (is_array($style_key)) {
			foreach ($style_key as $_key) {
				$_key = trim($_key);
				if (array_key_exists($_key, $attributes['style'])) {
					unset($attributes['style'][$_key]);
				}
			}
		}

		return true;
	}

	/*
	 * EXISTS
	 */

	public static function attribute_exists(array &$attributes, $key) {
		return array_key_exists($key, $attributes);
	}

	/*
	 * RENDER
	 */

	public static function render_attributes(array $attributes, array $additional_attributes = array(), $overwrite = null) {
		$attributes = self::get_attributes_merge($attributes, $additional_attributes, $overwrite);
		return HTML::attributes($attributes);
	}

} // End Force_Attributes_Core
