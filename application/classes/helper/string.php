<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_String
 * User: legion
 * Date: 27.04.12
 * Time: 14:16
 */
class Helper_String {

	public static function first_sentence($text) {
		$str_end_symbols = array(
			'.',
			'!',
			'?',
		);
		$pos = 1000000000;

		foreach ($str_end_symbols as $symbol) {
			$temp_pos = strpos($text, $symbol);
			$pos = ($temp_pos !== false && $temp_pos < $pos) ? $temp_pos : $pos;
		}

		return ($pos > 0 && $pos <= strlen($text)) ? substr($text, 0, $pos + 1) : $text;
	}

	public static function to_string($text, $delimiter = PHP_EOL, $log_if_non_convertible_value_given = true) {
		if (is_string($text)) {
			return $text;
		} elseif (is_numeric($text)) {
			return (string)$text;
		} elseif (is_array($text)) {
			return implode($delimiter, $text);
		} elseif (is_object($text) && method_exists($text, '__toString')) {
			return (string)$text;
		} elseif (empty($text)) {
			return '';
		} elseif ($log_if_non_convertible_value_given) {
			Log::warning('Helper_String::to_string() non-convertible value given as $text: ' . var_export($text, true));
		}
		return '';
	}

	public static function valid_alias($alias, $max_length = 0) {
		$alias = utf8_decode($alias);
		$alias = str_replace(' ', '-', $alias);
		$alias = preg_replace(array(
			'/[^a-z0-9\-\_\s]*/i',
			'/ */',
		), '$1', $alias);
		$alias = preg_replace('/\s\s+/', ' ', $alias);
		$alias = trim($alias);
		$alias = strtolower($alias);
		$alias = utf8_encode($alias);
		if ($max_length > 0) {
			$alias = substr($alias, 0, $max_length);
		}
		return $alias;
	}

	public static function translate_to_alias($string) {
		if (empty($string)) {
			return '';
		}
		$alias = self::transliteration($string);
//		$alias = self::clean_alias($alias);
		return URL::title($alias);
	}

	public static function translate_to_role($string) {
		if (empty($string)) {
			return '';
		}
		$string = mb_strtolower($string);
		$role = self::transliteration($string);
		return self::clean_role($role);
	}

	public static function clean_alias($alias) {
		$alias = strtolower($alias);
		$alias = preg_replace('/&.+?;/', '', $alias); // kill entities
//		$alias = str_replace('-', '_', $alias);
		$alias = preg_replace('/[^a-z0-9\-\s-.]/', '', $alias);
		$alias = preg_replace('/\s+/', '-', $alias);
		$alias = preg_replace('|-+|', '-', $alias);
		$alias = trim($alias, '-');
		return $alias;
	}

	public static function clean_role($role) {
		$role = preg_replace('/&.+?;/', '', $role); // kill entities
		$role = preg_replace('/[^a-z0-9\-\_\s-.]/', '', $role);
		$role = preg_replace('/\s+/', '-', $role);
		$role = preg_replace('|-+|', '-', $role);
		$role = preg_replace('|_+|', '_', $role);
		return $role;
	}

	public static function clean_keywords($keywords) {
		$keywords = mb_strtolower($keywords);
		$keywords = str_replace('/', ', ', $keywords);
		$keywords = str_replace('\\', ', ', $keywords);
		$keywords = preg_replace('/\s+/', ' ', $keywords);
		$keywords = preg_replace('|-+|', '-', $keywords);
		return $keywords;
	}

	public static function replace_url_with_link_tag($string) {
		$string = htmlspecialchars_decode($string);
		$string = preg_replace('/(<a|<\/a)(([^>]|\n)*)>/', '', $string);
		return preg_replace('/(https?:\/\/)?(www\.)?([-а-яa-z0-9_\.]{2,}\.)(рф|[a-z]{2,6})((\/[-а-яa-z0-9_.]{1,})?\/?([a-z0-9_-]{2,}\.[a-z]{2,6})?(\?[a-z0-9_]{2,}=[-0-9]{1,})?((\&[a-z0-9_]{2,}=[-0-9]{1,}){1,})?)/i ', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $string);
	}

