<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachment
 * User: legion
 * Date: 12.10.19
 * Time: 19:44
 */
class Core_Attachment extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Attachment';
	protected $model_name = 'attachment';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

} // End Core_Attachment