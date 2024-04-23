<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Config
 * User: legion
 * Date: 27.05.16
 * Time: 11:04
 */
class Force_Config {

	const DEFAULT_CONFIG = 'DEFAULT_CONFIG';

	protected $filename = 'project_custom';
	protected $filename_default = 'project';
	protected $params = array();
	protected static $_domain = NULL;
	protected static $_instances = array();
	protected $overwrite = array();

	private static $search = [
		"'",
		'\\',
	];
	private static $replace = [
		'&#39;',
		'&#92;',
	];

	public function __construct($filename = 'project_custom') {
		$this->filename = (string)$filename;

		if ($this->filename == 'project_custom') {
			$this->filename_default = 'project';
		} else if ($this->filename_default == 'project') {
			$this->filename_default = NULL;
		}
	}

	// Пока не вижу никакого смысла в factory, вся работа идёт через instance
//	public static function factory($filename = 'project_custom') {
//		return new self($filename);
//	}

	/**
	 * @param string $filename
	 *
	 * @return Force_Config
	 */
	public static function instance($filename = 'project_custom') {
		if (!array_key_exists($filename, self::$_instances)) {
			self::$_instances[$filename] = new self($filename);
		}
		return self::$_instances[$filename];
	}

	protected static function _parse_array(array $value, $level = 1) {
		$_arr = '';
		foreach ($value as $_key => $_value) {
//				if (is_string($_key)) {
//					$_arr .= "'{$_key}'";
//				}
			if (is_string($_value)) {
				$_value = "'{$_value}'";
			} elseif (is_numeric($_value)) {
				// do nothing
			} elseif (is_bool($_value)) {
				$_value = ($_value) ? 'true' : 'false';
			} elseif (is_array($_value)) {
				$_value = self::_parse_array($_value, $level + 1);
			} else {
				$_value = '';
			}
			$_arr .= self::_get_tabs($level + 1) . "'{$_key}' => {$_value},\n";
		}
		if (!empty($_arr)) {
			$_arr = "\n{$_arr}";
		}
		return "array({$_arr}" . self::_get_tabs($level) . ")";
	}

	protected static function _get_tabs($amount) {
		$tabs = '';
		for ($i = 0; $i < $amount; $i++) {
			$tabs .= "\t";
		}
		return $tabs;
	}

	protected function _merge_params($original_params, array $params, $level = 1) {
		if (!is_array($original_params) || empty($original_params)) {
			return $params;
		}
		foreach ($params as $key => $value) {
			if (array_key_exists($key, $original_params)) {
				if ($level == 1) {
					$overwrite = Arr::get($this->overwrite, $key, false);
				} else {
					$overwrite = false;
				}
				if (is_array($original_params[$key]) && is_array($value)) {
					if ($overwrite) {
						$original_params[$key] = $value;
					} else {
						$original_params[$key] = $this->_merge_params($original_params[$key], $value, $level + 1);
					}
				} else {
					$original_params[$key] = $value;
				}
			} else {
				$original_params[$key] = $value;
			}
		}
		ksort($original_params);
		return $original_params;
	}

	public function set_params(array $params) {
		$this->params = $params;
		return $this;
	}

	public function set_param($name, $value, $overwrite = false) {
		$this->params[$name] = $value;
		$this->overwrite[$name] = boolval($overwrite);
		return $this;
	}

	public function save() {
		try {
			$original_params = Kohana::$config
				->load($this->filename)
				->as_array();
		} catch (Exception $e) {
			$original_params = array();
		}

		$params = $this->_merge_params($original_params, $this->params);

		$filename = APPPATH . 'config/' . $this->filename . '.php';

		$file_source = "<?php defined('SYSPATH') or die('Access denied.');\n\n";

		$file_source .= "return array(\n";

		foreach ($params as $key => $value) {
			if (is_array($value)) {
				$value = self::_parse_array($value);
			} else {
				$value = str_replace(self::$search, self::$replace, $value);

				$value = "'{$value}'";
			}
			$file_source .= "\t'{$key}' => {$value},\n";
		}

		$file_source .= ");\n";

		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'w')) {
				return false;
			}

			if (fwrite($handle, $file_source) === FALSE) {
				return false;
			}

			fclose($handle);

		} else {
			return false;
		}

		return true;
	}

	public function default_config($filename) {
		$this->filename_default = (string)$filename;
		return $this;
	}

	/*
	 * GET
	 */

	public function get_param($param_name, $default = null) {
		$result = null;

		if (empty($param_name)) {
			return $default;
		}

		try {
			$original_param = Kohana::$config
				->load($this->filename . '.' . $param_name);
		} catch (Exception $e) {
			$original_param = NULL;
		}

		if (empty($original_param)) {

			if (!empty($this->filename_default)) {
				try {
					$default = Kohana::$config
						->load($this->filename_default . '.' . $param_name);
				} catch (Exception $e) {
					$default = NULL;
				}
			}

			$result = $default;
		} else {
			$result = $original_param;
		}

		if (!is_array($result)) {
			$result = str_replace(self::$replace, self::$search, $result);
		}

		return $result;
	}

	public function get_exploded_param($param_name, $glue = ',') {
		$result = $this->get_param($param_name);

		if (empty($result)) {
			$result = array();
		} else {
			$result = explode($glue, $result);
		}

		return $result;
	}

	public static function get_domain() {
		if (empty(self::$_domain)) {
			$host = (array_key_exists('HTTP_HOST', $_SERVER)) ? $_SERVER['HTTP_HOST'] : '';

			if (empty($host)) {
				$host = Force_Config::instance()->get_param('domain');
			}

			$host = rtrim($host, ' /');

			self::$_domain = $host;
		}

		return self::$_domain;
	}

	/**
	 * @deprecated use Force_URL::get_current_host()
	 * @return string
	 */
	public static function get_host() {
		return Force_URL::get_current_host();
	}

	public static function get_copyright_year() {
		$config = Force_Config::instance();
		$year = $config->get_param('start_year');
		$current_year = date('Y');
		if ($year > $current_year) {
			$year = $current_year;
		}
		if ($year < $current_year) {
			$year = $year . ' - ' . $current_year;
		}
		return $year;
	}

	public static function get_copyright($i18n_key = 'common.copyright') {
		return __($i18n_key, array(
			':company' => self::get_site_name(),
			':year' => self::get_copyright_year(),
		));
	}

	public static function get_site_name() {
		$config = Force_Config::instance();
		$site_name = $config->get_param('title');
		if (empty($site_name)) {
			$site_name = $config->get_param('name');
		}
		return (string)$site_name;
	}

} // End Force_Config