	public static function prepare_name_for_xml($name) {
		return str_replace(array(
			'"',
			'&',
			'>',
			'<',
			"'",
		), array(
			'&quot;',
			'&amp;',
			'&gt;',
			'&lt;',
			'&apos;',
		), $name);
	}

	/*
	 * AMOUNT AND CURRENCY
	 */

	public static function get_amount($amount, $currency_name = null, $digits_after_comma = 2) {
		$digits_after_comma = (int)$digits_after_comma;
		if ($digits_after_comma < 0) {
			$digits_after_comma = 0;
		}
		$amount = number_format(round($amount, $digits_after_comma), $digits_after_comma, ',', ' ');
		if (is_string($currency_name) && !empty($currency_name)) {
			$amount .= ' ' . self::get_currency_icon($currency_name);
		}
		return $amount;
	}

	public static function get_currency_icon($currency_name) {
		$icon = 'fa-' . strtolower($currency_name);
		$icons = array(
			'fa-bitcoin' => 'fa-bitcoin',
			'fa-btc' => 'fa-btc',
			'fa-cny' => 'fa-cny',
			'fa-dollar' => 'fa-dollar',
			'fa-eur' => 'fa-eur',
			'fa-euro' => 'fa-euro',
			'fa-gbp' => 'fa-gbp',
			'fa-ils' => 'fa-ils',
			'fa-inr' => 'fa-inr',
			'fa-jpy' => 'fa-jpy',
			'fa-krw' => 'fa-krw',
			'fa-money' => 'fa-money',
			'fa-rmb' => 'fa-rmb',
			'fa-rouble' => 'fa-rouble',
			'fa-rub' => 'fa-rub',
			'fa-ruble' => 'fa-ruble',
			'fa-rupee' => 'fa-rupee',
			'fa-shekel' => 'fa-shekel',
			'fa-sheqel' => 'fa-sheqel',
			'fa-try' => 'fa-try',
			'fa-turkish-lira' => 'fa-turkish-lira',
			'fa-usd' => 'fa-usd',
			'fa-won' => 'fa-won',
			'fa-yen' => 'fa-yen',
		);
		if (array_key_exists($icon, $icons)) {
			return Helper_Bootstrap::get_icon(strtolower($icon), false);
		} else {
			return ucfirst($currency_name);
		}
	}

	/*
	 * TRANSLITERATION
	 */

