<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Uri
 * User: legion
 * Date: 04.08.12
 * Time: 7:56
 */
class Helper_Uri {

	protected static $back_url = null;
	protected static $simple_back_url = null;

	const BACK_URL_PARAM = 'back_url';
	protected static $forbidden_back_urls = array(
		'/auth',
		'/error',
	);

	/**
	 * @deprecated use Force_URL instead
	 *
	 * @param array      $params
	 * @param bool|true  $use_query_params
	 * @param null       $route_name
	 * @param bool|false $url_as_param
	 * @param bool|false $replace_params
	 *
	 * @return string
	 * @throws Kohana_Exception
	 */
	public static function get_uri($params = array(), $use_query_params = true, $route_name = null, $url_as_param = false, $replace_params = false) {
		if (!is_array($params)) { //$params can be null
			$params = array();
		}
		$request = Request::current();

		if (!empty($route_name)) {
			$route = Route::get($route_name);
		} else {
			$route = $request->route();
			$route_name = Route::name($route);
		}

		$route = self::check_route($route, $route_name, $params);

		$route_params = array(
				'directory' => $request->directory(),
				'controller' => $request->controller(),
				'action' => $request->action(),
			) + $request->param();
		$request_params = $request->query();
		$query = ($use_query_params) ? self::query(($replace_params) ? $params : array_merge($request_params, $params)) : '';
		$url = URL::site($route->uri(array_merge($route_params, $params)) . $query);
		if ($url_as_param) {
			$url = self::base64_url_encode($url);
		}
		return $url;
	}

	protected static function check_route($route, $route_name, $params) {
		$parents = Kohana::$config->load('uri.index_routes');
		if (is_array($parents)) {
			foreach ($parents as $source => $destination) {
				if (($route_name == $source) && array_key_exists('action', $params) && ($params['action'] != 'index')
				) {
					$route = Route::get($destination);
				}
			}
		}
		return $route;
	}

	public static function query($params) {
		if (empty($params)) {
			return '';
		}

		// Note: http_build_query returns an empty string for a params array with only NULL values
		$query = http_build_query($params, '', '&');

		// Don't prepend '?' to an empty string
		return ($query === '') ? '' : ('?' . $query);
	}

	/*
	 * BACK URL
	 */

	public static function get_current_url() {
		$url = Request::current()->uri();
		if ($url == '/') {
			$url = '';
		}
		return URL::base(true) . $url . URL::query();
	}

	public static function insert_back_url($start_with = '?', $url = null) {
		if (is_null(self::$back_url)) {
			if (is_null($url)) {
				$url = self::get_current_url();
			}
			foreach (self::$forbidden_back_urls as $forbidden_url) {
				if (strpos($url, $forbidden_url) !== false) {
					return '';
				}
			}
			self::$back_url = self::base64_url_encode($url);
		}
		return $start_with . self::BACK_URL_PARAM . '=' . self::$back_url;
	}

	/**
	 * @deprecated use Force_URL::set_back_url()
	 *
	 * @param           $back_url
	 * @param bool|true $encode
	 *
	 * @return bool
	 */
	public static function set_back_url($back_url, $encode = true) {
		if ($encode) {
			$back_url = self::base64_url_encode($back_url);
		}
		Session::instance()
			->set(self::BACK_URL_PARAM, $back_url);
		return true;
	}

	/**
	 * @deprecated use Force_URL::get_back_url()
	 *
	 * @param bool|true $get_once
	 * @param bool|true $decode
	 *
	 * @return mixed|string
	 */
	public static function get_back_url($get_once = true, $decode = true) {
		if ($get_once) {
			$back_url = Session::instance()
				->get_once(self::BACK_URL_PARAM, '');
		} else {
			$back_url = Session::instance()
				->get(self::BACK_URL_PARAM, '');
		}
		if (!empty($back_url) && $decode) {
			$back_url = self::base64_url_decode($back_url);
		}
		return $back_url;
	}

	/**
	 * @deprecated use Force_URL::catch_back_url()
	 *
	 * @param bool|true $set_to_session
	 *
	 * @return string
	 */
	public static function catch_back_url($set_to_session = true) {
		$back_url = Arr::get($_GET, self::BACK_URL_PARAM, false);

		if ($set_to_session && $back_url) {
			self::set_back_url($back_url, false);
		}

		return self::base64_url_decode($back_url);
	}

	public static function save_current_uri_as_back_url() {
		$back_url = self::get_current_url();
		self::set_back_url($back_url);
	}

	/**
	 * @deprecated use Force_URL::get_index_uri()
	 *
	 * @param bool|false $back_url_get_once
	 *
	 * @return mixed|string
	 */
	public static function get_index_uri($back_url_get_once = false) {
		$back_url = self::get_back_url($back_url_get_once);
		if (empty($back_url)) {
			$back_url = self::get_uri(array(
				'action' => 'index',
			));
		}
		return $back_url;
	}

	/**
	 * @deprecated use Force_URL::clear_back_url()
	 */
	public static function clear_back_url() {
		Session::instance()->delete(self::BACK_URL_PARAM);
	}

	/**
	 * @deprecated use Force_URL::get_current_host()
	 * @return string
	 */
	public static function get_current_host() {
		return Force_URL::get_current_host();
	}

	public static function get_host($host_name, $protocol = 'http') {
		$hosts = Kohana::$config->load('environment.hosts');
		if (!empty($hosts) && is_array($hosts) && array_key_exists($host_name, $hosts)) {
			$host_name = $hosts[$host_name];
		}

		return (!empty($protocol)) ? $protocol . '://' . $host_name : $host_name;
	}

	public static function seo_clean_uri() {
		if (!Form::is_post() && array_key_exists('REQUEST_URI', $_SERVER)) {
			$request = trim($_SERVER['REQUEST_URI']);
			$request_array = explode('?', $request);
			if (array_key_exists(0, $request_array)) {
				$request = $request_array[0];
			} else {
				$request = '';
			}

			if ($request != '/' && $request != '') {
				if (substr($request, -1) === '/') {
					$request_array[0] = substr($request, 0, strlen($request) - 1);
					$request = implode('?', $request_array);
					Request::initial()
						->redirect($request, 301);
				}
			}
		}
	}

	/**
	 * @deprecated use Force_URL::base64_url_encode()
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public static function base64_url_encode($input) {
		return strtr(base64_encode($input), '+/=', '-_*');
	}

	/**
	 * @deprecated use Force_URL::base64_url_decode()
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public static function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_*', '+/='));
	}

	public static function auto_fill($link) {
		$request = Request::current();
		$link = strtr($link, [
			':dir' => $request->directory(),
			':con' => $request->controller(),
			':act' => $request->action(),
		]);
		return self::verify_link($link);
	}

	public static function verify_link($link) {
		$link = trim($link, ' /');
		if (strpos($link, 'http') === 0) {
			return $link;
		} elseif (substr($link, 0, 1) == '#') {
			return $link;
		} else {
			return URL::site($link);
		}
	}

	public static function get_link($link, $back_url = false) {
		if (!empty($link)) {
			$start_with = (strpos($link, '?') === false) ? '?' : '&';
			if (is_null($back_url)) {
				if (Request::current()->action() == 'index') {
					$link .= self::insert_back_url($start_with);
				}
			} elseif ($back_url) {
				if (!is_bool($back_url) && !empty($back_url)) {
					$link .= self::insert_back_url($start_with, $back_url);
				} else {
					$link .= self::insert_back_url($start_with);
				}
			}

			$link = URL::site($link);
		}

		return $link;
	}

} // End Helper_Uri
