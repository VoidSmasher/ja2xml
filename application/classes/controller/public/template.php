<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Public_Template
 * User: legion
 * Date: 11.05.16
 * Time: 20:42
 */
class Controller_Public_Template extends Controller_Public_Layout {

	/**
	 * Пост-обработка действий контроллера
	 */
	public function after() {
		if ($this->auto_render) {

			/*
			 * MENU
			 */
			$main_menu = Force_Menu::instance('public')->template('public');
//			$user_menu = Force_Menu_User::instance()->render();
			$lang_menu = Force_Menu_Lang::instance()->render();

			if ($this->request->directory() == 'public' && $main_menu->has_item('index')) {
				$main_menu->item('index')->active();
			}

			$main_menu = $main_menu->render();

			/*
			 * Подключение общих header и footer для всех страниц наследуемых от данного темплэйта
			 */
			$this->template->header = View::factory(TEMPLATE_VIEW . 'public/header')
				->bind('main_menu', $main_menu)
//				->bind('user_menu', $user_menu)
				->bind('lang_menu', $lang_menu);

			$this->template->footer = View::factory(TEMPLATE_VIEW . 'public/footer');

			Helper_Assets::add_before_scripts_in_footer(array(

			));

			Helper_Assets::add_before_styles(array(

			));
		}
		parent::after();
	}

} // End Controller_Public_Template