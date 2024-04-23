<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Deferred_Action
 * User: CRUD Generator
 * Date: 12.06.16
 * Time: 06:24
 */
class Core_Deferred_Action extends Core_Common {

	const LOG_EVERYTHING = false;

	use Core_Common_Static;

	protected static $model_class = 'Model_Deferred_Action';
	protected $model_name = 'deferred_action';

	/*
	 * Количество попыток выполнения отложенного действия
	 */
	const LIMIT = 3;

	const ACTION_AUTH_REG_CONFIRM = 1;
	const ACTION_AUTH_REMIND = 2;
	const ACTION_AUTH_FORCE_REG = 3;
	const ACTION_SEND_USER_PASSWORD_CHANGE_EMAIL = 4;

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public function auto_send(Model_Deferred_Action $model) {
		$result = false;

		if (!$model->loaded() || empty($model->action)) {
			return $result;
		}
		$model->tries++;
		$model->updated_at = time();

		switch ($model->action) {
			case self::ACTION_AUTH_REG_CONFIRM:
				$result = $this->auth_reg_confirm($model);
				break;
			case self::ACTION_AUTH_REMIND:
				$result = $this->auth_remind($model);
				break;
			case self::ACTION_AUTH_FORCE_REG:
				$result = $this->auth_force_reg($model);
				break;
			case self::ACTION_SEND_USER_PASSWORD_CHANGE_EMAIL:
				$result = $this->send_user_password_change_email($model);
				break;
		}

		if (self::LOG_EVERYTHING) {
			Log::info_class(__CLASS__, __FUNCTION__, 'result: ' . (int)$result);
		}

		if ($result) {
			$model->executed_at = time();
		} elseif ($model->tries >= self::LIMIT) {
			$model->canceled_at = time();
		}

		try {
			$model->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		return $result;
	}

	public static function create_auth_reg_confirm($data) {
		$deferred_action = Jelly::factory('deferred_action');
		$deferred_action->action = self::ACTION_AUTH_REG_CONFIRM;
		$deferred_action->data = json_encode($data, JSON_UNESCAPED_UNICODE);

		try {
			$deferred_action->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		return $deferred_action->saved();
	}

	public function auth_reg_confirm(Model_Deferred_Action $model) {
		$result = false;
		$data = json_decode($model->data, true);

		$to = Arr::get($data, 'email');

		if (empty($to)) {
			Log::error_class(__CLASS__, __FUNCTION__, 'E-mail is empty');
			return false;
		}

		$subject = Arr::get($data, 'subject');

		$message = View::factory(EMAIL_VIEW . 'auth/registration_confirm');
		foreach ($data as $k => $v) {
			$message->set($k, $v);
		}

		try {
			$result = Helper_Mail::send($subject, $message, $to);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to send mail to: ' . var_export($to, true));
		}

		return $result;
	}

	public static function create_auth_remind($data) {
		$deferred_action = Jelly::factory('deferred_action');
		$deferred_action->action = self::ACTION_AUTH_REMIND;
		$deferred_action->data = json_encode($data, JSON_UNESCAPED_UNICODE);

		try {
			$deferred_action->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		return $deferred_action->saved();
	}

	public function auth_remind(Model_Deferred_Action $model) {
		$result = false;
		$data = json_decode($model->data, true);

		$to = Arr::get($data, 'email');

		if (empty($to)) {
			Log::error_class(__CLASS__, __FUNCTION__, 'E-mail is empty');
			return false;
		}

		$subject = Arr::get($data, 'subject');

		$message = View::factory(EMAIL_VIEW . 'auth/remind');
		foreach ($data as $k => $v) {
			$message->set($k, $v);
		}

		try {
			$result = Helper_Mail::send($subject, $message, $to);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to send E-mail to: ' . var_export($to, true));
		}

		return $result;
	}

	public static function create_auth_force_reg($user_email, $password, $user_name = null) {
		$deferred_action = Jelly::factory('deferred_action');
		$deferred_action->action = self::ACTION_AUTH_FORCE_REG;
		$deferred_action->data = json_encode(array(
			'user_email' => $user_email,
			'user_name' => $user_name,
			'password' => $password,
		));

		try {
			$deferred_action->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		if ($deferred_action->saved() && class_exists('GetResponse')) {
			try {
				$get_response = new GetResponse(false);
				$get_response->addContact(
					'pOl9g',
					$user_name,
					$user_email
				);
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		return $deferred_action->saved();
	}

	public function auth_force_reg(Model_Deferred_Action $model) {
		$result = false;
		$data = json_decode($model->data, true);

		$user_email = Arr::get($data, 'user_email');
		$user_name = Arr::get($data, 'user_name', '');
		$password = Arr::get($data, 'password');

		if (empty($user_email) && empty($password)) {
			return $result;
		}

		$subject = Force_Config::get_site_name() . ' :: ' . __('auth.title.registration');
		$message = View::factory(EMAIL_VIEW . 'auth/registration')
			->set('password', $password)
			->set('user_name', $user_name);

		try {
			$result = Helper_Mail::send($subject, $message, $user_email);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to send E-mail to: ' . $user_email);
		}

		return $result;
	}

	public static function create_send_user_password_change_email($data) {
		$deferred_action = Jelly::factory('deferred_action');
		$deferred_action->action = self::ACTION_SEND_USER_PASSWORD_CHANGE_EMAIL;
		$deferred_action->data = json_encode($data, JSON_UNESCAPED_UNICODE);

		try {
			$deferred_action->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		return $deferred_action->saved();
	}

	public function send_user_password_change_email(Model_Deferred_Action $model) {
		$result = false;
		$data = json_decode($model->data, true);

		$user_id = Arr::get($data, 'id');
		$user = Core_User::factory($user_id)->get_one();

		if (!$user->loaded()) {
			Log::error_class(__CLASS__, __FUNCTION__, 'Unable to load user with id: ' . var_export($user_id, true));
			return false;
		}

		$to = Arr::get($data, 'email');

		if (empty($to)) {
			Log::error_class(__CLASS__, __FUNCTION__, 'E-mail is empty');
			return false;
		}

		$subject = 'Изменение Вашего пароля на сайте';

		$message = View::factory(EMAIL_VIEW . 'auth/password_change')
			->bind('data', $data)
			->render();

		try {
			$result = Helper_Mail::send($subject, $message, $to);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to send E-mail to: ', var_export($to, true));
		}

		return $result;
	}

} // End Core_Deferred_Action
