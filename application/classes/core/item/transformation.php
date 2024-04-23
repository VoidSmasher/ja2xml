<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Item_Transformation
 * User: legion
 * Date: 08.05.2021
 * Time: 13:36
 */
class Core_Item_Transformation extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Item_Transformation';
	protected $model_name = 'item_transformation';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}