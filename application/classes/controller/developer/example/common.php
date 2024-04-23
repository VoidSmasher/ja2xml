<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Example_Common
 * User: legion
 * Date: 15.08.14
 * Time: 17:32
 */
class Controller_Developer_Example_Common extends Controller_Developer_Template {

	public function action_index() {
		$button = Force_Button::factory('кнопка', 'val')
			->btn_warning()
			->btn_disabled()
			->icon('fa-user')
			->attributes(array('id' => 18927))
			->btn_xs();

		$this->template->content[] = $button->render();
	}

} // End Controller_Developer_Example_Common