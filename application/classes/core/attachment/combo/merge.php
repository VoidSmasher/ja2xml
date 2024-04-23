<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachment_Combo_Merge
 * User: legion
 * Date: 08.05.2021
 * Time: 12:20
 */
class Core_Attachment_Combo_Merge extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Attachment_Combo_Merge';
	protected $model_name = 'attachment_combo_merge';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

}