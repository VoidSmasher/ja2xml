<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_URL_Parser
 * User: legion
 * Date: 20.06.17
 * Time: 23:47
 * @todo это заготовка для эээ чего-нибудь эдакого. Не использовать! Здесь всё может поменятся капитально.
 */
class Helper_URL_Parser {

	const YOUTUBE = 'youtube';
	const VIMEO = 'vimeo';
	const RUTUBE = 'rutube';

	protected static $_parsers = [
		self::YOUTUBE => [
			'id' => [
				'parser' => '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
				'params' => [],
			],
			'player' => [
				'parser' => 'http://www.youtube.com/embed/:video_id',
				'params' => [
					':video_id',
				],
			],
			'image' => [
				'parser' => 'http://img.youtube.com/vi/:video_id/0.jpg',
				'params' => [
					':video_id',
				],
			],
		],
		self::VIMEO => [
			'id' => [
				'parser' => '/[http|https]+:\/\/(?:www\.|)vimeo\.com\/([a-zA-Z0-9_\-]+)(&.+)?/i',
				'params' => [],
			],
			'player' => [
				'parser' => 'http://player.vimeo.com/video/:video_id',
				'params' => [],
			],
			'image' => [
				'parser' => '',
				'params' => [],
			],
		],
		self::RUTUBE => [
			'id' => [
				'parser' => '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/embed\/([a-zA-Z0-9_\-]+)/i',
				'params' => [],
			],
			'player' => [
				'parser' => 'http://rutube.ru/video/embed/:video_id',
				'params' => [],
			],
			'image' => [
				'parser' => '',
				'params' => [],
			],
		],
	];

} // End Helper_URL_Parser
