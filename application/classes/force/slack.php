<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Slack
 * User: legion
 * Date: 18.02.17
 * Time: 20:21
 */
class Force_Slack {

	protected $web_hook_url = null;

	public function __construct($web_hook_url) {
		$this->web_hook_url = (string)$web_hook_url;
	}

	public static function factory($web_hook_url) {
		return new self($web_hook_url);
	}

	public function send_json($json) {
		//API Url
		$url = $this->web_hook_url;

		//Initiate cURL.
		$ch = curl_init($url);

		//Encode the array into JSON.
		$jsonDataEncoded = json_encode($json);

		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);

		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

		//Execute the request
		return curl_exec($ch);
	}

} // End Force_Slack
