<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Counter_Google
 * User: ener
 * Date: 23.12.13
 * Time: 11:45
 */
class Controller_Counter_Google extends Controller {

	public function action_index() {
		echo 'google-site-verification: ' . Force_Config::instance()->get_param('google_code') . '.html';
	}

} // End Controller_Counter_Google