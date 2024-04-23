<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu_Common
 * User: legion
 * Date: 20.08.14
 * Time: 3:08
 */
abstract class Force_Menu_Common extends Force_Attributes {

	use Force_Attributes_Group;

	protected $_menu = array();
	protected $_template = 'bootstrap';
	protected $_template_customized = false;
	protected $_use_link_inherited = true;
	protected $_use_link = true;

	protected $_mark_as_deleted = false;

	protected $_i18n_item_prefix = 'menu.';

	abstract public function render();

	public function __toString() {
		return $this->render();
	}

	protected function _render_menu_body($template = null, $view_item = null, $view_dropdown = null, $view_item_use_template_path = true, $view_dropdown_use_template_path = true) {
		$menu_body = array();

		$item_number = 1;

		$last_key = 0;
		$last_menu_item_is_divider = true;
		foreach ($this->_menu as $index => $item) {
			if (($item instanceof Force_Menu_Divider) && !$last_menu_item_is_divider) {
				$last_key++;
				$menu_body[$last_key] = $item->render();
				$last_menu_item_is_divider = true;
				continue;
			}

			if ($item instanceof Force_Menu_Item) {
				// первая проверка на удаление
				if ($item->is_deleted()) {
					unset($this->_menu[$index]);
					continue;
				}
				if (!$this->_use_link_inherited) {
					$item->no_link(true);
				}
				if (!empty($view_item)) {
					$item->view_item($view_item, $view_item_use_template_path);
				}
				if (!empty($view_dropdown)) {
					$item->view_dropdown($view_dropdown, $view_dropdown_use_template_path);
				}
				// вторая проверка на удаление - если было вызвано в each()
				if ($item->is_deleted()) {
					unset($this->_menu[$index]);
					continue;
				}
				$last_key++;
				$menu_body[$last_key] = $item->render($template, $item_number);
				$item_number++;
				$last_menu_item_is_divider = false;
			}
		}

		// @TODO Оптимизировать проверку
		if (array_key_exists($last_key, $menu_body) && ($menu_body[$last_key] == Force_Menu_Divider::factory()
					->render())
		) {
			unset($menu_body[$last_key]);
		}

		return implode(PHP_EOL, $menu_body);
	}

	protected static function parse_menu($menu, $i18n_prefix = 'menu.') {
		if (!is_array($menu)) {
			return array();
		}

		$_menu = array();
		$_last_key = 0;

		$last_menu_item_is_divider = true;
		foreach ($menu as $key => $params) {
			if (Force_Menu_Divider::is_divider($params)) {
				if (!$last_menu_item_is_divider) {
					$_menu[$key] = new Force_Menu_Divider;
					$_last_key = $key;
				}
				$last_menu_item_is_divider = true;
				continue;
			}
			$last_menu_item_is_divider = false;
			if ($params instanceof Force_Menu_Item) {
				$name = $params->get_name();
				$_menu[$name] = $params;
			} else {
				$name = self::parse_params_name($params, $key);
				$i18n = self::parse_params_i18n($params, $name, $i18n_prefix);
				$link = self::parse_params_link($params);
				$icon = self::parse_params_icon($params);
				$menu = self::parse_params_menu($params);
				$_menu[$name] = Force_Menu_Item::factory($key, $link, $icon)->i18n($i18n)->menu($menu);
			}
			$_last_key = $name;
		}

		if (array_key_exists($_last_key, $_menu) && ($_menu[$_last_key] instanceof Force_Menu_Divider)) {
			unset($_menu[$_last_key]);
		}

		return $_menu;
	}

	public static function parse_params_name(array $params, $key) {
		if (is_numeric($key)) {
			$name = (array_key_exists('name', $params) && is_string($params['name'])) ? $params['name'] : $key;
		} else {
			$name = $key;
		}
		return $name;
	}

	public static function parse_params_i18n(array $params, $name, $i18n_prefix = 'menu.') {
		return (array_key_exists('i18n', $params) && is_string($params['i18n'])) ? $params['i18n'] : Force_Menu_Item::update_name($name, $i18n_prefix);
	}

