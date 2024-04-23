<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu_User
 * User: legion
 * Date: 19.08.14
 * Time: 21:43
 */
class Force_Menu_User extends Force_Menu {

	protected $_view = 'menu/user';
	protected $_user_name = '';

	public function __construct(array $menu = array()) {
		$this->_user_name = Helper_Auth::get_user()->get_name();
		parent::__construct($menu);
	}

	public static function factory(array $menu = array()) {
		return new self($menu);
	}

	/**
	 * @param $name
	 *
	 * @return Force_Menu_User
	 */
	public static function instance($name = 'user', $load = true) {
		if (!array_key_exists($name, self::$_instances)) {
			if ($load) {
				$menu = Kohana::$config->load('menu.' . $name);
				if (!is_array($menu)) {
					$menu = array();
				}
			} else {
				$menu = array();
			}
			self::$_instances[$name] = new self($menu);
		}
		return self::$_instances[$name];
	}

	public function render($template = null, $view_data = null) {
		$_view_data = array(
			'user_name' => $this->_user_name,
		);
		if (is_array($view_data)) {
			$view_data = array_merge($_view_data, $view_data);
		} else {
			$view_data = $_view_data;
		}
		return parent::render($template, $view_data);
	}

	/*
	 * SET
	 */

	public function user_name($user_name) {
		$user_name = (string)$user_name;
		$this->_user_name = trim($user_name);
		return $this;
	}

} // End Force_Menu_User
