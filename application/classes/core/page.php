<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Page
 * User: legion
 * Date: 13.07.14
 * Time: 13:20
 */
class Core_Page extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Page';
	protected $model_name = 'page';
	protected static $cannot_be_deleted = array(
		'eula',
		'about',
	);

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * CHECKS
	 */

	public static function can_be_deleted($alias) {
		return !in_array($alias, self::$cannot_be_deleted);
	}

	/*
	 * DELETE
	 */

	protected function _can_be_deleted(Jelly_Model $model) {
		return self::can_be_deleted($model->alias);
	}

} // End Core_Page
