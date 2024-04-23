<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Project
 * User: legion
 * Date: 16.01.14
 * Time: 11:07
 * @deprecated
 */
class Helper_Project {

	protected static $meta = null;
	protected static $copyright = null;
	protected static $uri = null;

	public static function get_name() {
		return self::get_meta_param('name');
	}

	public static function get_title() {
		return self::get_meta_param('title');
	}

	public static function get_domain() {
		return self::get_meta_param('domain');
	}

	public static function get_copyright() {
		if (is_null(self::$copyright)) {
			$year = self::get_meta_param('start_year');
			$current_year = date('Y');
			if ($year > $current_year) {
				$year = $current_year;
			}
			if ($year < $current_year) {
				$year = $year . ' - ' . $current_year;
			}
			$owner = self::get_meta_param('company');
			if (empty($owner)) {
				$owner = self::get_meta_param('title');
			}
			if (empty($owner)) {
				$owner = self::get_meta_param('name');
			}
			self::$copyright = '&copy; ' . $owner . ' ' . $year;
		}

		return self::$copyright;
	}

	public static function get_developed_by() {
		$developed_by = __('project.developed_by', array(':developer' => self::get_meta_param('developed_by')));
		return html::anchor(self::get_meta_param('developed_by_link'), $developed_by, array(
			'rel' => 'nofollow',
			'target' => '_blank',
			'title' => $developed_by,
		));
	}

	public static function get_uri() {
		if (is_null(self::$uri)) {
			self::$uri = Helper_Uri::get_host(self::get_domain());
		}

		return self::$uri;
	}

	/*
	 * COMMON CONFIG PARAMS
	 */

	protected static function get_meta_param($param) {
		if (is_null(self::$meta)) {
			self::$meta = Kohana::$config->load('project');
		}
		if (is_array(self::$meta) && array_key_exists($param, self::$meta)) {
			return self::$meta[$param];
		}
		if (is_object(self::$meta) && (self::$meta instanceof Config_Group)) {
			return self::$meta->get($param);
		}

		return null;
	}

} // End Helper_Project
 