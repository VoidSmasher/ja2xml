<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Bootstrap
 * User: legion
 * Date: 01.11.12
 * Time: 13:06
 * Помощник в работе с Twitter Bootstrap 3
 */
class Helper_Bootstrap {

	const LABEL_DEFAULT = 'label-default';
	const LABEL_BLUE = 'label-primary';
	const LABEL_GREEN = 'label-success';
	const LABEL_CYAN = 'label-info';
	const LABEL_YELLOW = 'label-warning';
	const LABEL_RED = 'label-danger';

	const TABLE_ROW_DEFAULT = 'default';
	const TABLE_ROW_BLUE = 'primary';
	const TABLE_ROW_GREEN = 'success';
	const TABLE_ROW_CYAN = 'info';
	const TABLE_ROW_YELLOW = 'warning';
	const TABLE_ROW_RED = 'danger';

	public static function get_brand_links(array $brand_links = array(), $add_default = true) {
		if ($add_default) {
			$project_name = Helper_Bootstrap::get_icon('fa-desktop');
			$brand_links_default = array(
				$project_name => '/',
			);
			$brand_links = array_merge($brand_links_default, $brand_links);
		}
		return $brand_links;
	}

	public static function get_warning_message($message) {
		return Force_Label::factory(__('alert.warning') . ' ' . $message)
			->color_red()
			->render();
	}

	/*
	 * ICONS
	 */

	public static function get_icon_from_menu_params($menu_params, $fw = true) {
		$icon = '';
		if (array_key_exists('icon', $menu_params)) {
			$icon = self::get_icon($menu_params['icon'], $fw);
		}
		return $icon;
	}

	public static function get_icon_class($icon_class, $fw = true) {
		$icon_class = trim($icon_class);
		if (substr($icon_class, 0, 3) == 'fa-') {
			$icon_class = 'fa ' . $icon_class;
			if ($fw) {
				$icon_class .= ' fa-fw';
			}
		} elseif (substr($icon_class, 0, 10) == 'glyphicon-') {
			$icon_class = 'glyphicon ' . $icon_class;
		}
		return $icon_class;
	}

	public static function get_icon($icon_class, $fw = true) {
		if (empty($icon_class)) {
			return '';
		}
		$icon_class = self::get_icon_class($icon_class, $fw);
		return '<i class="' . $icon_class . '"></i> ';
	}

} // End Helper_Bootstrap
