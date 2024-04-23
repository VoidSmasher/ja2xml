<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Menu
 * User: legion
 * Date: 09.12.15
 * Time: 1:41
 */
class Helper_Menu {

	protected static $_item_name_for_files_in_folder = '[ DIR ]';

	protected static function _create_menu_from_dir(&$menu, $directory, $cut_len, $uri_prefix, $exclude = '') {
		$skipByExclude = false;
		$handle = opendir($directory);
		if ($handle) {
			while (false !== ($file = readdir($handle))) {
				preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
				if ($exclude) {
					preg_match($exclude, $file, $skipByExclude);
				}
				if (!$skip && !$skipByExclude) {
					if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
						if ($menu instanceof Force_Menu || $menu instanceof Force_Menu_Item) {
							$item = $menu->item($file);
						}
						self::_create_menu_from_dir($item, $directory . DIRECTORY_SEPARATOR . $file, $cut_len, $uri_prefix, $exclude);
					} else {
						$name = basename($file, '.php');
						$uri = substr($directory, $cut_len);
						$uri = explode(DIRECTORY_SEPARATOR, $uri);
						$uri = implode('_', $uri);
						$uri .= (!empty($uri) ? '_' : '') . $name;
						$uri = URL::site($uri_prefix . '/' . $uri);
						if ($menu instanceof Force_Menu) {
							$menu->item(self::$_item_name_for_files_in_folder)->add_item($name, $uri);
						} elseif ($menu instanceof Force_Menu_Item) {
							$menu->add_item($name, $uri);
						}
					}
				}
			}
			closedir($handle);
		}
	}

	public static function create_menu_from_dir($dir, $exclude = '') {
		$menu = Force_Menu::factory();
		$menu->add_item(self::$_item_name_for_files_in_folder);

		$uri_prefix = explode(DIRECTORY_SEPARATOR, $dir);
		$uri_prefix = $uri_prefix[count($uri_prefix)-1];

		self::_create_menu_from_dir($menu, $dir, strlen($dir)+1, $uri_prefix, $exclude);

		return $menu;
	}

	public static function item_name_for_files_in_folder($name) {
		self::$_item_name_for_files_in_folder = $name;
	}

} // End Helper_Menu
