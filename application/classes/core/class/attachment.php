<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Class_Attachment
 * User: legion
 * Date: 28.07.2020
 * Time: 14:44
 */
class Core_Class_Attachment extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Class_Attachment';
	protected $model_name = 'class_attachment';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public function get_list_as_array() {
		return $this->get_builder()
			->select_column('AttachmentClass', 'index')
			->select_column(DB::expr('CONCAT(AttachmentClassName, " - ", AttachmentClass)'), 'title')
			->select_all()
			->as_array('index', 'title');
	}

}