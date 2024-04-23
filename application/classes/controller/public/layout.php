<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Public_Layout
 * User: legion
 * Date: 14.03.17
 * Time: 22:44
 */
class Controller_Public_Layout extends Controller_Template {

	/**
	 * Шаблон страницы
	 * @var string
	 */
	public $template = 'template/public/layout';

	/**
	 * Пред-обработка действий контроллера
	 */
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->template->content = array();
		}
	}

	/**
	 * Пост-обработка действий контроллера
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

			Helper_Assets::add_before_scripts_in_footer(array(
				'assets/common/js/jquery-2.2.3.min.js',
			));

			Helper_Assets::add_before_styles(array(
				'assets/common/css/font-awesome.min.css',
			));
		}
		parent::after();
	}

} // End Controller_Public_Layout