<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Pocket
 * User: legion
 * Date: 14.11.19
 * Time: 23:23
 */
class Core_Pocket extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Pocket';
	protected $model_name = 'pocket';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}