<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Api_Documentation
 * User: legion
 * Date: 19.07.13
 * Time: 9:28
 */
class Helper_Api_Documentation {

	const DOCUMENTATION_PATH = 'helper/api/documentation/';
	const DEFAULT_TITLE = 'default_documentation_section_id';

	protected $api_key = null;
	protected $host = null;
	protected $html = '';
	protected $menu_links = array();
	protected $navigation = array();
	protected $method_path = '';
	protected $method_type = 'json';
	protected $methods = array();
	protected $last_title = self::DEFAULT_TITLE;

	public function __construct($api_key) {
		$this->api_key = $api_key;
		$this->host = Force_URL::get_current_host();
	}

	public static function factory($api_key) {
		return new Helper_Api_Documentation($api_key);
	}

	public function publish() {
		echo $this->render();
	}

	public function render() {
		Helper_Assets::add_styles(array(
			'assets/common/css/bootstrap.min.css' => 'screen',
			'assets/common/css/bootstrap-docs.css' => 'screen',
		));

		Helper_Assets::add_scripts(array(
			'assets/common/js/jquery-2.2.3.min.js',
			'assets/common/js/bootstrap.min.js',
//			'assets/common/js/bootstrap-side-nav.js',
		));

		$content = $this->_render_content();

		$header = View::factory(self::DOCUMENTATION_PATH . 'header')
			->set('brand_links', array(
				'API Documentation' => $_SERVER['REQUEST_URI'],
			))
			->set('main_menu', $this->menu_links);

		$navigation = View::factory(self::DOCUMENTATION_PATH . 'chunks/navigation')
			->set('navigation', $this->navigation);

		$html = View::factory(self::DOCUMENTATION_PATH . 'bootstrap')
			->set('title', 'API DOCUMENTATION')
			->set('description', 'Api documentation')
			->set('keywords', 'Api, Documentation')
			->set('styles', Helper_Assets::get_styles())
			->set('js_vars', Helper_Assets::get_js_vars())
			->set('scripts', Helper_Assets::get_scripts())
			->set('scripts_in_footer', Helper_Assets::get_scripts_in_footer())
			->set('errors', Helper_Error::get_view())
			->set('notifications', Helper_Notify::get_view())
			->set('modal', Helper_Modal::render())
			->set('header', $header)
			->set('before_footer', '')
			->set('footer', '')
			->set('navigation', $navigation)
			->set('content', $content);

		return $html;
	}

	protected function _render_content() {
		$html = '';
		foreach ($this->methods as $title => $section_params) {
			if ($title != self::DEFAULT_TITLE) {
				preg_match('/\s*[a-zA-Z0-9\-_!?&,\.\[\]()]*/', $title, $matches);
				if (!empty($matches[0])) {
					$link = strtolower(strtr($title, array(
						' ' => '-',
						'&' => '',
						'?' => '',
						'!' => '',
						',' => '',
						'.' => '_',
						'[' => '',
						']' => '',
						'(' => '',
						')' => '',
					)));
				} else {
					$link = null;
				}
				$section_id = $this->_add_to_navigation($title, $link);

				$html .= (string)View::factory(self::DOCUMENTATION_PATH . 'title')
					->bind('section_id', $section_id)
					->bind('title', $title)
					->set('description', $section_params['description'])
					->render();
			}
			if (array_key_exists('methods', $section_params)) {
				ksort($section_params['methods']);
				foreach ($section_params['methods'] as $method_name => $method_params) {
					if (is_array($method_params)) {
						preg_match('/[a-zA-Z0-9_]*/', $method_name, $matches);

						if (!empty($matches[0])) {
							$link = strtolower($method_name);
						} else {
							$link = null;
						}
						$section_id = $this->_add_to_navigation($method_name, $link);

						$html .= (string)View::factory(self::DOCUMENTATION_PATH . 'method', $method_params)
							->bind('section_id', $section_id)
							->render();
					}
				}
			}
		}

		return $html;
	}

	public function menu_links(array $menu_links) {
		$this->menu_links = $menu_links;
		return $this;
	}

	public function insert_title($title, $description = null) {
		$this->last_title = $title;
		$this->methods[$title] = array(
			'description' => $this->_get_description($description),
		);

		return $this;
	}

	public function insert_method($method_name, $description = null, array $params = null, array $errors = null) {
//		preg_match('/\w+\/(\w+)\.json/', $controller_and_action_from_api_root, $matches);
//		$method_name = $matches[1];

//		$method_path = '/api/' . $controller_and_action_from_api_root;
		$method_type = (!empty($this->method_type)) ? '.' . $this->method_type : '';
		$method_path = $this->method_path . $method_name . $method_type;
		$action = $this->host . $method_path;
		if (is_null($params)) {
			$params = array();
		}
		if (is_null($errors)) {
			$errors = array();
		}

		$this->methods[$this->last_title]['methods'][$method_name] = array(
			'controller_and_action_from_api_root' => $action,
			'method_name' => $method_name,
			'method_path' => $method_path,
			'host' => $this->host,
			'action' => $action,
			'application_key' => $this->api_key,
			'params' => $params,
			'errors' => $errors,
			'description' => $this->_get_description($description),
		);

		return $this;
	}

	public function set_method_path($path) {
		$this->method_path = $path;
		return $this;
	}

	public function set_method_type($type) {
		$this->method_type = $type;
		return $this;
	}

	protected function _get_description($description) {
		if (is_array($description)) {
			$description = implode("\n", $description);
		}

		$description = (string)$description;

		if (!empty($description)) {
			$description = nl2br($description);
		}

		return $description;
	}

	protected function _add_to_navigation($name, $link = null) {
		if (empty($link)) {
			$link = 'section_' . count($this->navigation);
		}
		$this->navigation['#' . $link] = $name;
		return $link;
	}

} // End Helper_Api_Documentation
