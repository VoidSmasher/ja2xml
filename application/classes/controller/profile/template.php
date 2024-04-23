<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Profile_Template
 * User: legion
 * Date: 16.03.15
 * Time: 2:39
 */
class Controller_Profile_Template extends Controller_Bootstrap_Template {

	protected $_auth_required = true;
	protected $_show_counters = false;

	/*
	 * RESULTS GENERATOR
	 */

	protected function _show_result($title, $message, $btn_link = null, $btn_caption = null) {
		if (is_null($btn_link)) {
			$btn_link = '/';
		}
		if (is_null($btn_caption)) {
			$btn_caption = __('common.back_to_main');
		}
		$this->template->page_title = $title;
		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/result')
			->bind('title', $title)
			->bind('message', $message)
			->bind('btn_link', $btn_link)
			->bind('btn_caption', $btn_caption);
	}

	public function after() {
		$user_menu = Force_Menu_User::instance();
		$profile_menu = clone $user_menu;
		$profile_menu->view(CONTROLLER_VIEW . 'profile/menu', false);
		$this->template->content = View::factory(CONTROLLER_VIEW . '/profile/layout')
			->set('content', $this->template->content)
			->set('menu', $profile_menu->render());
		parent::after();
	}

} // End Controller_Profile_Template