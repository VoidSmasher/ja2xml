<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	/**
	 * The following options are available for MySQL:
	 * string   hostname     server hostname, or socket
	 * string   database     database name
	 * string   username     database username
	 * string   password     database password
	 * boolean  persistent   use persistent connections?
	 * array    variables    system variables as "key => value" pairs
	 * Ports and sockets may be appended to the hostname.
	 * For localhost hostname use '127.0.0.1' instead of 'localhost'.
	 */
	'default' => array(
		'type' => 'mysqli',
		'connection' => array(
			'hostname' => '127.0.0.1',
			'database' => 'database',
			'username' => 'username',
			'password' => 'password',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset' => 'utf8',
		'caching' => FALSE,
		'profiling' => TRUE,
	),
);