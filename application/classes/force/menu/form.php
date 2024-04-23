<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu_Form
 * User: legion
 * Date: 06.11.17
 * Time: 23:20
 */
class Force_Menu_Form extends Force_Menu_Common {

	protected $_back_url = '';

	public function __construct(array $menu = array()) {
		$this->menu($menu);
	}

	public static function factory(array $menu = array()) {
		return new self($menu);
	}

	public function render($template = null, $view_data = null) {
		$this->attribute('class', 'menu-sidebar');
		$this->attribute('class', 'affix-top');
//		$this->attribute('class', 'hidden-print');
//		$this->attribute('class', 'hidden-sm');
//		$this->attribute('class', 'hidden-xs');
		$this->attribute('role', 'complementary');
//		$this->attribute('data-spy', 'affix');
//		$this->attribute('data-offset-top', 0);
//		$this->attribute('data-offset-bottom', 3879-799);
//		$this->attribute('data-offset-bottom', 'function(){return $("body").height()-$(".bs-docs-sidenav").height();}');

		foreach ($this->_menu as $name => $item) {
			if ($item instanceof Force_Menu_Item) {
				$item->active();
			}
			break;
		}

		if (!empty($this->_back_url)) {
			$item = Force_Menu_Item::factory('back_to_list')
				->i18n('common.back_to_list')
				->attribute('class', 'back-to-list')
				->link($this->_back_url);
			$this->add_divider_before();
			$this->add_item_before($item);
		}

		$menu_body = $this->_render_menu_body($template);

		return View::factory(FORCE_VIEW . 'form/menu', $view_data)
			->set('attributes', $this->get_attributes())
			->bind('menu_body', $menu_body)
			->bind('back_url', $this->_back_url)
			->render();
	}

	public function back_url($back_url) {
		$this->_back_url = (string)$back_url;
		return $this;
	}

	/*
	 * GET
	 */

	public function as_array() {
		$_array = array();
		foreach ($this->_menu as $key => $item) {
			if ($item instanceof Force_Menu_Divider) {
				$_array[$key] = $item->as_array();
			}

			if ($item instanceof Force_Menu_Item) {
				$_array[$key] = $item->as_array();
			}
		}
		return $_array;
	}

} // End Force_Menu_Form
