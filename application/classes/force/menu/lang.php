<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu_Lang
 * User: legion
 * Date: 14.09.16
 * Time: 21:33
 */
class Force_Menu_Lang extends Force_Menu {

	protected $_view = 'menu/lang';
	protected $_current_lang = 'ru';
	protected $_allowed_lang = array();

	public function __construct(array $menu = array()) {
		$this->_current_lang = i18n::get_lang();

		$menu = $this->_build_lang_menu($menu);

		parent::__construct($menu);
	}

	public static function factory(array $menu = array()) {
		return new self($menu);
	}

	public function rebuild() {
		$this->_current_lang = i18n::get_lang();

		$menu = self::_load_lang_menu();

		$menu = $this->_build_lang_menu($menu);

		$this->menu($menu);

		return $this;
	}

	/**
	 * @param $name
	 *
	 * @return Force_Menu_Lang
	 */
	public static function instance($name = 'lang', $load = true) {
		if (!array_key_exists($name, self::$_instances)) {
			if ($load) {
				$menu = self::_load_lang_menu();
			} else {
				$menu = array();
			}
			self::$_instances[$name] = new self($menu);
		}
		return self::$_instances[$name];
	}

	public function render($template = null, $view_data = null) {
		$_view_data = array(
			'current_lang' => Force_Menu_Item::update_name($this->_current_lang),
			'current_icon' => Helper_Bootstrap::get_icon($this->_current_lang),
		);
		if (is_array($view_data)) {
			$view_data = array_merge($_view_data, $view_data);
		} else {
			$view_data = $_view_data;
		}
		return parent::render($template, $view_data);
	}

	protected function _build_lang_menu(array $menu) {
		foreach ($menu as $_lang) {
			$this->_allowed_lang[$_lang] = $_lang;
		}

		$menu = $this->_allowed_lang;

		if (array_key_exists($this->_current_lang, $menu)) {
			unset($menu[$this->_current_lang]);
		}

		foreach ($menu as $_lang => $_data) {
			$menu[$_lang] = array(
				'name' => $_lang,
				'icon' => $_lang,
				'link' => self::get_link($_lang),
			);
		}

		return $menu;
	}

	/**
	 * @return array
	 * @throws Kohana_Exception
	 */
	protected static function _load_lang_menu() {
		$menu = Kohana::$config->load('common.languages');
		if (!is_array($menu)) {
			$menu = array();
		}
		return $menu;
	}

	/*
	 * GET LANG
	 */

	public function get_current_lang() {
		return $this->_current_lang;
	}

	public function get_allowed_lang() {
		return $this->_allowed_lang;
	}

	public function is_allowed($lang) {
		return array_key_exists($lang, $this->_allowed_lang);
	}

	/*
	 * HELPERS
	 */

	public static function get_link($lang) {
		return Force_URL::factory('lang')
			->route_param('id', $lang)
			->back_url()
			->get_url();
	}

} // End Force_Menu_Lang
