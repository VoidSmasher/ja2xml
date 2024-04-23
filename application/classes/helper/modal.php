<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Modal
 * User: legion
 * Date: 19.07.13
 * Time: 11:48
 */
class Helper_Modal {

	protected static $_modal = array();

	public static function add($rendered_modal_view) {
		if ($rendered_modal_view instanceof Force_Modal) {
			$rendered_modal_view = $rendered_modal_view->render();
		}
		self::$_modal[] = (string)$rendered_modal_view;
	}

	public static function get() {
		return self::$_modal;
	}

	public static function render() {
		return implode("\n", self::$_modal);
	}

} // End Helper_Modal
