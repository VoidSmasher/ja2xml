<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Template
 * User: legion
 * Date: 15.06.12
 * Time: 19:06
 */
class Controller_Migration_Template extends Controller_Bootstrap_Layout {

	protected $_auth_required = true;
	protected $_show_counters = false;

	protected $migration;
	protected $migration_title = null;
	protected $migration_description = null;

	public function __construct(Request $request, Response $response) {
		parent::__construct($request, $response);
		$this->migration = Force_Migration::instance();
	}

	/**
	 * Пред-обработка действия контроллера
	 */
	public function before() {
		parent::before();

		if ($this->user_is_authorized && !$this->user->has_role('developer')) {
			throw new HTTP_Exception_403;
		}
	}

	public function get_count() {
		return null;
	}

	public function action_json_start() {
		$this->migration->start($this->get_count());
	}

	public function action_index() {
		$this->migration->title($this->migration_title);
		$this->migration->description($this->migration_description);
		$this->template->content = $this->migration->render($this->get_count());
	}

	public function action_json_finish() {
		$this->migration->stop();
	}

	/**
	 * Пост-обработка действия контроллера
	 */
	public function after() {
		if ($this->auto_render) {
			$this->template->page_title = $this->migration->title();

			/*
			 * MAIN MENU
			 */
			$main_menu = Helper_Menu::create_menu_from_dir(dirname(__FILE__), '/template/')->render();
			/*
			 * USER MENU
			 */
			$user_menu = Force_Menu_User::instance()->render();
			/*
			 * BRANDS
			 */
			$brand_links = Helper_Bootstrap::get_brand_links(array(
				__('migration.title') => '/migration',
			));
			/*
			 * Подключение общих header и footer для всех страниц наследуемых от данного темплэйта
			 */
			$this->template->header = View::factory(TEMPLATE_VIEW . 'bootstrap/header')
				->bind('brand_links', $brand_links)
				->bind('user_menu', $user_menu)
				->bind('main_menu', $main_menu);

			$this->template->footer = View::factory(TEMPLATE_VIEW . 'bootstrap/footer');

			Helper_Assets::add_before_scripts(array(
				'assets/admin/js/bootstrap-custom.js',
			));

			Helper_Assets::add_before_styles(array(
				'assets/admin/css/bootstrap-custom.css' => 'screen',
			));

		}
		parent::after();
	}

} // End Controller_Migration_Template