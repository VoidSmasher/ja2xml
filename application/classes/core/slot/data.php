<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Slot_Data
 * User: legion
 * Date: 13.10.19
 * Time: 1:26
 */
class Core_Slot_Data extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Slot_Data';
	protected $model_name = 'slot_data';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

} // End Core_Slot_Data