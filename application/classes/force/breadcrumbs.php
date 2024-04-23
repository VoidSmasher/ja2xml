<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Breadcrumbs
 * User: legion
 * Date: 15.08.17
 * Time: 20:58
 */
class Force_Breadcrumbs extends Force_Attributes {

	protected static $_instance = null;

	protected $_breadcrumbs = array();

	public function __construct() {

	}

	public function __toString() {
		return $this->render();
	}

	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function get_current_title() {
		end($this->_breadcrumbs);
		$title = key($this->_breadcrumbs);
		reset($this->_breadcrumbs);
		return $title;
	}

	public function render() {
		$body = [];

		$breadcrumbs_count = count($this->_breadcrumbs);
		$iterator = 1;
		foreach ($this->_breadcrumbs as $label => $link) {
			if ($iterator >= $breadcrumbs_count) {
				$body[] = '<li class="active">' . $label . '</li>';
			} else {
				$body[] = '<li>' . HTML::anchor($link, $label) . '</li>';
			}
			$iterator++;
		}

		$this->attribute('class', 'breadcrumb');

		return '<ol'. HTML::attributes($this->get_attributes()) .'>' . implode("\n", $body) . '</ol>';
	}

	public function add($label, $link = null) {
		$this->_breadcrumbs[$label] = Helper_Uri::auto_fill($link);
		return $this;
	}

	public function remove($label) {
		if (array_key_exists($label, $this->_breadcrumbs)) {
			unset($this->_breadcrumbs[$label]);
		}
		return $this;
	}

} // End Force_Breadcrumbs
