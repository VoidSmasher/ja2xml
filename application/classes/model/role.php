<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Role
 * User: legion
 * Date: 15.05.14
 * Time: 10:48
 */
class Model_Role extends Model_Auth_Role {

	public function __construct() {
		parent::__construct();
		$this->meta()->field('name')->label = __('common.name');
		$this->meta()->field('description')->label = __('common.description');
		$this->meta()->field('users')->label = __('common.links');
	}

} // End Model_Role