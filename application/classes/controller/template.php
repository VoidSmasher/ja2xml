<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Template
 * User: legion
 * Date: 03.09.12
 * Time: 1:13
 */
class Controller_Template extends Kohana_Controller_Template {

	/**
	 * public $template = 'template/bootstrap/layout';
	 * Шаблон страницы
	 * Указывается ТОЛЬКО в дочерних template
	 *
	 * Прямое наследование контроллера от этого файла ДОЛЖНО вызывать ошибку,
	 * потому что такое использование не допустимо.
	 *
	 * Используй для наследования дочерние template-файлы, смотри например:
	 * - application/classes/controller/bootstrap/layout.php
	 * - application/classes/controller/bootstrap/template.php
	 */

	/**
	 * Название страницы по-умолчанию
	 * @var string
	 */
	protected $page_title = '';

	/**
	 * protected $auth = NULL;
	 * protected $user = NULL;
	 * protected $user_is_authorized = FALSE;
	 * Смотри __get() ниже
	 */

	/**
	 * Указывает, что на данной странице не будет никакой работы с пользователем.
	 * Если указано TRUE, то:
	 * $this->user - будет пустой не загруженной моделью
	 * $this->user_is_authorized - будет FALSE
	 * Затрагивает работу Helper_Auth - все ответы от Helper_Auth будут аналогичными.
	 *
	 * Если template не используется, то можно использовать Helper_Auth::no_user()
	 * - для непосредственного указания Helper_Auth, что пользователя в системе нет.
	 *
	 * Здесь этот параметр не менять - повторно указать в дочернем template.
	 * @var bool
	 */
	protected $_no_user = FALSE;

	/**
	 * Обязательность авторизации для доступа к странице
	 *
	 * Здесь этот параметр не менять - повторно указать в дочернем template.
	 * @var bool
	 */
	protected $_auth_required = FALSE;

	/**
	 * Отвечает за необходимость заполнения обязательных полей у пользователя
	 * Если включен, будет перенаправлять авторизованного пользователя на профиль
	 * для заполнения данных до тех пор пока пользователь их не заполнит.
	 * @var bool
	 */
	protected $require_user_data = TRUE;

	/**
	 * Признак ajax-like запроса
	 * Устанавливается автоматически, смотри before()
	 * @var bool
	 */
	protected $_ajax = FALSE;

	/**
	 * Стандартный вывод ошибок
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Отвечает за автоматический вывод ошибок
	 * Включить для отмены вывода ошибок, например, если предполагается своя реализация вывода ошибок.
	 */
	protected $disable_default_error_output = FALSE;

	/**
	 * Если TRUE, то будет реагировать на наличие файла deploy.flag в корне проекта.
	 * Если FALSE, то никакие действия при наличии файла предприниматься не будут,
	 * однако данные о наличии файла всё равно будут сохранены в сессии и их можно обработать самостоятельно.
	 * Данные из сессии о наличии флага можно получить следующим образом:
	 * Session::instance()->get('deploy');
	 * Вернёт:
	 * - null - если файла нет
	 * - дату создания файла - если файл есть
	 */
	protected $show_deploy_message = FALSE;

	/**
	 * Позволяет запретить показ счётчиков на уровне контроллера.
	 * Это не финальная проверка.
	 * Для полного набора проверок смотри after() ниже.
	 * @var bool
	 */
	protected $_show_counters = TRUE;

	/**
	 * @todo проверить, оживить, облагародить
	 * Стандартный вывод всплывающих сообщений - flash messages
	 * @var array
	 */
	protected $flash = array();

	/**
	 * Тип данных по умолчанию - не менять.
	 * Данным параметром управляет route
	 * Смотри application/routes.php
	 */
	protected $data_type = DATA_HTML;

	/**
	 * Magic method __get
	 * Читай мануал http://php.net/manual/ru/language.oop5.magic.php
	 *
	 * @param $name
	 */
	public function __get($name) {
		switch ($name) {
			case 'auth':
				return Helper_Auth::get_auth();
				break;
			case 'user':
				return Helper_Auth::get_user();
				break;
			case 'user_is_authorized':
				return Helper_Auth::is_user_authorized();
				break;
		}
		return null;
	}

