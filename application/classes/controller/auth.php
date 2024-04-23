<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Auth
 * User: legion
 * Date: 17.01.14
 * Time: 5:30
 */
class Controller_Auth extends Controller_Bootstrap_Template {

	protected $captcha_on_login = false;
	protected $captcha_on_remind = true;
	protected $display_user_not_found_message = true;

	public function before() {
		parent::before();
//		if (!$this->user_is_authorized && in_array($this->request->action(), array(
//					'change',
//				))
//		) {
//			Force_URL::save_current_url_as_back_url();
//			$this->request->redirect(Force_URL::current()->action('login')->get_url());
//		}

		$this->template->fluid = false;

		switch ($this->request->action()) {
			case 'login':
				if (!Helper_Auth::get_permission('allow_login')) {
					throw new HTTP_Exception_403;
				}
				break;
			case 'registration':
			case 'registration_confirm':
				if (!Helper_Auth::get_permission('allow_registration')) {
					throw new HTTP_Exception_403;
				}
				break;
			case 'change':
				if (!Helper_Auth::get_permission('allow_change_password')) {
					throw new HTTP_Exception_403;
				}
				break;
			case 'logout':
			case 'remind':
			case 'reset':
				break;
		}

		Force_URL::catch_back_url();
	}

	public function action_index() {
		if ($this->user_is_authorized) {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		} else {
			$this->action_login();
		}
	}

	/*
	 * LOGIN & LOGOUT
	 */

	public function action_login() {
		if ($this->user_is_authorized) {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		}

		if (Form::is_post()) {
			if ($this->captcha_on_login && !Captcha::valid(Arr::get($_POST, 'captcha', ''))) {
				Helper_Error::add(__('auth.error.captcha_failed'));
			}

			if (Helper_Error::no_errors()) {
				$remember = false;
				$login = Arr::get($_POST, 'login');
				$password = Arr::get($_POST, 'password');

				$user = Jelly::factory('user')->get_user($login);

				if ($this->auth->login($user, $password, $remember)) {
					$back_url = Force_URL::get_back_url();

					if (!empty($back_url) and !preg_match('/.json/', $back_url)) {
						$this->request->redirect($back_url, 302);
					} else {
						$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
					}
				} else {
					/*
					 * Проверяем наличие флага незавершенной регистрации - этот способ быстрее следующей проверки
					 */
					$session = Session::instance();
					$registration_confirmation = $session->get(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION);
					if (!empty($registration_confirmation)) {
						$this->request->redirect(Force_URL::current()->action('registration_confirm')->get_url(), 302);
					}

					/*
					 * Проверяем на наличие роли Login.
					 * Если такой роли у пользователя нет, то это означает, что он ещё не подтвердил свою регистрацию
					 * и необходимо ему об этом ещё раз напомнить.
					 * Такая ситуация возможна, если сессия, в которой была сделана регистрация уже отмерла
					 * либо пользователь пытается войти с другого устройства.
					 */
					if ($user->loaded() && !$user->has('roles', Jelly::factory('role')->get_role('login'))
					) {
						$session->set(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION, $user->id);
						$this->request->redirect(Force_URL::current()->action('registration_confirm')->get_url(), 302);
					}

					/*
					 * Если вышестоящие проверки не отработали, выдаём стандартную ошибку авторизации
					 */
					Helper_Error::add(__('auth.error'));
				}
			}
		}

		$this->template->page_title = __('auth.title.login'); // заголовок страницы

		$captcha = ($this->captcha_on_login) ? Captcha::instance()->render(TRUE) : null;
		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/login')
			->set('captcha', $captcha);
	}

	public function action_logout() {
		// Log the user out if he is logged in
		if ($this->auth->logged_in()) {
			$this->auth->logout(true);
			Helper_Image::remove_current_images_from_session();
		}
		// Redirect to the index page
		$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
	}

	/*
	 * REGISTRATION
	 */

