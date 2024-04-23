<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Daemon Errorfinder
 * User: Verstov Andrey
 */
class Controller_Daemon_Errorfinder extends Controller_Daemon_Template {

	protected $_subject = 'Daemon Errorfinder';
	protected $_recipients = array();

	public function action_index() {
		$this->_recipients = Kohana::$config->load('daemons.cron_settings.errorfinder.config.recipients');

		if (empty($this->_recipients)) {
			return false;
		}

		$this->_subject .= ': ' . Force_Config::get_domain();

		$file_path = APPPATH . 'logs/' . date('Y/m/d', time()) . '.php';
		try {
			$fp = fopen($file_path, 'r+');

			$errors = array();

			while (!feof($fp)) {
				$line = fgets($fp);

				if (preg_match("/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) --- ERROR/", $line, $matches) === 1) {
					$datetime = $matches[1];
				} else {
					continue;
				}

				$errorLine = str_replace($datetime . ' --- ERROR: ', '', $line);

				if (strtotime($datetime) >= (time() - 3600)) {
					$formattedTime = date("Y-m-d H:i:s", strtotime($datetime));
					if (!isset($errors[$errorLine])) {
						$errors[$errorLine]['firsttime'] = $formattedTime;
						$errors[$errorLine]['lasttime'] = $formattedTime;
						$errors[$errorLine]['times'] = 1;
					} else {
						$errors[$errorLine]['lasttime'] = $formattedTime;
						$errors[$errorLine]['times']++;
					}
				}
			}
			$newPosition = ftell($fp);

			if (!rewind($fp)) {
				Log::error_class(__CLASS__, __FUNCTION__, 'Cannot rewind!');
			}

			fwrite($fp, $newPosition . "        \n");
			fclose($fp);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to read logs!');
		}

		if (!empty($errors)) {
			$body = array();
			foreach ($errors as $text => $error) {
				if ($error['times'] > 1) {
					$body[] = sprintf("%s - %s times, first time - %s, last time - %s", $text, $error['times'], $error['firsttime'], $error['lasttime']);
				} else {
					$body[] = sprintf("%s - %s time, %s", $text, $error['times'], $error['firsttime']);
				}
			}
			if (!empty($body)) {
				$body = implode("\n\n", $body);
				try {
					Helper_Mail::send($this->_subject, $body, $this->_recipients, false);
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to send mail!');
				}
			}
		}

		return true;
	}

} // End Controller Daemon Errorfinder