<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Example_Menu
 * User: legion
 * Date: 20.08.14
 * Time: 5:06
 */
class Controller_Developer_Example_Menu extends Controller_Developer_Template {

	public function action_index() {
		Force_Menu::instance('developer')->add_menu_before(Force_Menu::factory()
			->add_item('test_menu_1', '/developer/example_menu')->add_divider()->as_array());

		$this->template->content[] = '<p>Пример в меню выше</p>';
		$this->template->content[] = '<p>Добавлен пункт test_menu_1</p>';
	}

} // End Controller_Developer_Example_Menu