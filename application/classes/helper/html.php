<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Html
 * User: Andrey Verstov
 * Date: 01.08.12
 * Time: 19:04
 */
class Helper_Html {

	public static function update_attributes($old_attributes, $new_attributes, $append_class = false) {
		if (!empty($old_attributes) && is_array($old_attributes) && is_array($new_attributes)) {
			if ($append_class && array_key_exists('class', $old_attributes) && array_key_exists('class', $new_attributes)
			) {
				if (!empty($old_attributes['class']) && !empty($new_attributes['class'])) {
					$new_attributes['class'] = $old_attributes['class'] . ' ' . $new_attributes['class'];
				} elseif (!empty($old_attributes['class']) && empty($new_attributes['class'])) {
					$new_attributes['class'] = $old_attributes['class'];
				}
			}
			$new_attributes = $old_attributes + $new_attributes;
		}
		return $new_attributes;
	}

	public static function add_rel_nofollow($html) {
		$current_pos = 0;
		$a_tags = array();
		while (($start = stripos($html, '<a ', $current_pos)) !== false) {
			$end = stripos($html, '>', ($start + 3));
			if ($end === false) {
				$html = substr($html, 0, $start);
				break;
			}
			$a_tags[] = substr($html, ($start + 3), ($end - $start - 3));
			$current_pos = $end;
		}

		foreach ($a_tags as $tag) {
			// Если есть атрибут "rel"
			if (($pos = mb_strpos($tag, 'rel=')) != false) {
				$sub = substr($tag, $pos + 4);
				// если значение в кавычках
				if (substr($sub, 0, 1) == '"') {
					$sub = ltrim($sub, '"');
					$right_quote_pos = strpos($sub, '"');
					$sub = substr($sub, 0, $right_quote_pos);
					$html = str_replace('rel="' . $sub . '"', 'rel="nofollow"', $html);
					// если без кавычек
				} else {
					$end_pos = strpos($sub . ' ', ' ');
					$sub = substr($sub, 0, $end_pos);
					$html = str_replace('rel=' . $sub, 'rel="nofollow"', $html);
				}
				// Если нет атрибута "rel"
			} else {
				$new_tag = $tag . ' rel="nofollow"';
				$html = str_replace($tag, $new_tag, $html);
			}
		}

		return $html;
	}

	/*
	 * HTML FORM VALUE PREPARATIONS
	 */

	public static function prepare_value_for_form($value) {
		$forbidden_chars = array(
			'~',
			'`',
			'"',
			'\'',
			'/',
			'|',
			'\\',
			'<',
			'>',
			'?',
			'[',
			']',
			'{',
			'}',
			'+',
			'=',
			':',
			';',
			'%',
//			'@',
			'#',
			'$',
			'^',
			'&',
			'*',
			'(',
			')',
		);
		$value = str_replace($forbidden_chars, '', $value);
		$value = mb_strtolower($value);
		$value = trim(preg_replace("/[\s]{2,}/", " ", $value));
		return $value;
	}

	public static function prepare_value_for_sql($value) {
		if (empty($value)) {
			return '';
		}
		$value = self::prepare_value_for_form($value);
		$value = explode(' ', $value);
		$value = '%' . implode('%', $value) . '%';
		return $value;
	}

} // End Helper_Html
