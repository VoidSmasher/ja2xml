<?php defined('SYSPATH') or die('Access denied.');

/**
 * Helper_Mail
 * @author Verstov Andrey <andrey@verstov.ru>
 * @copyright (c) 2011 Verstov.ru <http://www.verstov.ru>
 */
class Helper_Mail {

	public static function straight_send($subject, $message, $to, $from = '') {
		if (empty($from)) {
			$from = Kohana::$config->load('email.from');
		}

		$headers = "From: " . Force_Config::get_site_name() . " <" . $from . ">\r\n" . "Reply-To: " . $from . "\r\n" . "X-Mailer: PHP/" . Force_Config::get_domain() . "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=utf-8\r\n";

		return mail($to, $subject, $message, $headers);
	}

	public static function send($title, $message, $to, $template = true, $attach = null) {
		$result = false;
		if ($template) {
			$message = self::template($title, $message);
		}

		if (is_string($to)) {
			$to = explode(',', $to);
		}

		if (!is_array($to)) {
			$to = array();
		}

		foreach ($to as $_key => $_value) {
			$to[$_key] = trim($_value);
		}

		$mailer = Email::connect();

		try {
			$letter = Swift_Message::newInstance()
				->setSubject($title)
				->setBody($message, 'text/html', 'utf-8')
				->setFrom(Kohana::$config->load('email.from'))
				->setTo($to);

			if (!is_null($attach)) {
				$letter->attach($attach);
			}
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		try {
			$result = $mailer->send($letter);
		} catch (Exception $e) {
			if (is_array($to)) {
				$to = implode(',', $to);
			}
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to send email to: ' . $to);
		}

		return $result;
	}

	public static function template($title, $body) {
		return View::factory(TEMPLATE_VIEW . 'email/layout')
			->set('title', $title)
			->set('content', $body);
	}

} // End Helper_Mail