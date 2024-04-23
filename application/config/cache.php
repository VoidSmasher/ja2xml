<?php defined('SYSPATH') or die('No direct script access.');
return array(

	'sqlite' => array(
		'driver' => 'sqlite',
		'default_expire' => 600,
		'database' => APPPATH . 'cache/kohana-cache.sql3',
		'schema' => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
	),

	'memcache' => array(
		'driver'             => 'memcache',
		'default_expire'     => 3600,
		'compression'        => FALSE,              // Use Zlib compression (can cause issues with integers)
		'servers'            => array(
			'local' => array(
				'host'             => 'localhost',  // Memcache Server
				'port'             => 11211,        // Memcache port number
				'persistent'       => FALSE,        // Persistent connection
				'weight'           => 1,
				'timeout'          => 1,
				'retry_interval'   => 15,
				'status'           => TRUE,
			),
		),
		'instant_death'      => TRUE,               // Take server offline immediately on first fail (no retry)
	),

	'memcachetag' => array(
		'driver' => 'memcachetag',
		'default_expire' => 3600,
		'compression' => FALSE,
		// Use Zlib compression   (can cause issues with integers)
		'servers' => array(
			array(
				'host' => 'localhost',
				// Memcache Server
				'port' => 11211,
				// Memcache port number
				'persistent' => FALSE,
				// Persistent connection
			),
		),
	),

	'file'    => array(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	),

	'cache_expire_times' => array(
		'unread_messages_count' => 360,
		'user_online' => 360,
		'player_config_xml' => 120,
		'galaforbs_users' => 120,
	),

);