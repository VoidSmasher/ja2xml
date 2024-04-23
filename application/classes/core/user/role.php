<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_User_Role
 * User: legion
 * Date: 23.04.14
 * Time: 22:32
 */
class Core_User_Role extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Role';
	protected $model_name = 'role';
	protected $ignore_hidden_roles = false;
	protected static $hidden_roles = array(
		'developer',
	);

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * SET
	 */

	public function name($value) {
		$this->builder->where("{$this->table}.name", '=', (string)$value);
		return $this;
	}

	public function user_id($user_id, $include = true) {
		$this->builder->join('roles_users')->on("{$this->table}.id", '=', 'roles_users.role_id')
			->where('roles_users.user_id', ($include) ? '=' : '!=', $user_id);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_list_as_array($convert_role_name_to_label = true) {
		$roles = $this
//			->order_by('name')
			->get_list()
			->as_array('id', 'name');

		if ($convert_role_name_to_label) {
			foreach ($roles as $key => $name) {
				$roles[$key] = Core_User_Role::get_label($name);
			}
		}

		return $roles;
	}

	public static function get_label($role_name) {
		return i18n::get_default('user.role.' . $role_name, $role_name);
	}

	public static function get_fixed_roles() {
		$fixed_roles = Kohana::$config->load('auth.fixed_roles');
		if (!is_array($fixed_roles)) {
			$fixed_roles = array();
		}
		return $fixed_roles;
	}

	/*
	 * BEFORE GET
	 */

	protected function _before_get() {
		$this->_check_hidden_roles();
	}

	/*
	 * HIDDEN ROLES
	 */

	protected function _check_hidden_roles() {
		if (!$this->ignore_hidden_roles && !empty(self::$hidden_roles)) {
			$this->builder->where("{$this->table}.name", 'NOT IN', self::$hidden_roles);
		}
	}

	public function ignore_hidden_roles() {
		$this->ignore_hidden_roles = true;
		return $this;
	}

	/*
	 * PREDEFINED SETUPS
	 */

	public function preset_for_admin() {
		return parent::preset_for_admin();
	}

} // End Core_User_Role
