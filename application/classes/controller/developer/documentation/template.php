<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class" Controller_Developer_Documentation_Template
 * User: legion
 * Date: 15.06.12
 * Time: 19:06
 * Parents:
 * - Controller_Developer_Template
 * - Controller_Bootstrap_Layout
 * - Controller_Template
 */
class Controller_Developer_Documentation_Template extends Controller_Developer_Template {

	public function before() {
		parent::before();
		Kohana::$environment = Kohana::PRODUCTION;
	}

	/**
	 * Шаблон страницы
	 * @var string
	 */
	public $template = 'template/bootstrap/docs/layout';

	public function after() {
		if ($this->auto_render) {
			$this->template->data_spy_target = '.docs-sidebar';

//			Helper_Assets::add_before_styles('assets/highlight/css/default.css');
			Helper_Assets::add_before_styles('assets/highlight/css/tomorrow.css');
			Helper_Assets::add_before_scripts('assets/highlight/js/highlight.pack.js');
			Helper_Assets::add_script_embedded_header_after('hljs.initHighlightingOnLoad();');
			Helper_Assets::add_styles('assets/admin/css/bootstrap-custom.css');
		}
		parent::after();
	}

} // End Controller_Developer_Documentation_Template