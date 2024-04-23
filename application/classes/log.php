<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Log
 * User: legion
 * Date: 22.05.17
 * Time: 18:28
 */
class Log extends Kohana_Log {

	/*
	 * ERROR
	 */

	public static function jelly_validation_exception(Jelly_Validation_Exception $e, $class, $function, $message = NULL, array $values = NULL) {
		$message = $class . '::' . $function . (!empty($message) ? ': ' . $message : '');
		$errors = $e->errors();
		foreach ($errors as $error) {
			if (is_string($error)) {
				$message .= PHP_EOL . $error;
			}
		}
		$message .= PHP_EOL . $e->getMessage();
		$message .= PHP_EOL . $e->getTraceAsString();
		Log::instance()->add(Log::ERROR, $message, $values)->write();
	}

	public static function exception(Exception $e, $class, $function, $message = NULL, array $values = NULL) {
		$message = $class . '::' . $function . (!empty($message) ? ': ' . $message : '');
		$message .= PHP_EOL . $e->getMessage();
		$message .= PHP_EOL . $e->getTraceAsString();
		Log::instance()->add(Log::ERROR, $message, $values)->write();
	}

	/*
	 * Автоматически создаёт экземпляр класса Exception, но не бросает его.
	 * Используется только для записи ошибки и трейса ошибки в лог.
	 */
	public static function error_class($class, $function, $message = NULL, array $values = NULL) {
		$e = new Exception();
		$message = $class . '::' . $function . (!empty($message) ? ': ' . $message : '');
		$message .= PHP_EOL . $e->getTraceAsString();
		Log::instance()->add(Log::ERROR, $message, $values)->write();
	}

	/*
	 * Автоматически создаёт экземпляр класса Exception, но не бросает его.
	 * Используется только для записи ошибки и трейса ошибки в лог.
	 */
	public static function error($message, array $values = NULL) {
		$e = new Exception();
		$message .= PHP_EOL . $e->getTraceAsString();
		Log::instance()->add(Log::ERROR, $message, $values)->write();
	}

	/*
	 * WARNING
	 */

	public static function warning_class($class, $function, $message, array $values = NULL) {
		$message = $class . '::' . $function . (!empty($message) ? ': ' . $message : '');
		Log::instance()->add(Log::WARNING, $message, $values)->write();
	}

	public static function warning($message, array $values = NULL) {
		Log::instance()->add(Log::WARNING, $message, $values)->write();
	}

	/*
	 * INFO
	 */

	public static function info_class($class, $function, $message, array $values = NULL) {
		$message = $class . '::' . $function . (!empty($message) ? ': ' . $message : '');
		Log::instance()->add(Log::INFO, $message, $values)->write();
	}

	public static function info($message, array $values = NULL) {
		Log::instance()->add(Log::INFO, $message, $values)->write();
	}

	/*
	 * DEBUG
	 */

	public static function debug_class($class, $function, $message, array $values = NULL) {
		$message = $class . '::' . $function . (!empty($message) ? ': ' . $message : '');
		Log::instance()->add(Log::DEBUG, $message, $values)->write();
	}

	public static function debug($message, array $values = NULL) {
		Log::instance()->add(Log::DEBUG, $message, $values)->write();
	}

} // End log
