<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Bootstrap_Template
 * User: legion
 * Date: 15.06.12
 * Time: 19:06
 * Parents:
 * - Controller_Template
 * - Controller_Bootstrap_Layout
 *
 * Предназначен для вывода вёрстки на основе Twitter Bootstrap с использованием header и footer
 */
class Controller_Bootstrap_Template extends Controller_Bootstrap_Layout {

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {
		if ($this->auto_render) {

			/*
			 * MENU
			 */
			$main_menu = Force_Menu::instance()->render();
			$user_menu = Force_Menu_User::instance()->render();
			$lang_menu = Force_Menu_Lang::instance()->render();

			/*
			 * BRANDS
			 */
			$brand_links = Helper_Bootstrap::get_brand_links();

			/*
			 * Подключение общих header и footer для всех страниц наследуемых от данного темплэйта
			 */
			$this->template->header = View::factory(TEMPLATE_VIEW . 'bootstrap/header')
				->bind('brand_links', $brand_links)
				->bind('main_menu', $main_menu)
				->bind('user_menu', $user_menu)
				->bind('lang_menu', $lang_menu);

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

} // End Controller_Bootstrap_Template