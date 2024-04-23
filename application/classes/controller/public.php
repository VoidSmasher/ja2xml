<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Public
 * User: legion
 * Date: 03.09.12
 * Time: 1:13
 */
class Controller_Public extends Controller_Public_Template {

	public function action_index() {
		$this->template->content = View::factory(CONTROLLER_VIEW . 'public/index');
	}

} // End Controller_Public