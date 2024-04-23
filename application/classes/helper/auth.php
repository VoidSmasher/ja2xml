<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Auth
 * User: legion
 * Date: 13.07.14
 * Time: 13:20
 */
class Helper_Auth {

	protected static $_auth = null;

	protected static $_user = null;
	protected static $_user_is_authorized = null;
	protected static $_no_user = FALSE;

	protected static $_permissions = array();
	protected static $_allow_permissions_by_default = true;

	const SESSION_REGISTRATION_CONFIRMATION = 'registration_confirmation';
	const SESSION_REGISTRATION_DONE = 'registration_done';
	const SESSION_CHANGE_PASSWORD_DONE = 'change_password_done';
	const SESSION_REMIND_PASSWORD_DONE = 'remind_password_done';
	const SESSION_REMIND_EXPIRED = 'remind_expired';
	const SESSION_PERSONAL_DATA_UPDATED = 'personal_data_updated';
	const SESSION_USER_IS_NOT_REGISTERED = 'user_is_not_registered';
	const SESSION_STORED_USER_EMAIL = 'stored_user_email';
	const SESSION_STORED_USER_LOGIN = 'stored_user_login';
	const REMIND_HASH_PREFIX = 'user_remind_';

	public static function no_user() {
		self::$_no_user = TRUE;
		return true;
	}

	public static function get_auth() {
		if (is_null(self::$_auth)) {
			self::$_auth = Auth::instance();
		}
		return self::$_auth;
	}

	/**
	 * @return Model_User
	 */
	public static function get_user() {
		if (is_null(self::$_user)) {
			if (self::$_no_user) {
				self::$_user = Core_User::factory()->create();
			} else {
				$auth = self::get_auth();
				if (Core_User::is_model($auth->get_user())) {
					self::$_user = Core_User::factory($auth->get_user()->id)->get_one();
				} else {
					self::$_user = Core_User::factory()->create();
				}
			}
		}
		return self::$_user;
	}

	public static function is_user_authorized() {
		if (self::$_no_user) {
			return false;
		}
		if (is_null(self::$_user_is_authorized)) {
			$auth = self::get_auth();
			$user = self::get_user();
			self::$_user_is_authorized = ($auth->logged_in() && (Core_User::is_model($user, true)));
		}
		return self::$_user_is_authorized;
	}

	/*
	 * PERMISSIONS
	 */

	public static function get_permission($permission) {
		if (self::$_no_user) {
			return false;
		}
		if (empty(self::$_permissions)) {
			self::$_permissions = Kohana::$config->load('auth.permissions');
		}
		if (array_key_exists($permission, self::$_permissions)) {
			return (boolean)self::$_permissions[$permission];
		}
		return self::$_allow_permissions_by_default;
	}

} // End Helper_Auth
