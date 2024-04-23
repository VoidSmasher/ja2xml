<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_URL
 * User: legion
 * Date: 23.10.17
 * Time: 18:23
 */
class Force_URL {

	const BACK_URL_PARAM = 'back_url';

	protected $route;
	protected $query = array();

	protected $lang = null;
	protected $directory = null;
	protected $controller = null;
	protected $action = null;
	protected $route_params = array();

	protected static $current_host = null;

	protected static $back_url = null;
	protected static $forbidden_back_urls = array(
		'/auth',
		'/error',
	);

	protected static $default_lang = 'ru';
	protected static $default_lang_hide = false;

	public function __construct($route_or_route_name = null) {
		if (!is_null($route_or_route_name)) {
			/*
			 * New
			 */
			$this->route($route_or_route_name);
		} else {
			/*
			 * Current
			 */
			$route = Request::current()->route();
			if ($route instanceof Route) {
				$this->route = $route;
			} else {
				throw new Kohana_Exception('Cannot determine current route');
			}
			$this->apply_current_route_params();
			$this->apply_current_query();
		}
	}

	public function __toString() {
		return $this->get_url();
	}

	public static function current() {
		return new self();
	}

	public static function current_clean() {
		$url = new self();
		$url->clean_route_params()->clean_query();
		return $url;
	}

	public static function factory($route_or_route_name = 'default') {
		return new self($route_or_route_name);
	}

	/*
	 * GET
	 */

	public function get_encoded_url() {
		$url = $this->get_url();
		return self::base64_url_encode($url);
	}

	public function get_url($protocol = NULL) {
		if (empty($this->lang)) {
			$this->lang = i18n::get_lang();
		}

		$params = $this->route_params;
		$params['directory'] = $this->directory;
		$params['controller'] = $this->controller;
		$params['action'] = $this->action;
		$params['lang'] = $this->lang;

		/*
		 * Скрывает основной язык сайта в урлах
		 */
		if (self::$default_lang_hide) {
			if ($this->lang == self::$default_lang) {
				unset($params['lang']);
			}
		}

		$rest_params = $params;

		unset($rest_params['directory']);
		unset($rest_params['controller']);
		unset($rest_params['action']);
		unset($rest_params['lang']);

		$route_defaults = $this->route->defaults();

		/*
		 * Чистим параметры от значений, которые совпадают со значениями по умолчанию.
		 */
		if (is_array($route_defaults)) {
			foreach ($params as $key => $value) {
				if (empty($value)) {
					unset($rest_params[$key]);
				}
				if (array_key_exists($key, $route_defaults) && ((string)$route_defaults[$key] == (string)$value)) {
					unset($rest_params[$key]);
					if ($key == 'directory' || $key == 'controller' || $key == 'lang') {
						/*
						 * Эти параметры являются обязательными, поэтому через чистку не проходят.
						 */
						continue;
					}
					unset($params[$key]);
				}
			}
		}

		if (!empty($rest_params)) {
			$params['action'] = $this->action;
		}

		$url = $this->route->uri($params);

		$url .= self::query($this->query);

		if (!($this->route->is_external())) {
			/*
			 * Определяем протокол, если он явно не был передан.
			 */
//			if (is_null($protocol)) {
//				if (array_key_exists('HTTPS', $_SERVER) && !empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off')) {
//					$protocol = 'https';
//				} elseif (array_key_exists('SERVER_PORT', $_SERVER) && ($_SERVER['SERVER_PORT'] == 443)) {
//					$protocol = 'https';
//				} else {
//					$protocol = 'http';
//				}
//			}
			$url = URL::site($url, $protocol);
		}

		return $url;
	}

	public function get_route_name() {
		return Route::name($this->route);
	}

	public function get_route() {
		return $this->route;
	}

