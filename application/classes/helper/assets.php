<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Assets
 * User: legion
 * Date: 19.07.12
 * Time: 20:46
 */
class Helper_Assets {

	const VERSION_PARAM = 'vp';

	protected static $header_styles = array();
	protected static $header_js_vars = array();
	protected static $header_scripts = array();
	protected static $header_scripts_embedded_before = array();
	protected static $header_scripts_embedded_after = array();

	protected static $footer_scripts = array();
	protected static $footer_scripts_embedded_before = array();
	protected static $footer_scripts_embedded_after = array();

	protected static $lang_LANG_default = 'en_US';
	protected static $lang_LANG = null;

	protected static $hash = '';

	protected static $links = array();
	protected static $meta = array();

	/**
	 * ADDERS
	 */

	protected static function _check_asset($asset, $version_hash = null) {
		if (empty($version_hash)) {
			$version_hash = self::get_hash();
		}
		return Helper_Uri::verify_link($asset) . (stristr($asset, '?') === FALSE ? '?' : '&') . self::VERSION_PARAM . '=' . $version_hash;
	}

	/**
	 * @param string|array $styles
	 * @param string       $style_params
	 *
	 * @return array
	 */
	protected static function _check_styles($styles, $style_params = 'screen') {
		$version_hash = self::get_hash();
		if (is_array($style_params)) {
			$style_params = implode(', ', $style_params);
		}
		if (is_string($styles)) {
			$styles = self::_check_asset($styles, $version_hash);
			$styles = array(
				$styles => $style_params,
			);
		} elseif (is_array($styles)) {
			$_styles = array();
			foreach ($styles as $_style => $_params) {
				if (is_numeric($_style)) {
					$_style = $_params;
					$_params = $style_params;
				}
				if (!empty($_style)) {
					$_style = self::_check_asset($_style, $version_hash);
					$_styles[$_style] = $_params;
				}
			}
			$styles = $_styles;
		}
		return $styles;
	}

	/**
	 * @param string|array $scripts
	 * @param string       $script_type
	 *
	 * @return array
	 */
	protected static function _check_scripts($scripts, $script_type = 'text/javascript') {
		$version_hash = self::get_hash();
		if (is_string($scripts)) {
			$scripts = self::_check_asset($scripts, $version_hash);
			$scripts = array(
				$scripts => $script_type,
			);
		} elseif (is_array($scripts)) {
			$_scripts = array();
			foreach ($scripts as $_script => $_type) {
				if (is_numeric($_script)) {
					$_script = $_type;
					$_type = $script_type;
				}
				if (!empty($_script)) {
					$_script = self::_check_asset($_script, $version_hash);
					$_scripts[$_script] = $_type;
				}
			}
			$scripts = $_scripts;
		}
		return $scripts;
	}

	/**
	 * @static
	 *
	 * @param string|array $styles
	 * @param string|array $single_style_params
	 */
	public static function add_styles($styles, $style_params = 'screen') {
		self::$header_styles = self::$header_styles + self::_check_styles($styles, $style_params);
	}

	/**
	 * Добавляет элементы в начало массива $styles
	 * @static
	 *
	 * @param string|array $styles
	 * @param string|array $single_style_params
	 */
	public static function add_before_styles($styles, $style_params = 'screen') {
		self::$header_styles = self::_check_styles($styles, $style_params) + self::$header_styles;
	}

	/**
	 * @static
	 *
	 * @param string|array $js_vars
	 * @param mixed        $single_var_value
	 */
	public static function add_js_vars($js_vars, $single_var_value = null) {
		if (is_array($js_vars)) {
			self::$header_js_vars = self::$header_js_vars + $js_vars;
		} else {
			self::$header_js_vars[$js_vars] = $single_var_value;
		}
	}

	/**
	 * @static
	 *
	 * @param      $js_vars
	 * @param null $single_var_value
	 */
	public static function add_before_js_vars($js_vars, $single_var_value = null) {
		if (!is_array($js_vars)) {
			$js_vars = array(
				(string)$js_vars => $single_var_value,
			);
		}
		self::$header_js_vars = $js_vars + self::$header_js_vars;
	}

