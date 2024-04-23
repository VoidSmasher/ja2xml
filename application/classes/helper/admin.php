<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Admin
 * User: legion
 * Date: 19.06.12
 * Time: 12:41
 */
class Helper_Admin {

	const OBJECT_NAME_DIVIDER = '::';
	const TITLE_DIVIDER = '. ';

	protected static $show_directory_title = true;

	public static function show_directory_title() {
		self::$show_directory_title = true;
	}

	public static function hide_directory_title() {
		self::$show_directory_title = false;
	}

	public static function title($object_name, $directory_title = NULL) {
		$parts = explode(self::OBJECT_NAME_DIVIDER, $object_name);
		foreach ($parts as $key => $part) {
			$parts[$key] = Force_Menu_Item::update_name($part);
		}
		$object_name = implode(self::TITLE_DIVIDER, $parts);

		if (self::$show_directory_title) {
			$directory_title = self::TITLE_DIVIDER . self::get_directory_title($directory_title);
		} else {
			$directory_title = '';
		}

		return $object_name . $directory_title;
	}

	public static function title_add($object_name, $directory_title = NULL) {
		return self::title($object_name, $directory_title) . self::TITLE_DIVIDER . __('common.add.title');
	}

	public static function title_edit($object_name, $directory_title = NULL) {
		return self::title($object_name, $directory_title) . self::TITLE_DIVIDER . __('common.edit.title');
	}

	public static function title_delete($object_name, $directory_title = NULL) {
		return self::title($object_name, $directory_title) . self::TITLE_DIVIDER . __('common.delete.title');
	}

	public static function title_show($object_name, $directory_title = NULL) {
		return self::title($object_name, $directory_title) . self::TITLE_DIVIDER . __('common.show.title');
	}

	public static function get_page_title($object_name = NULL, $directory_title = NULL) {
		$request = Request::current();
		$directory_title = self::get_directory_title($directory_title);
		$controller_title = $request->controller();

		if ($object_name) {
			$object_name .= Helper_Admin::OBJECT_NAME_DIVIDER . $controller_title;
		} else {
			$object_name = $controller_title;
		}

		switch ($request->action()) {
			case 'add':
				$page_title = self::title_add($object_name, $directory_title);
				break;
			case 'edit':
				$page_title = self::title_edit($object_name, $directory_title);
				break;
			case 'delete':
				$page_title = self::title_delete($object_name, $directory_title);
				break;
			case 'show':
				$page_title = self::title_show($object_name, $directory_title);
				break;
			default:
				$page_title = self::title($object_name, $directory_title);
		}
		return $page_title;
	}

	public static function get_directory_title($directory_title = null) {
		if (empty($directory_title)) {
			$request = Request::current();
			$directory = $request->directory();
			if (!empty($directory)) {
				$directory_title = __($directory . '.title');
			}
		}
		return $directory_title;
	}

	/**
	 * @param $a_bytes
	 *
	 * @return string
	 * @deprecated
	 */
	public static function humanize_file_size($a_bytes) {
		return Helper_String::humanize_file_size($a_bytes);
	}

	/**
	 * Подготовка запроса к поиску по БД
	 * Вычищает ненужные символы
	 * Принимает строку, переданную запросом
	 * Возвращает массив:
	 * [0] - очищенная от "мусора" поисковая строка
	 * [1] - строка для запроса LIKE (%слово1%слово2%...)
	 */
	public static function search_prepare($s) {
//		$d = array('~', '`', '"', '\'', '/', '|', '\\', '<', '>', '?', '[', ']', '{', '}', '-', '_', '+', '=', ':', ';', '%', '@', '#', '$','^', '&', '*', '(', ')');
		$d = array(
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
			'@',
			'#',
			'$',
			'^',
			'&',
			'*',
			'(',
			')',
		);
		$out = array();

		$s = str_replace($d, '', $s);
		$s = mb_strtolower($s);
		$s = trim(preg_replace("/[\s]{2,}/", " ", $s));
		$out[0] = $s;

		$s = explode(' ', $s);
		$s = '%' . implode('%', $s) . '%';
		$out[1] = $s;

		return $out;
	}

	public static function get_back_button_if_possible($back_url = null) {
		if (empty($back_url)) {
			$back_url = Force_URL::catch_back_url();
		}
		if (!empty($back_url)) {
			$button = Force_Button::factory(__('common.back_to_list'))
				->link($back_url)
				->render();
		} else {
			$button = '';
		}
		return $button;
	}

	public static function label_sortable($value = NULL) {
		return '<i class="fa fa-list sort"></i>&nbsp;<span class="number">' . $value . '</span>';
	}

} // End Helper_Admin
