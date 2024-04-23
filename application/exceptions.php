<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 03.07.12
 * Time: 14:27
 */
try {
	echo Request::factory()
		->execute()
		->send_headers()
		->body();
} catch (HTTP_Exception $e) {
	echo Request::factory('error/' . $e->getCode())->execute()->send_headers()->body();
} catch (Exception $e) {
	Log::error($e->getMessage() . "\n" . $e->getTraceAsString());

	if (Kohana::$environment === Kohana::DEVELOPMENT) {
		throw $e;
	} else {
		echo Request::factory('error/500')->execute()->send_headers()->body();
	}
}
