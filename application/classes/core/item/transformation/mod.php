<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Item_Transformation_Mod
 * User: legion
 * Date: 08.05.2021
 * Time: 13:37
 */
class Core_Item_Transformation_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Item_Transformation_Mod';
	protected $model_name = 'item_transformation_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}