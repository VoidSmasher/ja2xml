<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Pagination
 * User: legion
 * Date: 25.06.12
 * Time: 11:41
 */
class Helper_Pagination {

	/**
	 * @static
	 *
	 * @param     $template
	 * @param     $count
	 * @param int $items_per_page
	 * @param int $pages_in_line
	 *
	 * @return Pagination
	 */
	public static function get_pagination_3000($count, $items_per_page = 20, $pages_in_line = 19) {
		Helper_Assets::add_styles('paginator/css/paginator3000.css', 'screen, projection');
		Helper_Assets::add_scripts('paginator/js/paginator3000.js');
		$pagination = Pagination::factory(array(
			'items_per_page' => $items_per_page,
			'total_items' => $count,
			'pages_in_line' => $pages_in_line,
			'display_items_per_page_selector' => false,
			'view' => 'pagination/paginator3000',
		));
		return $pagination;
	}

	/**
	 * @static
	 *
	 * @param     $count
	 * @param int $items_per_page
	 * @param int $pages_in_line
	 *
	 * @return Pagination
	 */
	public static function get_basic_pagination($count, $items_per_page = 20, $pages_in_line = 9, $view = 'pagination/index') {
		$pagination = Pagination::factory(array(
			'items_per_page' => $items_per_page,
			'total_items' => $count,
			'pages_in_line' => $pages_in_line,
			'display_items_per_page_selector' => false,
			'view' => $view,
		));
		return $pagination;
	}

	/**
	 * @static
	 *
	 * @param     $count
	 * @param int $items_per_page
	 * @param int $pages_in_line
	 *
	 * @return Pagination
	 */
	public static function get_admin_pagination($count, $items_per_page = null, $pages_in_line = null) {
		if (empty($items_per_page)) {
			$items_per_page = Session::instance()
				->get('admin.items_per_page');
			if (empty($items_per_page)) {
				$items_per_page = Kohana::$config->load('pagination.admin.items_per_page.default');
			}
		}
		if (empty($pages_in_line)) {
			$pages_in_line = Kohana::$config->load('pagination.admin.items_in_line');
		}
		if (!is_numeric($items_per_page)) {
			$items_per_page = 10;
		}
		if (!is_numeric($pages_in_line)) {
			$pages_in_line = 9;
		}
		$pagination = Pagination::factory(array(
			'items_per_page' => (int)$items_per_page,
			'total_items' => (int)$count,
			'pages_in_line' => (int)$pages_in_line,
			'view' => 'pagination/admin',
			'first_page_in_url' => true,
			'display_items_per_page_selector' => true,
			'current_page' => array(
				'source' => 'query_string',
				'key' => 'page',
			),
		));
		return $pagination;
	}

	public static function get_admin_setup_url($value) {
		$result = Force_URL::current_clean()
			->action('items_per_page')
			->route_param('id', $value)
			->back_url()
			->get_url();

		return $result;
	}

	public static function set_items_per_page($value) {
		$session = Session::instance();
		$current_items_per_page = $session->get('admin.items_per_page');
		if ($current_items_per_page != $value) {
			$session->set('admin.items_per_page', $value);
		}
		return true;
	}

	public static function get_items_per_page() {
		$session = Session::instance();
		$items_per_page = $session->get('admin.items_per_page');
		if (empty($items_per_page)) {
			$items_per_page = self::get_items_per_page_default_for_admin();
		}
		return $items_per_page;
	}

	public static function get_items_per_page_default_for_admin() {
		return Kohana::$config->load('pagination.admin.items_per_page.default');
	}

	public static function get_items_per_page_variants_for_admin() {
		return Kohana::$config->load('pagination.admin.items_per_page.variants');
	}

	public static function get_items_per_page_select_box_for_admin($use_admin_block = false) {
		$items_per_page = Session::instance()
			->get('admin.items_per_page');
		if (empty($items_per_page)) {
			$items_per_page = Kohana::$config->load('pagination.admin.items_per_page.default');
		}
		$pagination = View::factory('pagination/admin_pages')
			->bind('items_per_page', $items_per_page);
		if ($use_admin_block) {
			$pagination = View::factory('pagination/admin_block')
				->set('pagination', $pagination);
		}
		return $pagination;
	}

} // End Helper_Pagination