	public function action_registration() {
		$fields = array(
			'username',
			'email',
			'password',
			'password_confirm',
			'surname',
			'name',
		);
		if (Form::is_post()) {
			$user_data = Arr::extract($_POST, $fields);

			foreach ($user_data as $field => $value) {
				$user_data[$field] = trim($value);
			}

			$agree = (boolean)Arr::get($_POST, 'agree', false);

			if (!Captcha::valid(Arr::get($_POST, 'captcha', ''))) {
				Helper_Error::add(__('auth.error.captcha_failed'), 'captcha');
			}

			if (!$agree) {
				Helper_Error::add(__('auth.error.agree_to_the_terms'), 'terms');
			}

			if ($user_data['password'] != $user_data['password_confirm']) {
				Helper_Error::add(__('auth.error.passwords_are_not_equal'), 'password');
			}

			if (Helper_Error::no_errors()) {
				$user = Jelly::factory('user');

				try {
					$user->set($user_data)->save();
				} catch (Jelly_Validation_Exception $e) {
					Helper_Error::add_from_jelly($user, $e->errors());
				}

				if (Helper_Error::no_errors() && $user->saved()) {
					/*
					 * Ставим флаг незавершенной регистрации и указываем id пользователя для получения данных
					 */
					Session::instance()->set(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION, $user->id);
					$this->request->redirect(Force_URL::current()->action('registration_confirm')->get_url(), 302);
				}
			}
		} else {
			foreach ($fields as $field) {
				$user_data[$field] = null;
				switch ($field) {
					case 'username':
						$stored_login = Session::instance()->get(Helper_Auth::SESSION_STORED_USER_LOGIN);
						Session::instance()->delete(Helper_Auth::SESSION_STORED_USER_LOGIN);
						if (!empty($stored_login)) {
							$user_data[$field] = $stored_login;
						}
						break;
					case 'email':
						$stored_email = Session::instance()->get(Helper_Auth::SESSION_STORED_USER_EMAIL);
						Session::instance()->delete(Helper_Auth::SESSION_STORED_USER_EMAIL);
						if (!empty($stored_email)) {
							$user_data[$field] = $stored_email;
						}
						break;
				}
			}
		}

		$this->template->page_title = __('auth.title.registration');

//		$eula = Core_Page::factory()->alias('eula')->get_one();
//		if ($eula->loaded()) {
//			$eula = $eula->as_array();
//		} else {
		$domain = Force_Config::get_domain();
		$eula = array(
			'title' => __('auth.title.eula'),
			'content' => nl2br("Настоящее Соглашение определяет условия использования Пользователями материалов и сервисов сайта " . $domain . " (далее — «Сайт»).

				1.Общие условия
				1.1. Использование материалов и сервисов Сайта регулируется нормами действующего законодательства Российской Федерации.
				1.2. Настоящее Соглашение является публичной офертой. Получая доступ к материалам Сайта Пользователь считается присоединившимся к настоящему Соглашению.
				1.3. Администрация Сайта вправе в любое время в одностороннем порядке изменять условия настоящего Соглашения. Такие изменения вступают в силу по истечении 3 (Трех) дней с момента размещения новой версии Соглашения на сайте. При несогласии Пользователя с внесенными изменениями он обязан отказаться от доступа к Сайту, прекратить использование материалов и сервисов Сайта.

				2. Обязательства Пользователя
				2.1. Пользователь соглашается не предпринимать действий, которые могут рассматриваться как нарушающие российское законодательство или нормы международного права, в том числе в сфере интеллектуальной собственности, авторских и/или смежных правах, а также любых действий, которые приводят или могут привести к нарушению нормальной работы Сайта и сервисов Сайта.
				2.2. Использование материалов Сайта без согласия правообладателей не допускается (статья 1270 Г.К РФ). Для правомерного использования материалов Сайта необходимо заключение лицензионных договоров (получение лицензий) от Правообладателей.
				2.3. При цитировании материалов Сайта, включая охраняемые авторские произведения, ссылка на Сайт обязательна (подпункт 1 пункта 1 статьи 1274 Г.К РФ).
				2.4. Комментарии и иные записи Пользователя на Сайте не должны вступать в противоречие с требованиями законодательства Российской Федерации и общепринятых норм морали и нравственности.
				2.5. Пользователь предупрежден о том, что Администрация Сайта не несет ответственности за посещение и использование им внешних ресурсов, ссылки на которые могут содержаться на сайте.
				2.6. Пользователь согласен с тем, что Администрация Сайта не несет ответственности и не имеет прямых или косвенных обязательств перед Пользователем в связи с любыми возможными или возникшими потерями или убытками, связанными с любым содержанием Сайта, регистрацией авторских прав и сведениями о такой регистрации, товарами или услугами, доступными на или полученными через внешние сайты или ресурсы либо иные контакты Пользователя, в которые он вступил, используя размещенную на Сайте информацию или ссылки на внешние ресурсы.
				2.7. Пользователь принимает положение о том, что все материалы и сервисы Сайта или любая их часть могут сопровождаться рекламой. Пользователь согласен с тем, что Администрация Сайта не несет какой-либо ответственности и не имеет каких-либо обязательств в связи с такой рекламой.

				3. Прочие условия
				3.1. Все возможные споры, вытекающие из настоящего Соглашения или связанные с ним, подлежат разрешению в соответствии с действующим законодательством Российской Федерации.
				3.2. Ничто в Соглашении не может пониматься как установление между Пользователем и Администрации Сайта агентских отношений, отношений товарищества, отношений по совместной деятельности, отношений личного найма, либо каких-то иных отношений, прямо не предусмотренных Соглашением.
				3.3. Признание судом какого-либо положения Соглашения недействительным или не подлежащим принудительному исполнению не влечет недействительности иных положений Соглашения.
				3.4. Бездействие со стороны Администрации Сайта в случае нарушения кем-либо из Пользователей положений Соглашения не лишает Администрацию Сайта права предпринять позднее соответствующие действия в защиту своих интересов и защиту авторских прав на охраняемые в соответствии с законодательством материалы Сайта.
				Пользователь подтверждает, что ознакомлен со всеми пунктами настоящего Соглашения и безусловно принимает их."),
		);
//		}

		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/registration')
			->bind('user_data', $user_data)
			->bind('eula', $eula)
			->set('captcha', Captcha::instance()->render(TRUE));
	}

	public function action_registration_confirm() {
		$session = Session::instance();

		$hash = $this->request->param('id');
		if (!empty($hash) && $this->_registration_confirm_by_hash($hash)) {
			$session->delete(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION);
			$this->_throw_registration_done();
		}

		$user_id = $session->get(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION);
		$user = Core_User::factory($user_id)->get_one();

		/*
		 * Если пользователя нет, значит делать ему тут нечего
		 */
		if (!Core_User::is_loaded($user)) {
			$session->delete(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION);
			$this->request->redirect(Force_URL::current()->action('registration')->get_url(), 302);
		}

		/*
		 * Если роль login была проставлена через админку, то возможна ситуация
		 * когда у пользователя есть и hash и роль login.
		 * В любом случае, наличие роли login однозначно определяет завершённость регистрации.
		 */
		$is_confirmed = $user->has_role('login');
		if ($is_confirmed) {
			$session->delete(Helper_Auth::SESSION_REGISTRATION_CONFIRMATION);
			$this->_throw_registration_done();
		} else {
			if (is_null($user->hash)) {
				try {
					$user->set('hash', Core_User::generate_new_md5_hash())->save();
				} catch (Jelly_Validation_Exception $e) {
					Helper_Error::add_from_jelly($user, $e->errors());
				}
			}
		}

		if (Helper_Error::no_errors()) {
			$subject = Force_Config::get_site_name() . ' :: ' . __('auth.title.registration');

			$confirm_url = Force_URL::get_current_host() . Force_URL::current()
					->action('registration_confirm')
					->route_param('id', $user->hash);

			$message = View::factory(EMAIL_VIEW . 'auth/registration_confirm')
				->set('user_name', $user->get_name())
				->bind('confirm_url', $confirm_url);

			Helper_Mail::send($subject, $message, $user->email);
		}

		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/registration_confirm');
	}

	protected function _registration_confirm_by_hash($hash) {
		$user = Core_User::factory()->hash($hash)->get_builder()->limit(1)->select();

		if (!Core_User::is_model($user)) {
			return false;
		}

		$user->set('hash', null);
		$user->add('roles', Jelly::factory('role')->get_role('login'));

		try {
			$user->save();
		} catch (Jelly_Validation_Exception $e) {
			Helper_Error::add_from_jelly($user, $e->errors());
		}

		return $user->saved();
	}

	/*
	 * RESTORE PASSWORD
	 */

	public function action_remind() {
		/*
		 * Если пользователь уже авторизован, то ему надлежит воспользоваться механизмом смены пароля
		 */
		if ($this->user_is_authorized) {
			$this->request->redirect(Force_URL::current()->action('change')->get_url(), 302);
		}

		if (Form::is_post()) {
			if ($this->captcha_on_remind && !Captcha::valid(Arr::get($_POST, 'captcha', ''))) {
				Helper_Error::add(__('auth.error.captcha_failed'));
			}

			$login = trim(Arr::get($_POST, 'login'));
			if (Helper_Error::no_errors() && !empty($login)) {
				$user = Core_User::factory()->login($login)->get_builder()->limit(1)->select();
				if (Core_User::is_model($user, true)) {

					$hash = Core_User::generate_new_md5_hash();
					Cache::instance()->set(Helper_Auth::REMIND_HASH_PREFIX . $hash, $user->id, Date::DAY);

					$reset_url = Force_URL::get_current_host() . Force_URL::current()
							->action('reset')
							->route_param('id', $hash);

					$subject = Force_Config::get_site_name() . ' :: ' . __('auth.title.remind');
					$message = View::factory(EMAIL_VIEW . 'auth/remind')->set('user_name', $user->get_name())
						->bind('reset_url', $reset_url);
					Helper_Mail::send($subject, $message, $user->email);
				} else {
					Session::instance()->set(Helper_Auth::SESSION_USER_IS_NOT_REGISTERED, true);
					if (Valid::email($login)) {
						Session::instance()->set(Helper_Auth::SESSION_STORED_USER_EMAIL, $login);
					} else {
						Session::instance()->set(Helper_Auth::SESSION_STORED_USER_LOGIN, $login);
					}
					$this->request->redirect(Force_URL::current()->action('unregistered')->get_url(), 302);
				}
				if (Helper_Error::no_errors()) {
					$this->_throw_reminded();
				}
			}
		}
		$this->template->page_title = __('auth.title.remind');

		$captcha = ($this->captcha_on_remind) ? Captcha::instance()->render(TRUE) : null;
		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/remind')
			->bind('captcha', $captcha);
	}

	public function action_reset() {

		$hash = $this->request->param('id');
		if (empty($hash)) {
			throw new HTTP_Exception_404;
		}

		$cache = Cache::instance();
		if (!$user_id = $cache->get(Helper_Auth::REMIND_HASH_PREFIX . $hash, false)
		) {
			$this->_throw_remind_expired();
		}

		if (Form::is_post()) {
			$user = Core_User::factory($user_id)->get_one();
			if (!$user->loaded()) {
				$this->_throw_remind_expired();
			}

			$password = Arr::get($_POST, 'password');
			$password_confirm = Arr::get($_POST, 'password_confirm');
			if ($password != $password_confirm) {
				Helper_Error::add(__('auth.error.passwords_are_not_equal'));
			}
			if (Helper_Error::no_errors()) {
				$user->password = $password;
				try {
					$user = $user->save();
				} catch (Jelly_Validation_Exception $e) {
					Helper_Error::add_from_jelly($user, $e->errors());
				}
			}
			if (Helper_Error::no_errors()) {
				$cache->delete(Helper_Auth::REMIND_HASH_PREFIX . $hash);
				$this->auth->force_login($user);
				$this->_throw_password_changed();
			}

		} else {
			if (!Core_User::factory($user_id)->is_exist()) {
				$this->_throw_remind_expired();
			}
		}

		$this->template->page_title = __('auth.title.remind');
		$captcha = ($this->captcha_on_remind) ? Captcha::instance()->render(TRUE) : null;
		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/new_password')
			->set('captcha', $captcha);
	}

	/*
	 * RESULTS
	 */

	protected function _throw_registration_done() {
		Session::instance()->set(Helper_Auth::SESSION_REGISTRATION_DONE, true);
		$this->request->redirect(Force_URL::current()->action('registration_done')->get_url(), 302);
	}

	public function action_registration_done() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_REGISTRATION_DONE);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_REGISTRATION_DONE);
			$this->_show_result(__('auth.title.registration'), __('auth.result.registration_done'));
		} else {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		}
	}

	protected function _throw_reminded() {
		Session::instance()->set(Helper_Auth::SESSION_REMIND_PASSWORD_DONE, true);
		$this->request->redirect(Force_URL::current()->action('reminded')->get_url(), 302);
	}

	public function action_reminded() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_REMIND_PASSWORD_DONE);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_REMIND_PASSWORD_DONE);
			$this->_show_result(__('auth.title.remind'), __('auth.result.change_password_mail_sent'));
		} else {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		}
	}

	protected function _throw_password_changed() {
		Session::instance()->set(Helper_Auth::SESSION_CHANGE_PASSWORD_DONE, true);
		$this->request->redirect(Force_URL::current()->action('changed')->get_url(), 302);
	}

	public function action_changed() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_CHANGE_PASSWORD_DONE);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_CHANGE_PASSWORD_DONE);
			$this->_show_result(__('auth.title.password_changed'), __('auth.result.password_changed'));
		} else {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		}
	}

	public function action_unregistered() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_USER_IS_NOT_REGISTERED);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_USER_IS_NOT_REGISTERED);
			$this->_show_result(__('auth.title.unregistered'), __('auth.result.unregistered'), Force_Button::factory(__('auth.button.register'))
				->link(Force_URL::current()->action('registration')->get_url())->btn_primary());
		} else {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		}
	}

	protected function _throw_remind_expired() {
		Session::instance()->set(Helper_Auth::SESSION_REMIND_EXPIRED, true);
		$this->request->redirect(Force_URL::current()->action('remind_expired')->get_url(), 302);

	}

	public function action_remind_expired() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_REMIND_EXPIRED);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_REMIND_EXPIRED);
			$this->_show_result(__('auth.title.remind'), __('auth.result.hash_expired'));
		} else {
			$this->request->redirect(Force_URL::factory('default')->get_url(), 302);
		}
	}

	/*
	 * RESULTS GENERATOR
	 */

	protected function _show_result($title, $message, $btn_link = null, $btn_caption = null) {
		if ($btn_link instanceof Force_Button) {
			$button = $btn_link->render();
		} else {
			if (is_null($btn_link)) {
				$btn_link = Force_URL::factory('default')->get_url();
			}
			if (is_null($btn_caption)) {
				$btn_caption = __('common.back_to_main');
			}
			$button = Force_Button::factory($btn_caption)->link($btn_link)->render();
		}
		$this->template->page_title = $title;
		$this->template->content = View::factory(CONTROLLER_VIEW . 'auth/result')
			->bind('title', $title)
			->bind('message', $message)
			->bind('button', $button);
	}

	/*
	 * REDIRECT JS
	 */

	public function action_json_redirect() {
		$controller = Arr::get($_GET, 'rc');
		$action = Arr::get($_GET, 'ra');
		$show_deploy_page = (boolean)Arr::get($_GET, 'sdp', false);

		$deploy = Session::instance()->get('deploy', 0);
		$alarm_time = 0;

		if ($deploy > 0) {
			$alarm_time = $deploy + 300;
			if (!Helper_Auth::is_user_authorized()) {
				API::do_not_log_next_send();
				API::send_json(array(
					'alarm_time' => $alarm_time,
					'location' => Force_URL::factory('default')->get_url(),
				));
			} elseif (time() > $alarm_time) {
				$this->request->redirect(Force_URL::current()->action('logout')->get_url(), 302);
			}
		} elseif ($show_deploy_page) {
			API::do_not_log_next_send();
			API::send_json(array(
				'alarm_time' => $alarm_time,
				'location' => Force_URL::factory('default')->get_url(),
			));
		}

		$this->_custom_redirect($controller, $action);

		API::do_not_log_next_send();
		API::send_json(array(
			'alarm_time' => $alarm_time,
			'location' => false,
		));
	}

	protected function _custom_redirect($controller, $action) {

	}

} // End Controller_Auth