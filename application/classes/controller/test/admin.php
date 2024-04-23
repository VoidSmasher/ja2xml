<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Test_Admin
 * User: legion
 * Date: 23.05.13
 * Time: 14:19
 */
class Controller_Test_Admin extends Controller_Test_Template {

	public function action_index() {
		$this->_index_menu('Controller_Test_Admin', '/test/admin/');
	}

	/*
	 * ADMIN TESTS
	 * Все нижеследующие тесты будут доступны только администраторам
	 */

	public function action_info() {
		echo phpinfo();
		exit(0);
	}

	public function action_time() {
		echo date('d.m.Y H:i:s T');
	}

	public function action_environment() {
		switch (Kohana::$environment) {
			case Kohana::PRODUCTION:
				$environment = 'PRODUCTION';
				break;
			case Kohana::DEVELOPMENT:
				$environment = 'DEVELOPMENT';
				break;
			default:
				$environment = Kohana::$environment;
				break;
		}
		$this->template->content = $environment;
	}

	public function action_test_speed() {
		$token = Profiler::start('Attachment', 'Instance');
		Attachment::instance();
		Profiler::stop($token);

		$bonus_list = array();
		$token = Profiler::start('Attachment', 'Bonus');
		for ($i = 1; $i < 1000000; $i++) {
			$bonus_list = Attachment::instance()->get_bonus_list();
		}
		Profiler::stop($token);
		Helper_Error::var_dump($bonus_list, 'Bonus');

		$mount_list = array();
		$token = Profiler::start('Attachment', 'Mount');
		for ($i = 1; $i < 1000000; $i++) {
			$mount_list = Attachment::instance()->get_mount_list();
		}
		Profiler::stop($token);
		Helper_Error::var_dump($mount_list, 'Mount');
	}

} // End Controller_Test_Admin