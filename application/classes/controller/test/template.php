<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Test_Template
 * User: legion
 * Date: 15.06.12
 * Time: 19:06
 * Parents:
 * - Controller_Bootstrap_Layout
 * - Controller_Template
 */
class Controller_Test_Template extends Controller_Bootstrap_Layout {

	protected $_auth_required = true;

	protected $_mode = null;
	protected $_step = 1;

	/**
	 * Пред-обработка действия контроллера
	 */
	public function before() {
		if ($this->request->controller() == 'public') {
			$this->_auth_required = false;
		} else {
			$this->_auth_required = true;
			if ($this->user_is_authorized && !$this->user->is_admin()) {
				throw new HTTP_Exception_403;
			}
		}

		parent::before();
	}

	public function action_index() {

	}

	protected function _index_menu($class_name, $path) {
		$this_methods = get_class_methods($class_name);
		$parent_methods = get_class_methods('Controller_Test_Template');
		$this->template->content[] = '<h3>Тесты</h3>';
		foreach ($this_methods as $method_name) {
			if (array_search($method_name, $parent_methods) === false) {
				if ((strpos($method_name, 'action_') !== false) && (strpos($method_name, '_json_') === false)) {
					$name = substr($method_name, 7);
					$link = $path . $name;
					$this->template->content[] = HTML::anchor($link, $name) . '<br/>';
				}
			}
		}
	}

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {
		if ($this->auto_render) {

			/**
			 * MAIN MENU
			 */
			$main_menu = Force_Menu::instance('test')
				->add_item('Public', '/test/public', 'glyphicon-user');

			if ($this->user_is_authorized && $this->user->is_admin()) {
				$main_menu->add_item('Admin', '/test/admin', 'glyphicon-warning-sign');
			}

			$main_menu = $main_menu->render();

			/*
			 * USER MENU
			 */
			$user_menu = Force_Menu_User::instance()->render();
			/*
			 * BRANDS
			 */
			$brand_links = Helper_Bootstrap::get_brand_links(array(
				'Test' => '/test',
			));
			/*
			 * Подключение общих header и footer для всех страниц наследуемых от данного темплэйта
			 */
			$this->template->header = View::factory(TEMPLATE_VIEW . 'bootstrap/header')
				->bind('brand_links', $brand_links)
				->bind('user_menu', $user_menu)
				->bind('main_menu', $main_menu);

			$this->template->footer = View::factory(TEMPLATE_VIEW . 'bootstrap/footer');

		}
		parent::after();
	}

} // End Controller_Test_Template