	/**
	 * @static
	 *
	 * @param $array_name
	 * @param $array_item
	 */
	public static function js_vars_push_array($array_name, $array_item, $array_key = null) {
		if (is_null($array_key)) {
			self::$header_js_vars[$array_name][] = $array_item;
		} else {
			self::$header_js_vars[$array_name][$array_key] = $array_item;
		}
	}

	/**
	 * @static
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public static function js_var_exists($name) {
		return array_key_exists($name, self::$header_js_vars);
	}

	/**
	 * @static
	 *
	 * @param string|array $scripts
	 */
	public static function add_scripts($scripts, $script_type = 'text/javascript') {
		self::$header_scripts = self::$header_scripts + self::_check_scripts($scripts, $script_type);
	}

	/**
	 * Добавляет элементы в начало массива $scripts
	 * @static
	 *
	 * @param string|array $scripts
	 */
	public static function add_before_scripts($scripts, $script_type = 'text/javascript') {
		self::$header_scripts = self::_check_scripts($scripts, $script_type) + self::$header_scripts;
	}

	/**
	 * Скрипты, которые будут выведены перед футером
	 * @static
	 *
	 * @param string|array $scripts_in_footer
	 */
	public static function add_scripts_in_footer($scripts, $script_type = 'text/javascript') {
		self::$footer_scripts = self::$footer_scripts + self::_check_scripts($scripts, $script_type);
	}

	/**
	 * Добавляет элементы в начало массива $scripts_in_footer
	 * @static
	 *
	 * @param string|array $scripts_in_footer
	 */
	public static function add_before_scripts_in_footer($scripts, $script_type = 'text/javascript') {
		self::$footer_scripts = self::_check_scripts($scripts, $script_type) + self::$footer_scripts;
	}

	/**
	 * Добавляем скрипт и сразу к нему переменные,
	 * чтобы было понятно к какому скрипту какие переменные были добавлены.
	 * @static
	 *
	 * @param      $script
	 * @param      $js_vars
	 * @param null $single_var_value
	 */
	public static function add_script_with_vars($script, $js_vars, $single_var_value = null, $script_type = 'text/javascript') {
		self::add_js_vars($js_vars, $single_var_value);
		self::add_scripts($script, $script_type);
	}

	/**
	 * Добавляет скрипт непосредственно в код страницы
	 * Размещает в head после всех прилинкованных скриптов
	 *
	 * @param $javascript
	 */
	public static function add_script_embedded($javascript) {
		self::add_script_embedded_header_before($javascript);
	}

	public static function add_script_embedded_header_before($javascript) {
		self::$header_scripts_embedded_before[] = $javascript;
	}

	public static function add_script_embedded_header_after($javascript) {
		self::$header_scripts_embedded_after[] = $javascript;
	}

	public static function add_script_embedded_footer_before($javascript) {
		self::$footer_scripts_embedded_before[] = $javascript;
	}

	public static function add_script_embedded_footer_after($javascript) {
		self::$footer_scripts_embedded_after[] = $javascript;
	}

	public static function add_link(array $attributes = array()) {
		self::$links[] = $attributes;
	}

	public static function add_meta(array $attributes = array()) {
		self::$meta[] = $attributes;
	}

	/**
	 * GETTERS
	 */

	public static function get_styles() {
		return self::$header_styles;
	}

	public static function get_js_vars() {
		return self::$header_js_vars;
	}

	public static function get_scripts() {
		return self::$header_scripts;
	}

	public static function get_scripts_in_footer() {
		return self::$footer_scripts;
	}

	public static function get_scripts_embedded() {
		return self::get_scripts_embedded_header_before();
	}

	public static function get_scripts_embedded_header_before() {
		return implode("\n", self::$header_scripts_embedded_before);
	}

	public static function get_scripts_embedded_header_after() {
		return implode("\n", self::$header_scripts_embedded_after);
	}

	public static function get_scripts_embedded_footer_before() {
		return implode("\n", self::$footer_scripts_embedded_before);
	}

	public static function get_scripts_embedded_footer_after() {
		return implode("\n", self::$footer_scripts_embedded_after);
	}

	public static function get_links() {
		return self::$links;
	}

	public static function get_meta() {
		return self::$meta;
	}

	/*
	 * EXTENSIONS
	 */

