<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Menu_Divider
 * User: legion
 * Date: 20.08.14
 * Time: 3:01
 */
class Force_Menu_Divider {

	const DIVIDER = ':divider';

	public function __construct() {
	}

	public static function factory() {
		return new self();
	}

	public function render() {
		return '<li class="divider" role="menu"></li>';
	}

	public function as_array() {
		return self::DIVIDER;
	}

	public static function is_divider($params) {
		return (is_string($params) && (strtolower($params) == self::DIVIDER));
	}

} // End Force_Menu_Divider
