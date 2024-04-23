<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Counter_Yandex
 * User: ener
 * Date: 23.12.13
 * Time: 11:45
 */
class Controller_Counter_Yandex extends Controller {

	public function action_index() {
		echo '<html>
		<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>
		<body>Verification: ' . Force_Config::instance()->get_param('yandex_code') . '</body>
		</html>';
	}

} // End Controller_Counter_Yandex