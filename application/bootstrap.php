<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH . 'classes/kohana/core' . EXT;

if (is_file(APPPATH . 'classes/kohana' . EXT)) {
	// Application extends the core
	require APPPATH . 'classes/kohana' . EXT;
} else {
	// Load empty core extension
	require SYSPATH . 'classes/kohana' . EXT;
}

/**
 * Paths
 */
define('TEMPLATE_VIEW', 'template/');
define('CONTROLLER_VIEW', 'controller/');
define('CONTROLLER_ADMIN_VIEW', CONTROLLER_VIEW . 'admin/');
define('HELPER_VIEW', 'helper/');
define('FORCE_VIEW', 'force/');
define('EMAIL_VIEW', 'email/');

define('DATA_JSON', 'json');
define('DATA_HTML', 'html');
define('DATA_XML', 'xml');
define('DATA_JS', 'js');

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Moscow');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'ru_RU.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array(
	'Kohana',
	'auto_load',
));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set Kohana::$environment if a 'HTTP_X_KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
Kohana::$environment = Kohana::PRODUCTION;

if (file_exists(APPPATH . 'environment.php')) {
	include APPPATH . 'environment.php';
} else if (array_key_exists('HTTP_X_KOHANA_ENV', $_SERVER) && !empty($_SERVER['HTTP_X_KOHANA_ENV'])) {
	Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['HTTP_X_KOHANA_ENV']));
}

/*
 * Warning
 * This will enable DEVELOPMENT environment in any other environment by GET request
 * Disable this code if you don't need any testing on PRODUCTION server
 */
if (Arr::get($_GET, 'debug', null) == 'jMGkv-aX-qk61j,kjqGIq1-k2!6y-i-Db7W') {
	Kohana::$environment = Kohana::DEVELOPMENT;
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url' => '/',
	'index_file' => '',
	'caching' => true,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// Caching with multiple backends
	'cache' => MODPATH . 'cache',
	// Database mysqli driver
	'mysqli' => MODPATH . 'mysqli',
	// Database access
	'database' => MODPATH . 'database',
	// Object Relationship Mapping
	'jelly' => MODPATH . 'jelly',
	// Object Relationship Mapping
	'jelly-auth' => MODPATH . 'jelly-auth',
	// Basic authentication
	'auth' => MODPATH . 'auth',
	// Image manipulation
	'image' => MODPATH . 'image',
	// Email sender
	'email' => MODPATH . 'email',
	// Pagination
	'pagination' => MODPATH . 'pagination',
	// Captcha
	'captcha' => MODPATH . 'captcha',
	// PHP Excel
	'phpexcel' => MODPATH . 'phpexcel',
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

/**
 * Include routes
 */
require APPPATH . 'routes' . EXT;

/*
 * Setup Cookies
 */
Cookie::$salt = '987s98d7f98sdkjxchvkx98x';

/**
 * Set the default language
 */
$lang = Cookie::get('lang');

$allowed_languages = Kohana::$config->load('common.languages');

if (array_key_exists($lang, $allowed_languages)) {
	I18n::lang($lang);
} else {
	I18n::lang('ru');
}
/*
 * Подробности об использовании lang в роутах смотри в комментарии в начале routes.php
 *
 * Если hide_lang указан, то:
 * - Force_URL будет скрывать указанный язык в генерируемых url;
 * - обязательно нужно указывать параметр lang в роуте именно как необязательный, т.е. (<lang>/).
 */
Force_URL::hide_lang('ru');


/*
 * Setup Cache
 *
 * You can use this variants for cache driver:
 * sqlite
 * memcache
 * memcachetag
 * file
 *
 * For more information check application/config/cache.php
 */
if (Kohana::$environment == Kohana::PRODUCTION) {
	Cache::$default = 'sqlite';
} else {
	Cache::$default = 'sqlite';
}

// Load the cache driver using default setting
$cache = Cache::instance();

/*
 * DEPLOY
 */
$deploy = DOCROOT . '../deploy.flag';

if (is_file($deploy)) {
	Session::instance()->set('deploy', filectime($deploy));
} else {
	Session::instance()->delete('deploy');
}
