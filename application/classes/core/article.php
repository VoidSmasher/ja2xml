<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Article
 * Article: legion
 * Date: 04.05.14
 * Time: 16:57
 */
class Core_Article extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Article';
	protected $model_name = 'article';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

} // End Core_Article
