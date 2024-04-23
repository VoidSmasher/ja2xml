<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.03.11
 * Time: 14:27
 */

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 *
 * Все роуты необходимо указывать в порядке их использования, от часто используемых к редко используемым,
 * кроме роута default - он всегда последний
 *
 * Если в роуте указать (<lang>/)... (внимание на расположение слеша) и в default роута указать 'lang'='ru',
 * то Force_URL будет автоматически передавать текущий или указанный язык в url
 * Кроме того необходимо указать также и значения, которые могут быть переданы в lang
 *
 * Язык можно указать при генерации урлы конструкциями вида:
 * Force_URL::current()->lang('en')->get_url();
 * Force_URL::current()->route_param('lang','en')->get_url();
 * Force_URL::factory('route')->lang('en')->get_url();
 * Force_URL::factory('route')->route_param('lang','en')->get_url();
 *
 * Пример роута с lang:
Route::set('something', '(<lang>/)something', array(
	'lang' => '(de|en)',
))->defaults([
	'lang' => 'ru',
	'controller' => 'public',
	'action' => 'index',
	'data_type' => DATA_HTML,
	'id' => 0,
]);
 */

// article
Route::set('article', 'article(/<alias>)(.<data_type>)')->defaults([
	'controller' => 'articles',
	'action' => 'post',
	'data_type' => DATA_HTML,
	'alias' => 0,
]);

// lang
Route::set('lang', 'lang(/<id>)(.<data_type>)')->defaults([
	'controller' => 'lang',
	'action' => 'index',
	'data_type' => DATA_HTML,
	'id' => 0,
]);

// error
Route::set('error', 'error(/<code>)(.<data_type>)', array('code' => '\d{1,10}'))->defaults([
	'code' => 404,
	'controller' => 'error',
	'action' => 'index',
	'data_type' => DATA_HTML,
]);

// admin
Route::set('admin', 'admin(/<controller>(/<action>(/<id>(/<id2>))))(.<data_type>)')->defaults([
	'directory' => 'admin',
	'controller' => 'users',
	'action' => 'index',
	'data_type' => DATA_HTML,
	'id' => 0,
	'id2' => 0,
]);

// developer
Route::set('developer', 'developer(/<controller>(/<action>(/<id>(/<id2>))))(.<data_type>)')->defaults([
	'directory' => 'developer',
	'controller' => 'overview',
	'action' => 'index',
	'data_type' => DATA_HTML,
	'id' => 0,
	'id2' => 0,
]);

// migration
Route::set('migration', 'migration(/<controller>(/<action>(/<id>)))(.<data_type>)')->defaults([
	'directory' => 'migration',
	'controller' => 'overview',
	'action' => 'index',
	'id' => 0,
	'data_type' => DATA_HTML,
]);

// daemon
Route::set('daemon', 'daemon/(<controller>(/<action>(/<id>)))(.<data_type>)')->defaults([
	'directory' => 'daemon',
	'controller' => null,
	'action' => 'index',
	'data_type' => DATA_HTML,
	'id' => 0,
]);

// test
Route::set('test', 'test(/<controller>(/<action>(/<id>)))(.<data_type>)')->defaults([
	'directory' => 'test',
	'controller' => 'public',
	'action' => 'index',
	'id' => 0,
	'data_type' => DATA_HTML,
]);

Helper_Counter::yandex_route();
Helper_Counter::google_route();

// api
Route::set('api', 'api(/<controller>(/<action>))(.<data_type>)')->defaults([
	'directory' => 'api',
	'controller' => 'common',
	'action' => 'index',
	'data_type' => DATA_HTML,
]);

Route::set('default', '(<controller>(/<action>(/<id>)))(.<data_type>)')->defaults([
	'controller' => 'public',
	'action' => 'index',
	'data_type' => DATA_HTML,
	'id' => 0,
]);
