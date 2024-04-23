<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Error
 * User: legion
 * Date: 02.05.12
 * Time: 20:38
 */
class Helper_Error {

	const JELLY_ERROR_I18N_PREFIX = 'jelly.error.';

	protected static $errors = array();
	protected static $can_log_debug_messages = null;

	public static function init() {
		self::set(null);
	}

	public static function set($errors, $label = null) {
		self::$errors = array();
		self::add($errors, null, $label);
	}

	public static function add($error, $name = null, $label = null) {
		if (is_array($error)) {
			foreach ($error as $_error) {
				self::add_one($_error, $name, $label);
			}
		} else {
			self::add_one($error, $name, $label);
		}
	}

	protected static function add_one($text, $name = null, $label = null) {
		if (!is_string($text) && !is_null($text)) {
			return false;
		}
		if (is_array($name)) {
			foreach ($name as $_key) {
				self::error($text, $_key, $label);
			}
		} else {
			self::error($text, $name, $label);
		}
		return true;
	}

	protected static function error($text, $name = null, $label = null) {
		$error = array();
		$error_key = null;
		if (!empty($name) && is_string($name)) {
			$error['name'] = $name;
			$error_key = $name;
		}
		if (!empty($text) && is_string($text)) {
			$error['text'] = $text;
		}
		if (!empty($label) && is_string($label)) {
			$error['label'] = $label;
		}

		if (!empty($error)) {
			if (!empty($error_key)) {
				self::$errors[$error_key] = $error;
			} else {
				self::$errors[] = $error;
			}
		}
	}

	public static function parse_error($error) {
		if (is_string($error)) {
			$text = $error;
		} elseif (is_array($error) && array_key_exists('text', $error)) {
			$text = $error['text'];
			if (array_key_exists('label', $error)) {
				$text = $error['label'] . ': ' . $text;
			}
		} else {
			$text = '';
		}
		return $text;
	}

	public static function get() {
		return array_values(self::$errors);
	}

	/**
	 * @deprecated use get_names instead
	 * @return array
	 */
	public static function get_keys() {
		return self::get_names();
	}

	public static function get_names() {
		return array_keys(self::$errors);
	}

	/**
	 * @deprecated use get_by_name instead
	 *
	 * @param $name
	 *
	 * @return bool|string
	 */
	public static function get_by_key($name) {
		return self::get_by_name($name);
	}

	public static function get_by_name($name) {
		$error = false;
		if (array_key_exists($name, self::$errors)) {
			$error = self::parse_error(self::$errors[$name]);
		}
		return $error;
	}

	public static function get_view() {
		if (!empty(self::$errors)) {
			$view = View::factory(TEMPLATE_VIEW . 'common/chunks/errors')
				->bind('errors', self::$errors);
		} else {
			$view = '';
		}
		return $view;
	}

	public static function no_errors() {
		return empty(self::$errors);
	}

	public static function has_error($name) {
		return array_key_exists($name, self::$errors);
	}

	public static function has_errors() {
		return !self::no_errors();
	}

	/*
	 * JELLY
	 */

	public static function add_from_jelly($model, $errors, $add_label = true) {
		if (is_array($errors) && !empty($errors)) {
			foreach ($errors as $field => $rule) {
				$error_value = '';
				if (is_array($rule)) {
					reset($rule);
					$error_type = current($rule);
					$value = next($rule);
					if ($value && is_array($value)) {
						reset($value);
						$error_value = next($value);
					} elseif ($value && !is_array($value)) {
						$error_value = $value;
					}
				} else {
					$error_type = $rule;
				}
				if (!empty($error_type) && is_string($error_type)) {
					if ($add_label) {
						$label = Helper_Jelly::get_field_label($model, $field);
					} else {
						$label = NULL;
					}
					$error_message = __(self::JELLY_ERROR_I18N_PREFIX . $error_type, array(
						':field' => $field,
						':value' => $error_value,
					));
					self::add($error_message, $field, $label);
				}
			}
			return true;
		}
		return false;
	}

	/*
	 * LOG
	 */

	public static function can_log_debug_messages() {
		if (is_null(self::$can_log_debug_messages)) {
			self::$can_log_debug_messages = Kohana::$config->load('environment.log.debug');
		}
		if (!is_bool(self::$can_log_debug_messages)) {
			self::$can_log_debug_messages = (Kohana::$environment == Kohana::DEVELOPMENT);
		}
		return self::$can_log_debug_messages;
	}

	public static function write() {
		$log = Log::instance();
		foreach (self::$errors as $key => $value) {
			$message = self::parse_error($value);
			if (!empty($message)) {
				$log->add(Log::ERROR, $message);
			}
		}
		$log->write();
	}

	public static function log_pair($key, $value = NULL, $level = Log::DEBUG) {
		$log = Log::instance();

		if (is_array($key)) {
			foreach ($key as $_key => $_value) {
				$log->add($level, (string)$_key . ': ' . var_export($_value, true));
			}
		} else {
			$log->add($level, (string)$key . ': ' . var_export($value, true));
		}

		$log->write();
	}

	/*
	 * REMOVE
	 */

	public static function remove($key) {
		if (is_string($key) && !empty($key) && array_key_exists($key, self::$errors)) {
			unset(self::$errors[$key]);
		}
		return true;
	}

	public static function var_dump($value, $caption = null) {
		if (is_null($caption)) {
			if (is_object($value)) {
				$caption = get_class($value);
			}
		}
		$value = var_export($value, true);
		if (!is_null($caption)) {
			$value = $caption . ' = ' . $value;
		}
		echo '<pre>' . $value .  '</pre>';
	}

} // End Helper_Error
