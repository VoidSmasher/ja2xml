<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 08.08.17
 * Time: 19:18
 */
return array(
	'upload' => array(
		'directory' => 'uploads/',
	),

	'example' => array(
		'dir' => '',
		'accept' => array(
			'text/plain',
			'application/vnd.ms-office',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		),
		// Значение будет прогнано через Num::bytes()
		'max_file_size' => '2M',
	),
);
