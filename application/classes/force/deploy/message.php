<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Deploy_Message
 * User: legion
 * Date: 12.07.16
 * Time: 0:01
 */
class Force_Deploy_Message {

	const DELAY_IN_SECONDS = 300;

	protected static $deploy_time = 0;
	protected static $controller = '';
	protected static $action = '';
	protected static $alarm_time = 0;
	protected static $alarm_text = '00:00:00';

	public static function before() {
		self::$controller = Request::current()->controller();
		self::$action = Request::current()->action();
		self::$deploy_time = Session::instance()->get('deploy', 0);
		if (self::$deploy_time > 0) {
			self::$alarm_time = self::$deploy_time + self::DELAY_IN_SECONDS;
			self::$alarm_text = date('00:i:s', self::$alarm_time - time());
			$path = self::$controller . '/' . self::$action;
			if (!in_array($path, array(
				'auth/redirect',
				'auth/logout',
			))
			) {
				if (!Helper_Auth::is_user_authorized()) {
					echo View::factory(TEMPLATE_VIEW . 'deploy/layout')
						->set('js_vars', self::js_vars(true))
						->render();
					exit(0);
				} elseif (time() > self::$alarm_time) {
					Request::current()->redirect('/auth/logout');
				}
			}
		}
	}

	public static function after() {
		Helper_Assets::add_before_js_vars(self::js_vars());
		Helper_Assets::add_scripts_in_footer('assets/common/js/redirect.js');
	}

	protected static function js_vars($show_deploy_page = false) {
		return array(
			'fc_dams' => array(
				'rc' => self::$controller,
				'ra' => self::$action,
				'sdp' => (integer)$show_deploy_page,
				'alarm_time' => self::$alarm_time,
				'alarm_text' => self::$alarm_text,
				'alarm_message1' => __('deploy.message1'),
				'alarm_message2' => __('deploy.message2'),
			),
		);
	}

} // End Force_Deploy_Message