	public static function get_current_host() {
		if (is_null(self::$current_host)) {
			$domain = Force_Config::get_domain();

			$protocol = 'http';

			if (array_key_exists('HTTPS', $_SERVER)) {

				if (!empty($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
					$protocol = 'https';
				}

			}

			self::$current_host = $protocol . '://' . $domain;
		}

		return self::$current_host;
	}

	/*
	 * DEFAULT
	 */

	public function get_default($key) {
		$route_defaults = $this->route->defaults();
		$result = NULL;

		if (is_array($route_defaults) && array_key_exists($key, $route_defaults)) {
			$result = $route_defaults[$key];
		}

		return $result;
	}

	public function has_default($key) {
		$route_defaults = $this->route->defaults();
		$result = false;

		if (is_array($route_defaults)) {
			$result = array_key_exists($key, $route_defaults);
		}

		return $result;
	}

	/*
	 * SET
	 */

	public function route($route_or_route_name) {
		if ($route_or_route_name instanceof Route) {
			$this->route = $route_or_route_name;
		} else {
			$this->route = Route::get($route_or_route_name);
		}
		return $this;
	}

	public function directory($value) {
		$this->directory = strval($value);
		return $this;
	}

	public function controller($value) {
		$this->controller = strval($value);
		return $this;
	}

	public function action($value) {
		$this->action = strval($value);
		return $this;
	}

	public function lang($value) {
		$this->lang = strval($value);
		return $this;
	}

	public function route_param($key, $value) {
		switch ($key) {
			case 'directory':
				$this->directory($value);
				break;
			case 'controller':
				$this->controller($value);
				break;
			case 'action':
				$this->action($value);
				break;
			case 'lang':
				$this->lang($value);
				break;
			default:
				$this->route_params[(string)$key] = $value;
		}
		return $this;
	}

	public function query_param($key, $value) {
		$this->query[(string)$key] = $value;
		return $this;
	}

	/*
	 * DATA_TYPE
	 */

	public function data_html() {
		$this->route_param('data_type', DATA_HTML);
		return $this;
	}

	public function data_json() {
		$this->route_param('data_type', DATA_JSON);
		return $this;
	}

	public function data_xml() {
		$this->route_param('data_type', DATA_XML);
		return $this;
	}

	/*
	 * CLEAN
	 */

	public function clean_route_params() {
		$this->route_params = array();
		return $this;
	}

	public function clean_query() {
		$this->query = array();
		return $this;
	}

	/*
	 * APPLY CURRENT
	 */

	public function apply_current_route_params() {
		$this->lang = i18n::get_lang();
		$this->directory = Request::current()->directory();
		$this->controller = Request::current()->controller();
		$this->action = Request::current()->action();
		$this->route_params = Request::current()->param();
		return $this;
	}

	public function apply_current_query() {
		$query = Request::current()->query();
		if (is_array($query)) {
			$this->query = $query;
		} else {
			throw new Kohana_Exception('Cannot get current query');
		}
		return $this;
	}

	/*
	 * LANGUAGE
	 */

	public static function hide_lang($lang) {
		self::$default_lang = strval($lang);
		self::$default_lang_hide = boolval($lang);
	}

	/*
	 * BACK URL
	 */

	public static function catch_back_url($set_to_session = true) {
		$back_url = Arr::get($_GET, self::BACK_URL_PARAM, false);

		if ($set_to_session && $back_url) {
			self::set_back_url($back_url, false);
		}

		return self::base64_url_decode($back_url);
	}

	public static function clear_back_url() {
		Session::instance()->delete(self::BACK_URL_PARAM);
	}

	public static function set_back_url($back_url, $encode = true) {
		if ($encode) {
			$back_url = self::base64_url_encode($back_url);
		}
		Session::instance()->set(self::BACK_URL_PARAM, $back_url);
		return true;
	}

	public static function get_back_url($get_once = true, $decode = true) {
		if ($get_once) {
			$back_url = Session::instance()->get_once(self::BACK_URL_PARAM, '');
		} else {
			$back_url = Session::instance()->get(self::BACK_URL_PARAM, '');
		}
		if (!empty($back_url) && $decode) {
			$back_url = self::base64_url_decode($back_url);
		}
		return $back_url;
	}

	protected static function _get_current_url() {
		$url = Request::current()->uri();
		if ($url == '/') {
			$url = '';
		}
		return URL::base(true) . $url . URL::query();
	}

	protected static function _get_back_url_string($url = null) {
		if (is_null(self::$back_url)) {
			if (is_null($url)) {
				$url = self::_get_current_url();
			}
			foreach (self::$forbidden_back_urls as $forbidden_url) {
				if (strpos($url, $forbidden_url) !== false) {
					return '';
				}
			}
			self::$back_url = self::base64_url_encode($url);
		}
		return self::$back_url;
	}

	public static function save_current_url_as_back_url() {
		$back_url = self::_get_current_url();
		self::set_back_url($back_url);
	}

	public function back_url($url = null) {
		$this->query_param(self::BACK_URL_PARAM, self::_get_back_url_string($url));
		return $this;
	}

	/*
	 * URL ENCODE / DECODE
	 */

	public static function base64_url_encode($input) {
		return strtr(base64_encode($input), '+/=', '-_*');
	}

	public static function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_*', '+/='));
	}

	/*
	 * CUSTOM QUERY BUILDER
	 */

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
	 * BACK TO INDEX
	 */

	public static function back_to_index($index_path = null, $object_id = null) {
		$back_url = Force_URL::get_back_url();

		if (!empty($back_url)) {
			$redirect_to = $back_url;
		} else {
			if (empty($index_path)) {
				$index_path = Force_URL::current_clean()->action('index')->get_url();
				if (!empty($object_id)) {
					$index_path .= '#' . $object_id;
				}
			}
			$redirect_to = $index_path;
		}

		Request::current()->redirect($redirect_to);
	}

	public static function get_index_uri($back_url_get_once = false) {
		$back_url = self::get_back_url($back_url_get_once);
		if (empty($back_url)) {
			$back_url = Force_URL::current_clean()
				->action('index');
		}
		return $back_url;
	}

} // End Force_URL