	/**
	 * @param null $lang
	 *
	 * @return string
	 * @example ru_RU
	 */
	public static function get_lang_LANG($lang = NULL) {
		if (empty(self::$lang_LANG)) {
			$lang = I18n::lang($lang);

			if (strpos($lang, '-') !== false) {
				$lang = explode('-', $lang);
				self::$lang_LANG = $lang[0] . '_' . strtoupper($lang[1]);
			} else {
				self::$lang_LANG = $lang . '_' . strtoupper($lang);
			}
		}
		return self::$lang_LANG;
	}

	/*
	 * Jquery UI
	 */

	public static function add_jquery_ui($type_from_config, $add_before = false) {
		$ui_config = Kohana::$config->load('assets.jquery_ui.' . $type_from_config);
		if (!empty($ui_config)
			&& array_key_exists('css', $ui_config)
			&& array_key_exists('js', $ui_config)
			&& !empty($ui_config['css'])
			&& !empty($ui_config['js'])
		) {
			if ($add_before) {
				self::add_before_styles($ui_config['css']);
				self::add_before_scripts($ui_config['js']);
			} else {
				self::add_styles($ui_config['css']);
				self::add_scripts($ui_config['js']);
			}
		}
	}

	/*
	 * GROWL
	 */

	public static function add_growl($lang = NULL, $add_before = false) {
		$lang = self::get_lang_LANG($lang);

		if (file_exists(DOCROOT . 'assets/growl/js/language/' . $lang . '.js')) {
			$lang_file = 'assets/growl/js/language/' . $lang . '.js';
		} else {
			$lang_file = 'assets/growl/js/language/' . self::$lang_LANG_default . '.js';
		}

		if ($add_before) {
			Helper_Assets::add_before_scripts(array(
				$lang_file,
				'assets/growl/js/jquery.growl.js',
			));

			Helper_Assets::add_before_styles('assets/growl/css/jquery.growl.css');
		} else {
			Helper_Assets::add_scripts(array(
				$lang_file,
				'assets/growl/js/jquery.growl.js',
			));

			Helper_Assets::add_styles('assets/growl/css/jquery.growl.css');
		}
	}

	/**
	 * BOOTSTRAP VALIDATION
	 */

	public static function add_bootstrap_validation($lang = NULL, $add_before = false) {
		$lang = self::get_lang_LANG($lang);

		if (file_exists(DOCROOT . 'assets/validation/js/language/' . $lang . '.js')) {
			$lang_file = 'assets/validation/js/language/' . $lang . '.js';
		} else {
			$lang_file = 'assets/validation/js/language/' . self::$lang_LANG_default . '.js';
		}

		if ($add_before) {
			Helper_Assets::add_before_scripts(array(
				'assets/validation/js/bootstrapValidator.min.js',
				$lang_file,
			));

			Helper_Assets::add_before_styles('assets/validation/css/bootstrapValidator.min.css');
		} else {
			Helper_Assets::add_scripts(array(
				'assets/validation/js/bootstrapValidator.min.js',
				$lang_file,
			));

			Helper_Assets::add_styles('assets/validation/css/bootstrapValidator.min.css');
		}
	}

	/*
	 * HASH
	 */

	public static function get_hash() {
		if (empty(self::$hash)) {
			self::$hash = Cache::instance()->get('assets_hash', '');

			if (empty(self::$hash)) {
				self::$hash = md5(time());
				Cache::instance()->set('assets_hash', self::$hash);
			}
		}
		return self::$hash;
	}

	public static function render_header() {
		return View::factory('helper/assets/header', [
			'header_links' => self::get_links(),
			'header_meta' => self::get_meta(),
			'header_styles' => self::get_styles(),
			'header_scripts' => self::get_scripts(),
			'header_js_vars' => self::get_js_vars(),
			'header_scripts_embedded_before' => self::get_scripts_embedded_header_before(),
			'header_scripts_embedded_after' => self::get_scripts_embedded_header_after(),
		])->render();
	}

	public static function render_footer() {
		return View::factory('helper/assets/footer', [
			'footer_scripts' => self::get_scripts_in_footer(),
			'footer_scripts_embedded_before' => self::get_scripts_embedded_footer_before(),
			'footer_scripts_embedded_after' => self::get_scripts_embedded_footer_after(),
		])->render();
	}

} // End Helper_Assets
