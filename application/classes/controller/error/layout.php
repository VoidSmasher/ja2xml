<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Error_Layout
 * User: legion
 * Date: 04.03.14
 * Time: 16:10
 * Parents:
 * - Controller_Template
 *
 * Предназначен для вывода вёрстки на основе Twitter Bootstrap
 */
class Controller_Error_Layout extends Controller_Template {

	/**
	 * Шаблон страницы
	 * @var string
	 */
	public $template = 'template/error/layout';

	/**
	 * Указывает, что на данной странице не будет никакой работы с пользователем.
	 * Если указано TRUE, то:
	 * $this->user - будет пустой не загруженной моделью
	 * $this->user_is_authorized - будет FALSE
	 * Затрагивает работу Helper_Auth - все ответы от Helper_Auth будут аналогичными.
	 *
	 * @var bool
	 */
	protected $_no_user = TRUE;

	/**
	 * Пред-обработка действия контроллера
	 */
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->template->title = '';
			$this->template->text = '';
			$this->template->code = 500;
			$this->template->show_back_to_main = false;
		}
	}

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {
		if ($this->auto_render) {

		}
		parent::after();
	}

} // End Controller_Error_Layout