	public static function transliteration($string) {
		if (empty($string)) {
			return '';
		}
		$changes = array(
			"Є" => "EH",
			"І" => "I",
			"і" => "i",
			"№" => "#",
			"є" => "eh",
			"А" => "A",
			"Б" => "B",
			"В" => "V",
			"Г" => "G",
			"Д" => "D",
			"Е" => "E",
			"Ё" => "E",
			"Ж" => "ZH",
			"З" => "Z",
			"И" => "I",
			"Й" => "J",
			"К" => "K",
			"Л" => "L",
			"М" => "M",
			"Н" => "N",
			"О" => "O",
			"П" => "P",
			"Р" => "R",
			"С" => "S",
			"Т" => "T",
			"У" => "U",
			"Ф" => "F",
			"Х" => "H",
			"Ц" => "C",
			"Ч" => "CH",
			"Ш" => "SH",
			"Щ" => "SCH",
			"Ъ" => "",
			"Ы" => "Y",
			"Ь" => "",
			"Э" => "E",
			"Ю" => "YU",
			"Я" => "YA",
			"Ē" => "E",
			"Ū" => "U",
			"Ī" => "I",
			"Ā" => "A",
			"Š" => "S",
			"Ģ" => "G",
			"Ķ" => "K",
			"Ļ" => "L",
			"Ž" => "Z",
			"Č" => "C",
			"Ņ" => "N",
			"ē" => "e",
			"ū" => "u",
			"ī" => "i",
			"ā" => "a",
			"š" => "s",
			"ģ" => "g",
			"ķ" => "k",
			"ļ" => "l",
			"ž" => "z",
			"č" => "c",
			"ņ" => "n",
			"а" => "a",
			"б" => "b",
			"в" => "v",
			"г" => "g",
			"д" => "d",
			"е" => "e",
			"ё" => "e",
			"ж" => "zh",
			"з" => "z",
			"и" => "i",
			"й" => "j",
			"к" => "k",
			"л" => "l",
			"м" => "m",
			"н" => "n",
			"о" => "o",
			"п" => "p",
			"р" => "r",
			"с" => "s",
			"т" => "t",
			"у" => "u",
			"ф" => "f",
			"х" => "h",
			"ц" => "c",
			"ч" => "ch",
			"ш" => "sh",
			"щ" => "sch",
			"ъ" => "",
			"ы" => "y",
			"ь" => "",
			"э" => "e",
			"ю" => "yu",
			"я" => "ya",
			"Ą" => "A",
			"Ę" => "E",
			"Ė" => "E",
			"Į" => "I",
			"Ų" => "U",
			"ą" => "a",
			"ę" => "e",
			"ė" => "e",
			"į" => "i",
			"ų" => "u",
			"ö" => "o",
			"Ö" => "O",
			"ü" => "u",
			"Ü" => "U",
			"ä" => "a",
			"Ä" => "A",
			"õ" => "o",
			"Õ" => "O",
		);
		return strtr($string, $changes);
	}

	public static function limit($s, $l = 80) {
		if (mb_strlen($s, 'UTF-8') > $l) {
			return mb_substr($s, 0, $l, 'UTF-8') . '...';
		}
		return $s;
	}

	public static function humanize_file_size($a_bytes, $precision = 2) {
		if ($a_bytes < 1024) {
			return $a_bytes . ' ' . i18n::get_default('file.size.B', 'B');
		} elseif ($a_bytes < 1048576) {
			return round($a_bytes / 1024, $precision) . ' ' . i18n::get_default('file.size.KiB', 'KiB');
		} elseif ($a_bytes < 1073741824) {
			return round($a_bytes / 1048576, $precision) . ' ' . i18n::get_default('file.size.MiB', 'MiB');
		} elseif ($a_bytes < 1099511627776) {
			return round($a_bytes / 1073741824, $precision) . ' ' . i18n::get_default('file.size.GiB', 'GiB');
		} elseif ($a_bytes < 1125899906842624) {
			return round($a_bytes / 1099511627776, $precision) . ' ' . i18n::get_default('file.size.TiB', 'TiB');
		} elseif ($a_bytes < 1152921504606846976) {
			return round($a_bytes / 1125899906842624, $precision) . ' ' . i18n::get_default('file.size.PiB', 'PiB');
		} elseif ($a_bytes < 1180591620717411303424) {
			return round($a_bytes / 1152921504606846976, $precision) . ' ' . i18n::get_default('file.size.EiB', 'EiB');
		} elseif ($a_bytes < 1208925819614629174706176) {
			return round($a_bytes / 1180591620717411303424, $precision) . ' ' . i18n::get_default('file.size.ZiB', 'ZiB');
		} else {
			return round($a_bytes / 1208925819614629174706176, $precision) . ' ' . i18n::get_default('file.size.YiB', 'YiB');
		}
	}