	/**
	 * Пред-обработка действия контроллера
	 */
	public function before() {
		parent::before();
		Helper_Session::init();

		if ($this->show_deploy_message) {
			Force_Deploy_Message::before();
		}

		$this->data_type = mb_strtolower($this->request->param('data_type'));

		/*
		 * Проверяем входящий тип данных, смотри application/config/common.php
		 */
		if (!array_key_exists($this->data_type, Kohana::$config->load('common.data_types'))) {
			throw new HTTP_Exception_404();
		}

		if ($this->auto_render && $this->data_type != DATA_HTML) {
			$this->auto_render = false;
		}

		if ($this->_no_user) {
			Helper_Auth::no_user();
		} else {
			// Не тупи на $this->user, смотри __get() выше.
			if ($this->user_is_authorized) {
				$this->user->set_online();
				if ($this->require_user_data && !(($this->request->controller() == 'profile') && ($this->request->action() == 'index')) && empty($this->user->surname) && empty($this->user->name)) {
					$this->request->redirect(Force_URL::factory('default')
						->controller('profile')
						->get_url(), 302);
				}
			}
		}

		View::set_global('user_is_authorized', $this->user_is_authorized);

		/*
		 * View::set_global('user', $this->user);
		 * Нельзя user пихать в глобал.
		 * Для получения пользователя во view следует использовать Helper_Auth::get_user()
		 */

		View::set_global('request', $this->request);

		/*
		 * Если требуется авторизация, то отправлям позователя на страницу авторизации,
		 * но при этом убеждаемся, что он уже не находится на странице авторизации
		 * и не находится на странице ошибки - 404 например.
		 */
		if ($this->_auth_required && !$this->user_is_authorized && !in_array($this->request->controller(), array(
				'auth',
				'error',
			))
		) {
			Force_URL::save_current_url_as_back_url();
			$this->request->redirect(Force_URL::factory('default')
				->controller('auth')
				->action('login')
				->get_url(), 302);
		}

		// Проверка на запрос AJAX-типа
		if ($this->request->is_ajax() OR $this->request !== Request::initial()) {
			$this->_ajax = TRUE;
		}

		if ($this->auto_render) {

			/*
			 * Профайлер (ни в коем случае не показывать на продакшн сервере)
			 * Для просмотра профайлера на продакшн сервере, следует использовать параметр
			 * ?debug= значение которого можно найти в application/bootstrap.php
			 */
			View::set_global('debug', (Kohana::$environment == Kohana::DEVELOPMENT
				or Kohana::$environment == Kohana::TESTING) ? View::factory('profiler/stats') : '');

			/*
			 * Инициализация значений шаблона по-умолчанию
			 * Ничего не менять, смотреть в after() ниже.
			 */
			$this->template->title_delimiter = ' - '; // разделитель в заголовке страницы
			$this->template->title = Force_Config::get_site_name(); // заголовок по-умолчанию
			$this->template->description = ''; // заголовок по-умолчанию
			$this->template->keywords = ''; // заголовок по-умолчанию
			$this->template->page_title = ''; // заголовок по-умолчанию

			$this->template->header = ''; // заголовок страницы
			$this->template->content = ''; // контент страницы
			$this->template->footer = ''; // подвал страницы

			$this->template->before_header = ''; // контент перед заголовком страницы
			$this->template->before_footer = ''; // контент перед подвалом страницы

			$this->template->after_header = ''; // контект после заголовка страницы
			$this->template->after_footer = ''; // контект после подвала страницы

			/*
			 * Счётчики поисковых систем
			 * Смотри after()
			 */
			$this->template->counter_top = ''; // счётчики в начале страницы
			$this->template->counter_bottom = ''; // счётчики в конце страницы

			/*
			 * @todo проверить использование этого параметра в других проектах
			 * @deprecated
			 */
			$this->template->menu = ''; // не используется

			/*
			 * Выпадающие окна - попапы
			 * Смотри Force_Modal, Helper_Modal и after() ниже
			 * Пример использования $this->template->modal[] = Force_Modal::factory()
			 */
			$this->template->modal = '';

			/*
			 * Вывод ошибок
			 * Используй Helper_Error
			 * В template они попадут автоматически, смотри after()
			 * Для отключения вывода установи в своём контроллере
			 * protected $disable_default_error_output = TRUE
			 */
			$this->template->errors = '';

			/*
			 * Вывод оповещений (вверху страницы, НЕ всплывающие)
			 * Используй Helper_Notify
			 * В template они попадут автоматически, смотри after()
			 */
			$this->template->notifications = '';

			/*
			 * Скрипты и стили
			 * Используй Helper_Assets
			 * В template они попадут автоматически, смотри after()
			 */
			$this->template->assets_header = '';
			$this->template->assets_footer = '';
		}
	}

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {

		/*
		 * Завершаем работу если data_type не равен DATA_HTML
		 */
		if ($this->data_type != DATA_HTML) {
			exit(0);
		}

		if ($this->auto_render && $this->data_type == DATA_HTML) {
			if ($this->show_deploy_message) {
				Force_Deploy_Message::after();
			}

			/**
			 * Формирование заголовка страницы на основе данных о page_title, title_delimiter и title
			 * заголовок для первой страницы не изменяется
			 * @todo облагародить работу с первой страницей, роут может всё же пользовать?
			 */
			if (!empty($this->template->page_title)) {
				if ($this->template->page_title != __('page_title.main')) {
					$this->template->title = $this->template->page_title . $this->template->title_delimiter . $this->template->title;
				} else {
					$this->template->title = $this->template->page_title;
				}
			}

			/**
			 * Вывод ошибок
			 */
			if (!$this->disable_default_error_output) {
				$this->template->errors = Helper_Error::get_view();
			}

			/**
			 * Вывод оповещений
			 */
			$this->template->notifications = Helper_Notify::get_view();

			/**
			 * Получаем все стили, скрипты и переменные.
			 */
			$this->template->assets_header = Helper_Assets::render_header();
			$this->template->assets_footer = Helper_Assets::render_footer();

			/**
			 * Выпадающие окна
			 */
			if (!empty($this->template->modal)) {
				if (is_array($this->template->modal)) {
					foreach ($this->template->modal as $_modal) {
						Helper_Modal::add($_modal);
					}
				} else {
					Helper_Modal::add($this->template->modal);
				}
			}
			$this->template->modal = Helper_Modal::render();

			/**
			 * Чтобы отобразиться счётчикам нужно протий три проверки:
			 * - параметр _show_counters должен быть установлен в TRUE
			 * - контроллер не должен требовать авторизацию
			 * - PRODUCTION environment
			 */
			if ($this->_show_counters && !$this->_auth_required && Kohana::$environment == Kohana::PRODUCTION) {
				$this->template->counter_top = View::factory(TEMPLATE_VIEW . 'common/counters/top');
				$this->template->counter_bottom = View::factory(TEMPLATE_VIEW . 'common/counters/bottom');
			}
		}

		// @todo проверить актуальность и необходимость этой конструкции, сравнить (увязать) её работу с data_type
		// При ajax запросе как ответ используется контент шаблона
		if ($this->_ajax === TRUE) {
			$this->response->body($this->template->content);
		} else {
			parent::after();
		}
	}

} // End Controller_Template