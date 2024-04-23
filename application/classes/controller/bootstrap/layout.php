<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Bootstrap_Layout
 * User: legion
 * Date: 04.03.14
 * Time: 16:10
 * Parents:
 * - Controller_Template
 *
 * Предназначен для вывода вёрстки на основе Twitter Bootstrap
 */
class Controller_Bootstrap_Layout extends Controller_Template {

	/**
	 * Шаблон страницы
	 * @var string
	 */
	public $template = 'template/bootstrap/layout';

	/**
	 * Пред-обработка действия контроллера
	 */
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->template->content = array();
			$this->template->data_spy_target = '';
			$this->template->fluid = true;
		}
	}

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {
		if ($this->auto_render) {

			/*
			 * Блочная сборка
			 */
			if (is_array($this->template->content)) {
				if (!empty($this->template->content)) {
					$this->template->content = View::factory(TEMPLATE_VIEW . 'common/blocks')
						->set('blocks', $this->template->content);
				} else {
					$this->template->content = '';
				}
			}

			if ($this->show_deploy_message) {
				Helper_Assets::add_before_scripts('assets/common/js/jquery.countdown.min.js');
			}

			Helper_Assets::add_before_scripts(array(
				'assets/common/js/jquery-2.2.3.min.js',
				'assets/common/js/bootstrap.min.js',
				'assets/common/js/selectize.min.js',
			));

			Helper_Assets::add_before_styles(array(
				'assets/common/css/font-awesome.min.css' => 'screen',
				'assets/common/css/bootstrap.min.css' => 'screen',
				'assets/common/css/bootstrap-override.css' => 'screen',
				'assets/common/css/selectize.bootstrap3.css' => 'screen',
			));

		}
		parent::after();
	}

} // End Controller_Bootstrap_Layout