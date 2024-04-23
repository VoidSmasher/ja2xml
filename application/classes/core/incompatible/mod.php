<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Incompatible_Mod
 * User: legion
 * Date: 19.10.19
 * Time: 9:02
 */
class Core_Incompatible_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Incompatible_Mod';
	protected $model_name = 'incompatible_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

} // End Core_Incompatible_Mod