	public static function parse_params_link(array $params) {
		return (array_key_exists('link', $params) && is_string($params['link'])) ? $params['link'] : '';
	}

	public static function parse_params_icon(array $params) {
		return (array_key_exists('icon', $params) && is_string($params['icon'])) ? $params['icon'] : '';
	}

	public static function parse_params_menu(array $params) {
		return (array_key_exists('menu', $params) && is_array($params['menu'])) ? $params['menu'] : array();
	}

	/*
	 * GET/SET
	 */

	/**
	 * @param $name
	 *
	 * @return Force_Menu_Item
	 */
	public function item($name) {
		$name = (string)$name;

		if (!array_key_exists($name, $this->_menu) || !($this->_menu[$name] instanceof Force_Menu_Item)) {
			$item = Force_Menu_Item::factory($name);
			$this->add_item($item);
		}

		return $this->_menu[$name];
	}

	/*
	 * GET
	 */

	public function has_item($name) {
		return array_key_exists($name, $this->_menu);
	}

	public function get_menu() {
		return $this->_menu;
	}

	abstract public function as_array();

	/*
	 * SET
	 */

	public function i18n_item_prefix($prefix = 'menu.') {
		$this->_i18n_item_prefix = strval($prefix);
		return $this;
	}

	public function each($function_with_model_as_param) {
		$callback_params = func_get_args();
		array_shift($callback_params);

		foreach ($this->_menu as $item) {
			if (is_callable($function_with_model_as_param) && is_array($callback_params)) {
				call_user_func_array($function_with_model_as_param, array_merge(array(&$item), $callback_params));
			}
		}

		return $this;
	}

	public function menu(array $menu) {
		$this->_menu = self::parse_menu($menu, $this->_i18n_item_prefix);
		return $this;
	}

	public function add_menu(array $menu) {
		$this->_menu = array_merge($this->_menu, self::parse_menu($menu, $this->_i18n_item_prefix));
		return $this;
	}

	public function add_menu_before(array $menu) {
		$this->_menu = array_merge(self::parse_menu($menu, $this->_i18n_item_prefix), $this->_menu);
		return $this;
	}

	public function template($template) {
		if (!empty($template)) {
			$template = (string)$template;
			$template = trim($template, ' /');
		}
		if (!empty($template)) {
			$this->_template = $template;
			$this->_template_customized = true;
		}

		return $this;
	}

	public function add_divider() {
		$this->_menu[] = new Force_Menu_Divider();
		return $this;
	}

	public function add_divider_before() {
		$this->_menu = array_merge([
			new Force_Menu_Divider(),
		], $this->_menu);
		return $this;
	}

	public function add_item($name, $link = '', $icon_class = '', array $menu = array()) {
		if ($name instanceof Force_Menu_Item) {
			$item = $name;
		} else {
			$item = Force_Menu_Item::factory($name, $link, $icon_class, $menu);
		}

		$this->_menu[$item->get_key()] = $item;
		return $this;
	}

	public function add_item_before($name, $link = '', $icon_class = '', array $menu = array()) {
		if ($name instanceof Force_Menu_Item) {
			$item = $name;
		} else {
			$item = Force_Menu_Item::factory($name, $link, $icon_class, $menu);
		}

		$_item = array(
			$item->get_key() => $item,
		);

		$this->_menu = array_merge($_item, $this->_menu);
		return $this;
	}

	public function no_link($inherited = false) {
		$this->_use_link_inherited = !$inherited;
		$this->_use_link = false;
		return $this;
	}

	/*
	 * DELETION
	 */

	public function remove($name = null) {
		$result = false;
		if (is_null($name)) {
			$this->_mark_as_deleted = true;
			$result = true;
		} elseif (array_key_exists($name, $this->_menu)) {
			unset($this->_menu[$name]);
			$result = true;
		}
		return $result;
	}

	public function is_deleted() {
		return $this->_mark_as_deleted;
	}

} // End Force_Menu_Common
