<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Daemon Install
 * User: Andrey Verstov
 */
class Controller_Daemon_Install extends Controller {

	public function action_index() {
		$cron_settings = Kohana::$config->load('daemons.cron_settings');

		if (is_null($cron_settings)) {
			return false;
		}

		$php = $this->_php_path();
		if (!$php) {
			Log::error_class(__CLASS__, __FUNCTION__, 'Unable to find php path!');
			throw new Exception('Controller_Daemon_Install: Unable to find php path!', 500);
		}

		$tasks = array();
		foreach ($cron_settings as $name => $daemon) {
			$tasks[] = "{$daemon['period']['min']} {$daemon['period']['hour']} {$daemon['period']['day_of_month']} {$daemon['period']['month']} {$daemon['period']['day_of_week']} {$php} " . DOCROOT . "index.php  --uri=daemon/{$name}\n";
		}

		$other_tasks = $this->_get_other_crontab();
		$tasks = array_merge($tasks, $other_tasks);
		$this->_create_crontab($tasks);
		$this->_install_crontab();

		return true;
	}

	private function _php_path() {
		try {
			$paths = exec('which php');
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to exec which php!');
			return false;
		}

		$paths = explode(' ', $paths);

		if (array_key_exists(0, $paths)) {
			$result = $paths[0];
		} else {
			$result = false;
		}

		return $result;
	}

	private function _create_crontab($tasks = array()) {
		$directory = Kohana::$config->load('daemons.locker.directory');

		if (empty($directory)) {
			return false;
		}

		try {
			file_put_contents($directory . 'crontab.tasks', implode("", $tasks));
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to put crontab.tasks!');
		}

		return true;
	}

	private function _get_other_crontab() {
		$other_tasks = array();

		$tasks = array();

		try {
			exec("crontab -l", $tasks);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to exec crontab -l');
		}

		if (!empty($tasks)) {
			foreach ($tasks as $task) {
				if (!preg_match(escapeshellarg(DOCROOT), $task)) {
					$other_tasks[] = $task . "\n";
				}
			}
		}

		return $other_tasks;
	}

	private function _install_crontab() {
		$directory = Kohana::$config->load('daemons.locker.directory');

		if (empty($directory)) {
			return false;
		}

		try {
			exec("crontab {$directory}crontab.tasks");
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to exec crontab for ' . $directory . 'crontab.tasks');
		}

		return true;
	}

} // End Controller Daemon Install