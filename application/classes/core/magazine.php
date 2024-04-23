<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Magazine
 * User: legion
 * Date: 08.11.19
 * Time: 15:00
 */
class Core_Magazine extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Magazine';
	protected $model_name = 'magazine';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}