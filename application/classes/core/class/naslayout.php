<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Class_NasLayout
 * User: legion
 * Date: 28.07.2020
 * Time: 14:45
 */
class Core_Class_NasLayout extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Class_NasLayout';
	protected $model_name = 'class_naslayout';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public function get_list_as_array() {
		return $this->get_builder()
			->select_column('nasLayoutClass', 'index')
			->select_column(DB::expr('CONCAT(nasLayoutClassName, " - ", nasLayoutClass)'), 'title')
			->select_all()
			->as_array('index', 'title');
	}

}