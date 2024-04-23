<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu
 * User: legion
 * Date: 19.08.14
 * Time: 19:56
 */
class Force_Menu extends Force_Menu_Common {

	protected $_view = 'menu/main';
	protected $_view_use_template_path = true;
	protected $_view_dropdown = '';
	protected $_view_dropdown_use_template_path = true;
	protected $_view_item = '';
	protected $_view_item_use_template_path = true;

	protected static $_instances = array();

	public function __construct(array $menu = array()) {
		$this->menu($menu);
	}

	public static function factory(array $menu = array()) {
		return new self($menu);
	}

	public function __toString() {
		return $this->render();
	}

	/**
	 * @param string $name
	 *
	 * @return Force_Menu
	 */
	public static function instance($name = 'public', $load = true) {
		if (!array_key_exists($name, self::$_instances)) {
			if ($load) {
				$menu = self::load_menu($name);
			} else {
				$menu = array();
			}
			self::$_instances[$name] = new self($menu);
		}
		return self::$_instances[$name];
	}

	public static function load_menu($name) {
		$menu = Kohana::$config->load('menu.' . $name);
		if (!is_array($menu)) {
			$menu = array();
		}
		return $menu;
	}

	public function render($template = null, $view_data = null) {
		if (!empty($template)) {
			$template = (string)$template;
			$template = trim($template, ' ' . DIRECTORY_SEPARATOR);
		}
		if (empty($template)) {
			$template = $this->_template;
		}

		$menu_body = $this->_render_menu_body($template, $this->_view_item, $this->_view_dropdown, $this->_view_item_use_template_path, $this->_view_dropdown_use_template_path);

		if (!empty($menu_body)) {
			if ($this->_view_use_template_path) {
				$view = 'template' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $this->_view;
			} else {
				$view = $this->_view;
			}

			return View::factory($view, $view_data)
				->bind('menu_body', $menu_body)
				->render();
		}

		return '';
	}

	/*
	 * SET
	 */

	public function view($view, $use_template_path = true) {
		$view = (string)$view;
		$this->_view = trim($view, ' /');
		$this->_view_use_template_path = $use_template_path;
		return $this;
	}

	public function view_item($view, $use_template_path = true) {
		$view = (string)$view;
		$this->_view_item = trim($view, ' /');
		$this->_view_item_use_template_path = $use_template_path;
		return $this;
	}

	public function view_dropdown($view, $use_template_path = true) {
		$view = (string)$view;
		$this->_view_dropdown = trim($view, ' /');
		$this->_view_dropdown_use_template_path = $use_template_path;
		return $this;
	}

	/*
	 * GET
	 */

	public function as_array() {
		$_array = array();
		foreach ($this->_menu as $key => $item) {
			if ($item instanceof Force_Menu_Divider) {
				$_array[$key] = $item->as_array();
			}

			if ($item instanceof Force_Menu_Item) {
				$_array[$key] = $item->as_array();
			}
		}
		return $_array;
	}

} // End Force_Menu
