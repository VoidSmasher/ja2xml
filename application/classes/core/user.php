<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_User
 * User: legion
 * Date: 27.09.14
 * Time: 14:33
 */
class Core_User extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_User';
	protected $model_name = 'user';
	protected $_with_roles = array();
	protected $_roles_cached = array();
	protected $_roles_cached_flipped = array();

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * SET
	 */

	public function login($login) {
		$this->builder->and_where_open();
		$this->builder->where("{$this->table}.email", '=', $login)->or_where("{$this->table}.username", '=', $login);
		$this->builder->and_where_close();
		return $this;
	}

	public function email($email) {
		$this->builder->where("{$this->table}.email", '=', $email);
		return $this;
	}

	public function hash($hash) {
		$this->builder->where("{$this->table}.hash", '=', $hash);
		return $this;
	}

	public function with_role($role) {
		if (is_array($role)) {
			foreach ($role as $_role) {
				$this->with_role($_role);
			}
		} else {
			$this->_with_roles[] = (string)$role;
		}
		return $this;
	}

	/*
	 * GET
	 */

	public function _before_get() {
		if (!empty($this->_with_roles)) {
			$this->builder->join('roles_users')->on('roles_users.user_id', '=', 'users.id')
				->join('roles')->on('roles_users.role_id', '=', 'roles.id');
			if (count($this->_with_roles) > 1) {
				$this->builder->where('roles.name', '=', $this->_with_roles[0]);
			} else {
				$this->builder->where('roles.name', 'IN', $this->_with_roles);
			}
		}
	}

	public function get_list_as_array() {
		$this->builder
			->select_column("{$this->table}.id")
			->select_column(DB::expr("CONCAT_WS(' ', {$this->table}.surname, {$this->table}.name)"), 'full_name');
		return $this->get_list()->as_array('id', 'full_name');
	}

	public function get_list_for_suggest($search, $limit = 10) {
		$this->_before_get();
		$result = array();
		if (!empty($search)) {
			$search_for_like = Helper_Html::prepare_value_for_sql($search);
			$this->builder
				->where(DB::expr("CONCAT_WS('', {$this->table}.username, {$this->table}.surname, {$this->table}.name, {$this->table}.patronymic, {$this->table}.email)"), 'LIKE', $search_for_like)
				->select_column(array(
					'id',
					'username',
					'surname',
					'name',
					'patronymic',
					'email',
				))->limit($limit);

			if (is_numeric($search)) {
				$this->builder->or_where('id', '=', (int)$search);
				$this->order_by('id');
			}

			$collection = $this->builder->select_all();

			if ($collection->count() > 0) {
				foreach ($collection as $user) {
					$result[] = array(
						'value' => self::get_suggest_user_name($user),
						'id' => $user->id,
					);
				}
			}
		}
		return json_encode($result);
	}

	public static function get_suggest_user_name($user) {
		if (self::is_model($user)) {
			return $user->id . ' :: ' . self::get_full_name($user->surname, $user->name, $user->patronymic, $user->username, $user->email);
		} else {
			return '';
		}
	}

	public function get_name_for_suggest() {
		$user = $this->get_one();
		if ($user->loaded()) {

		}
	}

	/*
	 * ONLINE STATUS
	 */

	public static function set_online($user_id) {
		if ($user_id > 0) {
			return Cache::instance()
				->set('user_online.' . (integer)$user_id, true, Helper_Cache::get_lifetime('user_online'));
		}
		return false;
	}

	public static function is_online($user_id) {
		if ($user_id > 0) {
			return Cache::instance()->get('user_online.' . (integer)$user_id, false);
		}
		return false;
	}

	/*
	 * ROLES
	 */

	public function get_roles() {
		$id = $this->get_id();

		if (empty($this->_roles_cached) && is_numeric($id) && ($id > 0)) {

			$roles = Core_User_Role::factory()
				->ignore_hidden_roles()
				->user_id($id)
				->get_list()
				->as_array('id', 'name');

			$this->_roles_cached = $roles;
			$this->_roles_cached_flipped = array_flip($roles);
		}
		return $this->_roles_cached;
	}

	public function get_roles_flipped() {
		$this->get_roles();
		return $this->_roles_cached_flipped;
	}

	public function has_role($role_name_or_roles_as_array, $match_all_given_roles = false) {
		$roles = $this->get_roles_flipped();

		if (is_array($role_name_or_roles_as_array)) {
			if ($match_all_given_roles) {
				$matches = 0;
				foreach ($role_name_or_roles_as_array as $_role) {
					if (array_key_exists($_role, $roles)) {
						$matches++;
					}
				}
				return ($matches == count($role_name_or_roles_as_array));
			} else {
				foreach ($role_name_or_roles_as_array as $_role) {
					if (array_key_exists($_role, $roles)) {
						return true;
					}
				}
				return false;
			}
		} elseif (is_string($role_name_or_roles_as_array)) {
			return array_key_exists($role_name_or_roles_as_array, $roles);
		}

		return false;
	}

	/*
	 * HASH
	 */

	public static function generate_new_md5_hash() {
		return md5(uniqid() . time());
	}

	/*
	 * USER NAME
	 */

	public static function get_full_name($surname, $name, $patronymic = NULL) {
		$full_name = array();
		$name_parts = func_get_args();
		$num = 0;
		foreach ($name_parts as $name_part) {
			if ($num == 3 && !empty($full_name)) {
				$full_name[] = '::';
			}
			if (!empty($name_part)) {
				$full_name[] = $name_part;
			}
			$num++;
		}
		return implode(' ', $full_name);
	}

	public static function get_name($surname, $name, $surname_first = true) {
		if ($surname_first) {
			$user_name = $surname . ' ' . $name;
		} else {
			$user_name = $name . ' ' . $surname;
		}
		$user_name = trim($user_name);
		if (empty($user_name)) {
			$user_name = __('user.name.empty');
		}
		return $user_name;
	}

	public static function popovers_enabled() {
		$popovers_enabled = true;

		$user = Helper_Auth::get_user();
		if (self::is_model($user)) {
			$popovers_enabled = $user->enable_popovers;
		}

		return $popovers_enabled;
	}

} // End Core_User
