<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Pagination
 * User: legion
 * Date: 16.03.15
 * Time: 3:20
 */
class Pagination extends Kohana_Pagination {

	public $display_items_per_page_selector = false;
	public $display_first_page = true;
	public $display_previous_page = true;
	public $display_next_page = true;
	public $display_last_page = true;

	public function url($page = 1) {
		// Clean the page number
		$page = max(1, (int)$page);

		// No page number in URLs to first page
		if ($page === 1 AND !$this->config['first_page_in_url']) {
			$page = NULL;
		}

		switch ($this->config['current_page']['source']) {
			case 'query_string':
			case 'mixed':

				return Force_URL::current()
					->query_param($this->config['current_page']['key'], $page)
					->get_url();
//				return URL::site($this->route->uri($this->route_params) .
//					$this->query(array($this->config['current_page']['key'] => $page)), null, false);

			case 'route':

				return Force_URL::current()
					->route_param($this->config['current_page']['key'], $page)
					->get_url();
//				return URL::site($this->route->uri(array_merge($this->route_params,
//						array($this->config['current_page']['key'] => $page))) . $this->query());
		}

		return '#';
	}

} // End Pagination
