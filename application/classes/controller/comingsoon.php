<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_ComingSoon
 * User: legion
 * Date: 04.03.14
 * Time: 15:58
 */
class Controller_ComingSoon extends Controller_Bootstrap_Layout {

	public function action_index() {
		$this->template->page_title = 'Coming Soon';
		$message = 'We are working on this';
		$this->template->content[] = View::factory(CONTROLLER_VIEW . 'comingsoon/index')
			->bind('title', $this->template->page_title)
			->bind('message', $message);
	}

} // End Controller_ComingSoon