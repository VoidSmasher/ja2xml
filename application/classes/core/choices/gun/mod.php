<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 27.03.2021
 * Time: 15:11
 */
class Core_Choices_Gun_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Choices_Gun_Mod';
	protected $model_name = 'choices_gun_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}