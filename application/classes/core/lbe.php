<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_LBE
 * User: legion
 * Date: 14.11.19
 * Time: 23:23
 */
class Core_LBE extends Core_Common {

	const ITEM_CLASS_LBE = 131072;

	use Core_Common_Static;

	protected static $model_class = 'Model_LBE';
	protected $model_name = 'lbe';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}