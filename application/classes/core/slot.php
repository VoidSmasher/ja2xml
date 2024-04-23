<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Slot
 * User: legion
 * Date: 13.10.19
 * Time: 1:07
 */
class Core_Slot extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Slot';
	protected $model_name = 'slot';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

} // End Core_Slot