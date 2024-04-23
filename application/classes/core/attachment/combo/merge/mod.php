<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachment_Combo_Merge_Mod
 * User: legion
 * Date: 08.05.2021
 * Time: 12:20
 */
class Core_Attachment_Combo_Merge_Mod extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Attachment_Combo_Merge_Mod';
	protected $model_name = 'attachment_combo_merge_mod';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}