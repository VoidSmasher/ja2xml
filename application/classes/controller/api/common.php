<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Api_Common
 * User: legion
 * Date: 20.08.15
 * Time: 09:17
 */
class Controller_Api_Common extends Controller {

	protected $_application = false;
	protected $_post_only = false;

	public function before() {
		parent::before();
		header("X-Frame-Options: Allow");
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
		header('Content-Type: application/json');

		$key = $this->request->headers('x-auth-token');

		if (empty($key)) {
			$key = Arr::get($_REQUEST, 'key', false);
		}

		if ($key) {
			$this->_application = Core_Api_Application::factory()->key($key)->get_one();
		}

		if (!Core_Api_Application::is_model($this->_application)) {
			API::factory(403)
				->message('Access denied. Invalid API key.')
				->send();
		}

		if ($this->_post_only && !Form::is_post()) {
			API::factory(403)
				->message('Access denied. POST only.')
				->send();
		}
	}

	public function action_index() {
		$this->action_400();
	}

	public function action_400() {
		API::factory(400)
			->message('Bad Request')
			->send();
	}

	public function action_500() {
		API::factory(500)
			->message('Server Error')
			->send();
	}

} // End Controller_Api_Common