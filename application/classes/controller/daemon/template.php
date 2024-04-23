<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Daemon_Template
 * User: legion
 * Date: 02.10.12
 * Time: 14:41
 */
class Controller_Daemon_Template extends Controller {

	protected $daemon_name = null;
	protected $storage_dir = null;

	public function before() {
		parent::before();
		$this->_is_cli();

		$this->storage_dir = APPPATH . Helper_Daemon::LOCKER_DIRECTORY;

		$this->daemon_name = $this->request->controller();
		if (Helper_Daemon::check($this->daemon_name)) {
			exit('Already in progress!');
		} else {
			Helper_Daemon::lock($this->daemon_name, getmypid());
		}

		return true;
	}

	public function after() {
		Helper_Daemon::unlock($this->daemon_name);
		parent::after();
	}

	private function _is_cli() {
		if (!Kohana::$is_cli && Kohana::$environment == Kohana::PRODUCTION) {
			throw new HTTP_Exception_404();
		}
	}

} // End Controller Daemon_Template