	public static function invert_layout($string) {
		return strtr($string, array(
			'й' => 'q',
			'ц' => 'w',
			'у' => 'e',
			'к' => 'r',
			'е' => 't',
			'н' => 'y',
			'г' => 'u',
			'ш' => 'i',
			'щ' => 'o',
			'з' => 'p',
			'х' => '[',
			'ъ' => ']',
			'ф' => 'a',
			'ы' => 's',
			'в' => 'd',
			'а' => 'f',
			'п' => 'g',
			'р' => 'h',
			'о' => 'j',
			'л' => 'k',
			'д' => 'l',
			'ж' => ';',
			'э' => '\'',
			'я' => 'z',
			'ч' => 'x',
			'с' => 'c',
			'м' => 'v',
			'и' => 'b',
			'т' => 'n',
			'ь' => 'm',
			'б' => ',',
			'ю' => '.',

			'Й' => 'Q',
			'Ц' => 'W',
			'У' => 'E',
			'К' => 'R',
			'Е' => 'T',
			'Н' => 'Y',
			'Г' => 'U',
			'Ш' => 'I',
			'Щ' => 'O',
			'З' => 'P',
			'Х' => '[',
			'Ъ' => ']',
			'Ф' => 'A',
			'Ы' => 'S',
			'В' => 'D',
			'А' => 'F',
			'П' => 'G',
			'Р' => 'H',
			'О' => 'J',
			'Л' => 'K',
			'Д' => 'L',
			'Ж' => ':',
			'Э' => '\'',
			'?' => ',',
			'Ч' => 'X',
			'С' => 'C',
			'М' => 'V',
			'И' => 'B',
			'Т' => 'N',
			'Ь' => 'M',
			'Б' => ',',
			'Ю' => '.',

			'q' => 'й',
			'w' => 'ц',
			'e' => 'у',
			'r' => 'к',
			't' => 'е',
			'y' => 'н',
			'u' => 'г',
			'i' => 'ш',
			'o' => 'щ',
			'p' => 'з',
			'[' => 'х',
			']' => 'ъ',
			'a' => 'ф',
			's' => 'ы',
			'd' => 'в',
			'f' => 'а',
			'g' => 'п',
			'h' => 'р',
			'j' => 'о',
			'k' => 'л',
			'l' => 'д',
			';' => 'ж',
			'\'' => 'э',
			'z' => 'я',
			'x' => 'ч',
			'c' => 'с',
			'v' => 'м',
			'b' => 'и',
			'n' => 'т',
			'm' => 'ь',
			',' => 'б',
			'.' => 'ю',

			'Q' => 'Й',
			'W' => 'Ц',
			'E' => 'У',
			'R' => 'К',
			'T' => 'Е',
			'Y' => 'Н',
			'U' => 'Г',
			'I' => 'Ш',
			'O' => 'Щ',
			'P' => 'З',
			'{' => 'Х',
			'}' => 'Ъ',
			'A' => 'Ф',
			'S' => 'Ы',
			'D' => 'В',
			'F' => 'А',
			'G' => 'П',
			'H' => 'Р',
			'J' => 'О',
			'K' => 'Л',
			'L' => 'Д',
			':' => 'Ж',
			'\"' => 'Э',
			'X' => 'Ч',
			'C' => 'С',
			'V' => 'М',
			'B' => 'И',
			'N' => 'Т',
			'M' => 'Ь',
			'<' => 'Б',
			'>' => 'Ю',
		));
	}

	public static function generate_password($length = 8, $max_length = false) {
		$pwd = '';
		if ($max_length) {
			$length = rand($length, $max_length);
		}
		$digit = '0123456789';
		$char1 = 'bcdfghjklmnpqrstvwxz';
		$char2 = 'aeiouy';
		$d = rand(floor($length / 2), $length - 1);
		$chars = 0;
		for ($i = 0; $i < $length; $i++) {
			if ($i == $d) {
				$chars =& $digit;
			} elseif ($i % 2) {
				$chars =& $char1;
			} else {
				$chars =& $char2;
			}
			$pwd .= substr($chars, rand(0, strlen($chars) - 1), 1);
		}
		return $pwd;
	}

} // End Helper_String
