<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Lang
 * User: legion
 * Date: 20.05.16
 * Time: 15:08
 */
class Controller_Lang extends Controller {

	protected $_back_url = '/';

	public function before() {
		$back_url = Force_URL::catch_back_url(false);
		if (!empty($back_url)) {
			$this->_back_url = $back_url;
		}
	}

	public function action_index() {
		$lang = $this->request->param('id');
		if (Force_Menu_Lang::instance()->is_allowed($lang)) {
			Cookie::set('lang', $lang);
		}
		$this->request->redirect($this->_back_url);
	}

} // End Controller_Lang