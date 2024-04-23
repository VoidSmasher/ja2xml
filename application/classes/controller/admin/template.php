<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Template
 * User: legion
 * Date: 15.06.12
 * Time: 19:06
 * Parents:
 * - Controller_Bootstrap_Layout
 * - Controller_Template
 */
class Controller_Admin_Template extends Controller_Bootstrap_Layout {

	use Controller_Common_Admin_Actions;
	use Controller_Common_List_Filter;
	use Controller_Common_Uploader;

	protected $_auth_required = true;
	protected $_show_counters = false;

	/**
	 * Пред-обработка действия контроллера
	 */
	public function before() {
		parent::before();

		if ($this->user_is_authorized && !$this->user->is_admin()) {
			throw new HTTP_Exception_403;
		}

		if ($this->auto_render) {
			if ($this->request->action() == 'index') {
				Force_URL::clear_back_url();
			} else {
				Force_URL::catch_back_url();
			}
		}
	}

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {
		if ($this->auto_render) {

			// Automatic page titles
			$this->template->page_title = Helper_Admin::get_page_title($this->page_title);

			if ($this->request->action() == 'delete' && empty($this->template->content)) {
				$this->template->content = Helper_Admin::get_back_button_if_possible();
			}

			/*
			 * MENU
			 */
			define('CALIBRE', $this->request->query('calibre'));

			$main_menu = Force_Menu::instance('admin')->render();
			$user_menu = Force_Menu_User::instance()->render();
			$lang_menu = Force_Menu_Lang::instance()->render();

			/*
			 * BRANDS
			 */
			$brand_links = Helper_Bootstrap::get_brand_links(array(
				__('admin.title') => '/admin',
			));
			/*
			 * Подключение общих header и footer для всех страниц наследуемых от данного темплэйта
			 */
			$this->template->header = View::factory(TEMPLATE_VIEW . 'bootstrap/header')
				->bind('brand_links', $brand_links)
				->bind('lang_menu', $lang_menu)
				->bind('user_menu', $user_menu)
				->bind('main_menu', $main_menu);

			$this->template->footer = View::factory(TEMPLATE_VIEW . 'bootstrap/footer');

			Helper_Assets::add_before_scripts(array(
				'assets/admin/js/bootstrap-custom.js',
			));

			Helper_Assets::add_before_styles(array(
				'assets/admin/css/bootstrap-custom.css' => 'screen',
			));

		}
		parent::after();
	}

} // End Controller_Admin_Template