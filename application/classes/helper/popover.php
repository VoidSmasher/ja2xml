<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Popover
 * User: a.stifanenkov
 * Date: 28.11.2012
 * Time: 16:47
 */
class Helper_Popover {

	public static function add_to_attributes(&$attributes, $popover = null, $popover_placement = 'right', $popover_title = null) {
		if (Core_User::popovers_enabled()) {
			if (!empty($popover) && is_string($popover)) {
				if (!is_array($attributes)) {
					$attributes = array();
				}

				$attributes['rel'] = 'popover';
				$attributes['data-trigger'] = 'hover';
				$attributes['data-placement'] = $popover_placement;
				$attributes['data-content'] = htmlspecialchars($popover);
				if (!empty($popover_title) && is_string($popover_title)) {
					$attributes['data-title'] = htmlspecialchars($popover_title);
				}
			}
		}

		return;
	}

	public static function get_as_string($popover = null, $popover_placement = 'right', $popover_title = null) {
		$string = '';

		if (Core_User::popovers_enabled()) {
			if (!empty($popover) && is_string($popover)) {
				$string .= ' rel="popover" data-trigger="hover"';
				$string .= ' data-placement="' . $popover_placement . '"';
				$string .= ' data-content="' . htmlspecialchars($popover) . '"';

				if (!empty($popover_title) && is_string($popover_title)) {
					$string .= ' data-title="' . htmlspecialchars($popover_title) . '"';
				}
			}
		}

		return $string;
	}
} // End Helper_Popover
