<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Api_Core
 * User: legion
 * Date: 19.06.12
 * Time: 20:12
 */
class Core_Api_Core {

	public static function get_json($function_path, $params = '', $method = 'POST') {
		if (is_array($params)) {
			$params_arr = array();
			foreach ($params as $key => $value) {
				if (!empty($value)) {
					$params_arr[] = $key . '=' . $value;
				}
			}
			$params = implode('&', $params_arr);
		}

		try {
			if (mb_strtolower($method) == 'get') {
				$json = file_get_contents($function_path . '?' . $params);
			} else {
				$context = stream_context_create(array(
					'http' => array(
						'method' => 'POST',
						'header' => array(
							'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
						),
						'content' => $params,
					),
				));

				$json = file_get_contents(
					$function_path,
					false,
					$context
				);
			}
		} catch (Exception $e) {
			$json = '';
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		return $json;
	}

	public static function parse_answer($json_answer) {
		return json_decode($json_answer);
	}

} // End Core_Api_Core
