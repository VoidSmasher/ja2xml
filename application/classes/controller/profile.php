<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Profile
 * User: legion
 * Date: 04.09.14
 * Time: 18:36
 */
class Controller_Profile extends Controller_Profile_Template {

	public function before() {
		parent::before();

		$this->template->fluid = false;
		$this->disable_default_error_output = true;

		switch ($this->request->action()) {
			case 'index':
				if (!Helper_Auth::get_permission('allow_change_profile')) {
					throw new HTTP_Exception_403;
				}
				break;
			case 'password':
				if (!Helper_Auth::get_permission('allow_change_password')) {
					throw new HTTP_Exception_403;
				}
				break;
		}
	}

	public function action_index() {
		/*
		 * @TODO перенести сюда
		 * change_email (закодить)
		 */
		$user_data = Helper_Auth::get_user()->as_array();

		if (Form::is_post()) {
			$user_data = Arr::extract($_POST, array(
				'surname',
				'name',
				'patronymic',
				'username',
				'email',
			));
			foreach ($user_data as $_key => $_value) {
				$user_data[$_key] = trim($_value);
			}
			$this->user->set($user_data);
			if (Helper_Error::no_errors()) {
				try {
					$this->user = $this->user->save();
				} catch (Jelly_Validation_Exception $e) {
					Helper_Error::add_from_jelly($this->user, $e->errors());
				}
			}
			if (Helper_Error::no_errors() && $this->user->saved()) {
				Session::instance()->set(Helper_Auth::SESSION_PERSONAL_DATA_UPDATED, true);
				$this->request->redirect('/profile/updated');
			}
		}

		if (empty($user_data['surname'])) {
			Helper_Error::add(__('jelly.error.not_empty'), 'surname');
		}

		if (empty($user_data['name'])) {
			Helper_Error::add(__('jelly.error.not_empty'), 'name');
		}

		$this->template->content = View::factory(CONTROLLER_VIEW . 'profile/index')->bind('user_data', $user_data);
	}

	/*
	 * CHANGE PASSWORD
	 */

	public function action_password() {
		/*
		 * Если пользователь не авторизован, то ему надлежит воспользоваться механизмом восстановления пароля
		 */
		if (!$this->user_is_authorized) {
			$this->request->redirect('auth/remind');
		}

		if (Form::is_post()) {
			$password_old = Arr::get($_POST, 'password_old');
			if ($this->auth->hash($password_old) != $this->user->password) {
				Helper_Error::add(__('auth.error.old_password_mismatch'));
			}
			$password = Arr::get($_POST, 'password');
			$password_confirm = Arr::get($_POST, 'password_confirm');
			if ($password != $password_confirm) {
				Helper_Error::add(__('auth.error.passwords_are_not_equal'));
			}
			if (Helper_Error::no_errors()) {
				$this->user->password = $password;
				try {
					$this->user = $this->user->save();
				} catch (Jelly_Validation_Exception $e) {
					Helper_Error::add_from_jelly($this->user, $e->errors());
				}
			}
			if (Helper_Error::no_errors()) {
				Session::instance()->set(Helper_Auth::SESSION_CHANGE_PASSWORD_DONE, true);
				$this->request->redirect('/profile/password_changed');
			}
		}
		$this->template->page_title = __('auth.title.change_password');
		$this->template->content = View::factory(CONTROLLER_VIEW . '/profile/change_password');
	}

	/*
	 * RESULTS
	 */

	public function action_updated() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_PERSONAL_DATA_UPDATED);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_PERSONAL_DATA_UPDATED);
			$this->_show_result(__('auth.title.personal_data_updated'), __('auth.result.personal_data_updated'));
		} else {
			$this->request->redirect('/profile');
		}
	}

	public function action_password_changed() {
		$session = Session::instance();
		$done = $session->get(Helper_Auth::SESSION_CHANGE_PASSWORD_DONE);
		if ($done === true) {
			$session->delete(Helper_Auth::SESSION_CHANGE_PASSWORD_DONE);
			$this->_show_result(__('auth.title.password_changed'), __('auth.result.password_changed'));
		} else {
			$this->request->redirect('/profile');
		}
	}

} // End Controller_Profile