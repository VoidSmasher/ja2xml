<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'       => 'Jelly',
	'hash_method'  => 'sha256',
	'hash_key'     => 'j&g/oi7asdfxzcvzszw2!@#$%^',
	'lifetime'     => 1209600,
	'session_type' => Session::$default,
	'session_key'  => 'auth_user',

	// Username/password combinations for the Auth File driver
	'users' => array(
		// 'admin' => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
	),

	'permissions' => array(
		'allow_login' => true,
		'allow_registration' => true,
		'allow_change_password' => true,
		'allow_change_profile' => true,
	),

	// Следующие роли будет запрещено удалять или редактировать в админке
	'fixed_roles' => array(
		'login',
		'admin',
	),
);
