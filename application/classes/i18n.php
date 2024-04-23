<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class: I18n
 * User: Legion
 * Date: 03.05.2011
 * Time: 21:11:17
 */
class I18n extends Kohana_I18n {

	protected static $_to_js_function = false;

	public static function get_default($key, $default = '') {
		$value = __($key);
		if ($value == $key) {
			$value = $default;
		}
		return $value;
	}

	/*
	 * NUMERIC
	 */

	public static function get_numeric($key, $number, $with_number = false) {
		$lang = I18n::get_lang();

		$values = array();
		if (is_string($with_number) && !empty($with_number)) {
			$values[$with_number] = $number;
		}

		switch ($lang) {
			case 'ru':
				$result = self::get_numeric_ru($key, $number, $values);
				break;
			case 'en':
			default:
				$result = self::get_numeric_en($key, $number, $values);
				break;
		}

		if ($with_number === true) {
			return $number . '&nbsp;' . $result;
		}

		return $result;
	}

	protected static function get_numeric_ru($key, $number, array $values = array()) {
		if (is_float($number) && (fmod($number, 1) != 0)) {
			$result = __($key . '.2', $values);
		} else {
			$num = (integer)$number;
			$num = abs($num);

			$last = $num % 10;
			$last_two = $num % 100;

			if (($last_two > 10) && ($last_two < 15)) {
				$result = __($key . '.5', $values);
			} else if ($last == 1) {
				$result = __($key . '.1', $values);
			} else if (($last > 1) && ($last < 5)) {
				$result = __($key . '.2', $values);
			} else {
				$result = __($key . '.5', $values);
			}
		}

		return $result;
	}

	protected static function get_numeric_en($key, $number, array $values = array()) {
		if (is_float($number) && (fmod($number, 1) != 0)) {
			$result = __($key . '.2', $values);
		} else {
			$num = (integer)$number;
			$num = abs($num);

			$result = ($num == 1)
				? __($key . '.1', $values)
				: __($key . '.2', $values);
		}

		return $result;
	}

	/*
	 * LANG
	 */

	public static function get_lang() {
		$lang = I18n::lang();
		$lang = explode('-', $lang);
		if (!empty($lang)) {
			return $lang[0];
		} else {
			return I18n::lang();
		}
	}

	public static function set_lang($lang) {
//		$lang .= '-custom';
		I18n::lang($lang);
		return I18n::$lang;
	}

	/*
	 * TO JS
	 */

	public static function to_js($key) {
		$lang = I18n::get_lang();
		$table = I18n::load($lang);

		if (!is_array($table)) {
			return false;
		}

		if (is_string($key)) {
			self::_to_js_by_key_from_table($table, $key);
		} elseif (is_array($key)) {
			foreach ($key as $_key) {
				self::_to_js_by_key_from_table($table, $_key);
			}
		}

		if (!self::$_to_js_function) {
			Helper_Assets::add_script_embedded('
				function i18n_get(key) {
					if (i18n.hasOwnProperty(key)) {
						return i18n[key];
					} else {
						return key;
					}
				}
			');
			if (i18n::get_lang() == 'ru') {
				Helper_Assets::add_script_embedded('
				function i18n_get_numeric(key, number, with_number) {
					num = Math.abs(number);

					last = num % 10;
					last_two = num % 100;

					if ((last_two > 10) && (last_two < 15)) {
						result = i18n_get(key + ".5");
					} else if (last == 1) {
						result = i18n_get(key + ".1");
					} else if ((last > 1) && (last < 5)) {
						result = i18n_get(key + ".2");
					} else {
						result = i18n_get(key + ".5");
					}

					if (with_number == true) {
						return number + " " + result;
					} else {
						return result;
					}
				}
				');
			} else {
				Helper_Assets::add_script_embedded('
				function i18n_get_numeric(key, number, with_number) {
					num = Math.abs(number);

					if (num == 1) {
						result = i18n_get(key + ".1");
					} else {
						result = i18n_get(key + ".2");
					}

					if (with_number == true) {
						return number + " " + result;
					} else {
						return result;
					}
				}
				');
			}
			self::$_to_js_function = true;
		}

		return true;
	}

	protected static function _to_js_by_key_from_table(array $table, $key) {
		self::_to_js_by_key_item($table, $key);

		// numeric
		self::_to_js_by_key_item($table, $key . '.1');
		self::_to_js_by_key_item($table, $key . '.2');
		self::_to_js_by_key_item($table, $key . '.5');
	}

	protected static function _to_js_by_key_item(array $table, $key) {
		if (array_key_exists($key, $table)) {
//			$_key = str_replace([
//				'-',
//				',',
//				'.',
//			], '_', $key);

			Helper_Assets::js_vars_push_array('i18n', $table[$key], $key);
		}
	}

} // End I18n
