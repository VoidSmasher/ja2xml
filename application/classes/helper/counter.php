<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 20.06.14
 * Time: 11:18
 */
class Helper_Counter {

	public static function yandex() {
		$yandex_id = strip_tags(Force_Config::instance()->get_param('yandex_id'));
		return (string)empty($yandex_id) ? '' : View::factory(HELPER_VIEW . 'counter/yandex')
			->bind('yandex_code', $yandex_id);
	}

	public static function google() {
		$google_id = strip_tags(Force_Config::instance()->get_param('google_id'));
		return (string)empty($google_id) ? '' : View::factory(HELPER_VIEW . 'counter/google')
			->bind('google_code', $google_id);
	}

	public static function yandex_route() {
		$yandex_code = strip_tags(Force_Config::instance()->get_param('yandex_code'));
		if (!empty($yandex_code)) {
			Route::set('counter_yandex', 'yandex_' . $yandex_code . '.html')
				->defaults(array(
					'directory' => 'counter',
					'controller' => 'yandex',
					'action' => 'index',
					'data_type' => DATA_HTML,
				));
		}
	}

	public static function google_route() {
		$google_code = strip_tags(Force_Config::instance()->get_param('google_code'));
		if (!empty($google_code)) {
			Route::set('counter_google', $google_code . '.html')
				->defaults(array(
					'directory' => 'counter',
					'controller' => 'google',
					'action' => 'index',
					'data_type' => DATA_HTML,
				));
		}
	}

} // End Helper_Counter
