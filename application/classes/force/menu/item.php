<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu_Item
 * User: legion
 * Date: 19.08.14
 * Time: 19:59
 */
class Force_Menu_Item extends Force_Menu_Common {

	use Force_Attributes_Menu;
	use Force_Control_Name;
	use Force_Control_link;

	protected $_key = '';
	protected $_i18n = '';
	protected $_icon = '';
	protected $_active = false;
	protected $_view_dropdown = 'navigation/dropdown';
	protected $_view_dropdown_use_template_path = true;
	protected $_view_item = 'navigation/item';
	protected $_view_item_use_template_path = true;

	public function __construct($name, $link = '', $icon_class = '', array $menu = array()) {
		$this->_key = strval($name);
		$this->name($name);
		$this->link($link);
		$this->icon($icon_class);
		$this->menu($menu);
	}

	public static function factory($name, $link = '', $icon_class = '', array $menu = array()) {
		return new self($name, $link, $icon_class, $menu);
	}

	public function render($template = null, $number = 1) {
		if (!$this->_template_customized) {
			if (!empty($template)) {
				$template = strval($template);
				$template = trim($template, ' /');
			}
			if (empty($template)) {
				$template = $this->_template;
			}
		}

		if ($this->_use_link) {
			$link = !empty($this->_link) ? $this->_link : ($this->is_dropdown() ? '#' : '/');
			$this->attribute('href', $link);
		}

		$name = $this->get_name();
		$i18n = $this->get_i18n();

		$icon = Helper_Bootstrap::get_icon($this->_icon);
		$label = (!empty($i18n)) ? $i18n : self::update_name($name, $this->_i18n_item_prefix);

		$is_active = $this->is_active();

		if ($is_active) {
			$this->group_attribute('class', 'active');
		}

		if ($this->is_dropdown()) {
			if ($this->_view_dropdown_use_template_path) {
				$view = 'template' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $this->_view_dropdown;
			} else {
				$view = $this->_view_dropdown;
			}

			$menu_body = $this->_render_menu_body($template);

			$this->attribute('class', 'dropdown-toggle');
			$this->attribute('data-toggle', 'dropdown');

			$this->menu_attribute('class', 'dropdown-menu');

			$item_body = View::factory($view)
				->set('attributes', $this->render_attributes())
				->set('group_attributes', $this->render_group_attributes())
				->set('menu_attributes', $this->render_menu_attributes())
				->bind('is_active', $is_active)
				->bind('icon', $icon)
				->bind('name', $name)
				->bind('label', $label)
				->bind('number', $number)
				->bind('menu_body', $menu_body)
				->render();
		} else {
			if ($this->_view_item_use_template_path) {
				$view = 'template' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $this->_view_item;
			} else {
				$view = $this->_view_item;
			}

			$item_body = View::factory($view)
				->set('attributes', $this->render_attributes())
				->set('group_attributes', $this->render_group_attributes())
				->bind('is_active', $is_active)
				->bind('icon', $icon)
				->bind('name', $name)
				->bind('label', $label)
				->bind('number', $number)
				->render();
		}

		return $item_body;
	}

	/*
	 * SET
	 */

	public function i18n($string, array $values = NULL, $lang = 'en-us') {
		if (!empty($string)) {
			$this->_i18n = __($string, $values, $lang);
		}
		return $this;
	}

	public function icon($icon_class) {
		$this->_icon = strval($icon_class);
		return $this;
	}

	public function view_item($view, $use_template_path = true) {
		$view = strval($view);
		$this->_view_item = trim($view, ' /');
		$this->_view_item_use_template_path = $use_template_path;
		return $this;
	}

	public function view_dropdown($view, $use_template_path = true) {
		$view = strval($view);
		$this->_view_dropdown = trim($view, ' /');
		$this->_view_dropdown_use_template_path = $use_template_path;
		return $this;
	}

	/*
	 * GET
	 */

	public function get_i18n() {
		return $this->_i18n;
	}

	public function get_key() {
		return $this->_key;
	}

	public function as_array() {
		return array(
			'name' => $this->_name,
			'i18n' => $this->_i18n,
			'link' => $this->_link,
			'icon' => $this->_icon,
			'menu' => $this->get_menu_as_array(),
		);
	}

	public function get_menu_as_array() {
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

	public function is_dropdown() {
		return !empty($this->_menu);
	}

	/*
	 * ACTIVE
	 */

	public function active() {
		$this->_active = TRUE;
	}

	public function is_active() {
		$current_uri = '/' . Request::current()->uri();
		$is_active = $this->_active;
		$link = !empty($this->_link) ? $this->_link : ($this->is_dropdown() ? '' : '/');
		if ($this->is_dropdown()) {
			if (!$is_active) {
				$is_active = (!empty($link) && strpos('/' . $current_uri, $link) !== false);
			}
		} else {
			if (!$is_active) {
				$is_active = (!empty($link) && $link == $current_uri);
			}
		}
		return $is_active;
	}

	/*
	 * STATIC
	 */

	public static function update_name($name, $i18n_prefix = 'menu.') {
		$_name = __($i18n_prefix . $name);
		if ($_name == $i18n_prefix . $name) {
			$_name = UTF8::ucfirst($name);
		}
		return $_name;
	}

} // End Force_Menu_Item