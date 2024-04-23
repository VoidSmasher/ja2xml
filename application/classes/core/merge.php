<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Merge
 * User: legion
 * Date: 25.04.2021
 * Time: 10:43
 */
class Core_Merge extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Merge';
	protected $model_name = 'merge';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}