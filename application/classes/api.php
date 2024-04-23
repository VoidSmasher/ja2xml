<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: API
 * User: legion
 * Date: 15.10.14
 * Time: 19:55
 */
class API {

	protected $_message = '';
	protected $_code = 200;
	protected $_data = null;
	protected $_json_options = JSON_UNESCAPED_UNICODE;
	protected static $do_not_log_next_send = false;

	public function __construct($code = 200) {
		$this->_code = $code;
		return $this;
	}

	public static function factory($code = 200) {
		return new self($code);
	}

	/*
	 * SET
	 */

	public function message($message) {
		$this->_message = (string)$message;
		return $this;
	}

	public function code($code) {
		$this->_code = (int)$code;
		return $this;
	}

	public function data($data) {
		$this->_data = $data;
		return $this;
	}

	/**
	 * Принимает в себя параметр $options соответствующий второму параметру json_encode,
	 * но с предустановкой в JSON_UNESCAPED_UNICODE;
	 * json_encode по умолчанию для параметра $options имеет значение 0
	 *
	 * @param int $options
	 *
	 * @return $this
	 */
	public function json_options($options = JSON_UNESCAPED_UNICODE) {
		$this->_json_options = $options;
		return $this;
	}

	/*
	 * ANSWER
	 */

	public function send() {
		if (empty($this->_message)) {
			if (array_key_exists($this->_code, Response::$messages)) {
				$this->_message = Response::$messages[$this->_code];
			} else {
				$this->_message = 'Validation error';
			}
		}

		header('HTTP/1.0 ' . $this->_code . ' ' . $this->_message);

		$result = array(
			'code' => $this->_code,
			'message' => $this->_message,
			'data' => $this->_data,
		);

		if ($this->_code != 200) {
			$result['errors'] = Helper_Error::get();
		}

		API::send_json($result, $this->_json_options);
	}

	/*
	 * STATIC LIBRARY
	 */

	public static function do_not_log_next_send() {
		self::$do_not_log_next_send = true;
	}

	public static function send_json($data, $json_options = JSON_UNESCAPED_UNICODE) {
		$encoded = json_encode($data, $json_options);

		if (!self::$do_not_log_next_send && Helper_Error::can_log_debug_messages()) {
			Log::instance()->add(Log::DEBUG, 'DEBUG API ANSWER :: ' . $encoded)->write();
		}

		self::$do_not_log_next_send = false;

		echo $encoded;
		exit(0);
	}

} // End API
