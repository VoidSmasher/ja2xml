<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: HTML
 * User: legion
 * Date: 07.04.15
 * Time: 21:41
 */
class HTML extends Kohana_HTML {

	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE) {
			// Add the base URL
			$file = URL::site($file, $protocol, $index);
		}

		// Set the script link
		$attributes['src'] = $file;

		// Set the script type
		if (!array_key_exists('type', $attributes)) {
			$attributes['type'] = 'text/javascript';
		}

		return '<script' . HTML::attributes($attributes) . '></script>';
	}

} // End HTML
