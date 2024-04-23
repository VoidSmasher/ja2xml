<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Test_Public
 * User: legion
 * Date: 23.05.13
 * Time: 13:14
 */
class Controller_Test_Public extends Controller_Test_Template {

	public function action_index() {
		$this->_index_menu('Controller_Test_Public', '/test/public/');
	}

	/*
	 * PUBLIC TESTS
	 * Все нежиеследующие тесты будут доступны любому смертному
	 * и даже не зарегестированному пользователю ресурса
	 */

//	public function action_captcha() {
//		$captcha = Captcha::instance();
//		Helper_Assets::add_scripts('js/jquery.form.js');
//		Helper_Assets::add_scripts('assets/test/js/captcha.js');
//		$this->template->content = View::factory(CONTROLLER_VIEW . 'test/captcha')
//			->bind('captcha', $captcha);
//	}
//
//	public function action_json_captcha() {
//		$result = false;
//		if (Form::is_post()) {
//			$captcha_input = Arr::get($_POST, 'captcha');
//			$result = (Captcha::valid($captcha_input));
//		}
//		echo json_encode($result);
//	}

//	public function action_mail() {
//		echo View::factory(EMAIL_VIEW . 'auth/confirm')
//			->set('user_name', 'Pupkin Uasia')
//			->set('confirm_url', 'greencow.pro/super/puper/mega/url');
//		echo View::factory(EMAIL_VIEW . 'auth/registration_confirm')
//			->set('user_name', 'Pupkin Uasia')
//			->set('confirm_url', 'greencow.pro/super/puper/mega/url');
//		echo View::factory(EMAIL_VIEW . 'auth/remind')
//			->set('user_name', 'Pupkin Uasia')
//			->set('reset_url', 'greencow.pro/super/puper/mega/url');
//		exit(0);
//	}

} // End Controller_Test_Public