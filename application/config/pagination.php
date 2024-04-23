<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 02.07.12
 * Time: 19:29
 */
return array(
	'public' => array(
		'channels' => array(
			'users' => 17,
		),
		'messages' => array(
			'per_page' => 10,
			'contacts_per_page' => 15,
		),
	),
	'admin' => array(
		'items_per_page' => array(
			'variants' => array(
				10,
				20,
				50,
				100,
				500,
			),
			'default' => 20,
		),
		'items_in_line' => 9,
	),
);
