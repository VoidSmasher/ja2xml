<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Api_Application
 * User: legion
 * Date: 04.09.12
 * Time: 16:47
 */
class Core_Api_Application extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Application';
	protected $model_name = 'application';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
		$this->order_by_default = array(
			"{$this->table}.name" => 'asc',
		);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * SET
	 */

	public function key($key) {
		$this->builder->where('key', '=', $key);
		return $this;
	}

	public function domain($domain) {
		$this->builder->where('domain', '=', $domain);
		return $this;
	}

} // End Core_Api_Application
