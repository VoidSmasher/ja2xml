<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Incompatible
 * User: legion
 * Date: 19.10.19
 * Time: 8:41
 */
class Core_Incompatible extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Incompatible';
	protected $model_name = 'incompatible';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

} // End Core_Incompatible