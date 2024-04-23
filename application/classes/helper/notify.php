<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Notify
 * User: A. Stifanenkov
 * Date: 13.02.13
 * Time: 17:52
 */
class Helper_Notify {

	protected static $notifications = array();

	public static function init() {
		self::set(false);
	}

	public static function set($notifications, $label = null) {
		if (!empty($notifications)) {
			if (is_array($notifications)) {
				self::$notifications = $notifications;
			} elseif (is_string($notifications)) {
				self::$notifications = self::notify($notifications, $label);
			}
			return true;
		} else {
			self::$notifications = array();
			return false;
		}
	}

	public static function add($notification, $key = null, $label = null) {
		if (!empty($notification)) {
			if (is_array($notification)) {
				self::$notifications = self::$notifications + $notification;
			} else {
				if (!empty($key)) {
					$key = (string)$key;
					self::$notifications[$key] = self::notify($notification, $label);
				} else {
					self::$notifications[] = self::notify($notification, $label);
				}
			}
			return true;
		} else {
			return false;
		}
	}

	protected static function notify($text, $label = null) {
		$result = array(
			'text' => $text,
		);
		if (!empty($label)) {
			$result['label'] = $label;
		}
		return $result;
	}

	public static function parse_notify($notify, $key = null) {
		if (is_string($notify)) {
			$text = $notify;
		} elseif (is_array($notify) && array_key_exists('text', $notify)) {
			$text = $notify['text'];
			if (!is_null($key) && array_key_exists('label', $notify)) {
				$key = $notify['label'];
			}
		} else {
			$text = false;
		}
		if ($text) {
			$key = (!is_numeric($key) && is_string($key) && !empty($key)) ? $key . ': ' : '';
			$text = $key . $text;
		}
		return $text;
	}

	public static function get($notifications = null) {
		if (!is_null($notifications)) {
			self::set($notifications);
		}
		return (!empty(self::$notifications)) ? self::$notifications : false;
	}

	public static function get_by_key($key) {
		$notification = false;
		$notifications = self::get();
		if ($notifications) {
			if (array_key_exists($key, $notifications)) {
				$notification = self::parse_notify($notifications[$key]);
			}
		}
		return $notification;
	}

	public static function get_view($notifications = null) {
		$notifications = self::get($notifications);
		if ($notifications) {
			$view = View::factory(TEMPLATE_VIEW . 'common/chunks/notifications')
				->bind('notifications', $notifications);
		} else {
			$view = '';
		}
		return $view;
	}

	public static function no_notifications() {
		return empty(self::$notifications);
	}

	public static function has_notifications() {
		return !self::no_notifications();
	}

} // End Helper_Notify
