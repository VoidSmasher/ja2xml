<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 08.04.11
 * Time: 17:36
 */
return array(

	'upload' => array(
		'directory' => 'uploads/images/',
		'temp_directory' => 'uploads/images/temp/',

		'allowed_image_types' => array(
			'image/pjpeg' => 'jpg',
			'image/jpeg' => 'jpg',
			'image/jpg' => 'jpg',
			'image/png' => 'png',
			'image/x-png' => 'png',
			'image/gif' => 'gif',
		),
	),
	'static' => array(
		'directory' => 'assets/public/images/',
	),

	/*
		'avatar' => array(
			'crop' => array(
				'width' => 125,
				'height' => 125,
				'style' => Image::INVERSE,
				'types' => array(
					'avatar_small',
				),
				'sharpen => 10, // от 1 да 100, - NULL, ноль, false или не указывать, чтобы игнорировать параметр
				'quality => 10, // от 1 да 100, - NULL, ноль, false или не указывать, чтобы игнорировать параметр
			),
			'dir' => 'uploads/images/avatars/large/',
			'default' => 'placeholders/125x125.png',
		),
	*/

	'avatar_large' => array(
		'crop' => array(
			'width' => 125,
			'height' => 125,
			'style' => Image::INVERSE,
			'types' => array(
				'avatar_small',
			),
		),
		'dir' => 'uploads/images/avatars/large/',
		'default' => 'placeholders/125x125.png',
	),
	'avatar_small' => array(
		'crop' => array(
			'width' => 45,
			'height' => 45,
			'style' => Image::INVERSE,
		),
		'dir' => 'uploads/images/avatars/small/',
		'default' => 'placeholders/45x45.png',
	),

	'article_image_large' => array(
		'crop' => array(
			'width' => 730,
			'height' => null,
			'style' => Image::INVERSE,
			'types' => array(
				'article_image',
				'article_image_small',
			),
		),
		'dir' => 'uploads/images/article/large/',
		'default' => 'placeholders/730x300.png',
	),
	'article_image' => array(
		'crop' => array(
			'width' => 350,
			'height' => 262,
			'style' => Image::INVERSE,
		),
		'dir' => 'uploads/images/article/regular/',
		'default' => 'placeholders/350x262.png',
	),
	'article_image_small' => array(
		'crop' => array(
			'width' => 125,
			'height' => null,
			'style' => Image::INVERSE,
		),
		'dir' => 'uploads/images/article/small/',
		'default' => 'placeholders/125x125.png',
	),

	'summernote' => array(
		'crop' => array(
			'width' => null,
			'height' => null,
			'style' => Helper_Image::AS_IS,
		),
		'dir' => 'uploads/images/summernote/',
		'default' => 'placeholders/125x125.png',
	),

);
