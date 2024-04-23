<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class: Model_User
 * User: legion
 * Date: 15.05.14
 * Time: 10:48
 */
class Model_User extends Model_Auth_User {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('users');
		$meta->sorting(array(
			'surname' => 'asc',
			'name' => 'asc',
			'patronymic' => 'asc',
		));

		$meta->fields(array(
			'id' => Jelly::field('primary'),

			/*
			 * Регистрационные данные
			 */
			'username' => Jelly::field('string', array(
				'label' => __('user.login'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							32,
						),
					),
					array(
						'min_length',
						array(
							':value',
							3,
						),
					),
				),
				'unique' => TRUE,
			)),
			'email' => Jelly::field('email', array(
				'label' => __('user.email'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
				'unique' => TRUE,
			)),
			'password' => new Jelly_Field_Password(array(
				'label' => __('user.password'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							64,
						),
					),
					array(
						'min_length',
						array(
							':value',
							5,
						),
					),
				),
				'hash_with' => array(
					Auth::instance(),
					'hash',
				),
			)),
			'password_confirm' => new Jelly_Field_Password(array(
				'in_db' => FALSE,
				'label' => __('user.password_confirm'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							64,
						),
					),
					array(
						'min_length',
						array(
							':value',
							5,
						),
					),
				),
				'hash_with' => array(
					Auth::instance(),
					'hash',
				),
			)),

			/*
			 * Личные данные
			 */
			'full_name' => new Jelly_Field_String(array(
				'label' => __('user.name.full'),
				'in_db' => false,
			)),
			'surname' => new Jelly_Field_String(array( // required
				'label' => __('user.surname'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							40,
						),
					),
				),
			)),
			'name' => new Jelly_Field_String(array( // required
				'label' => __('user.name'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							40,
						),
					),
				),
			)),
			'patronymic' => new Jelly_Field_String(array( // optional
				'label' => __('user.patronymic'),
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							40,
						),
					),
				),
			)),

			'avatar' => new Jelly_Field_String(array(
				'label' => __('user.avatar'),
			)),

			/*
			 * Учетные данные
			 */
			'is_confirmed' => new Jelly_Field_Boolean(array(
				'default' => false,
			)),
			'hash' => new Jelly_Field_String(array(
				'default' => Core_User::generate_new_md5_hash(),
			)),
			'logins' => new Jelly_Field_Integer(array(
				'default' => 0,
				'convert_empty' => TRUE,
				'empty_value' => 0,
			)),
			'last_login' => new Jelly_Field_Timestamp(array(
				'label' => __('user.last_login'),
				'default' => 0,
			)),
			'created_at' => new Jelly_Field_Timestamp(array(
				'label' => __('common.registered_at'),
				'format' => Force_Date::FORMAT_SQL,
				'default' => time(),
			)),
			'roles' => new Jelly_Field_ManyToMany(array(
				'label' => __('user.roles'),
			)),
		));
	}

	public function get_full_name() {
		return Core_User::get_full_name($this->surname, $this->name, $this->patronymic);
	}

	public function get_name($surname_first = true) {
		return Core_User::get_name($this->surname, $this->name, $surname_first);
	}

	public function get_roles() {
		return Core_User::factory($this->id)->get_roles();
	}

	public function get_roles_flipped() {
		return Core_User::factory($this->id)->get_roles_flipped();
	}

	public function has_role($role_name_or_roles_as_array, $match_all_given_roles = false) {
		return Core_User::factory($this->id)->has_role($role_name_or_roles_as_array, $match_all_given_roles);
	}

	public function can_login() {
		return $this->has_role('login');
	}

	public function is_admin() {
		return $this->has_role('admin');
	}

	public function is_online() {
		return Core_User::is_online($this->id);
	}

	public function set_online() {
		return Core_User::set_online($this->id);
	}

	/*
	 * AVATAR
	 */

	public function get_avatar_large($as_html_tag = FALSE, $attributes = null) {
		if ($as_html_tag) {
			return Helper_Image::get_image_from_current_session_or_from_cdn($this->avatar, 'avatar_large', $this->get_name(), $attributes);
		} else {
			return Helper_Image::get_filename_from_current_session_or_from_cdn($this->avatar, 'avatar_large');
		}
	}

	public function get_avatar_small($as_html_tag = FALSE, $attributes = null) {
		if ($as_html_tag) {
			return Helper_Image::get_image_from_current_session_or_from_cdn($this->avatar, 'avatar_small', $this->get_name(), $attributes);
		} else {
			return Helper_Image::get_filename_from_current_session_or_from_cdn($this->avatar, 'avatar_small');
		}
	}

	public function remove_avatar($only_file = true) {
		$errors = false;
		if (!empty($this->avatar)) {
			if (Helper_Image::remove_image($this->avatar, 'avatar_large') && !$only_file) {
				$this->avatar = NULL;
				try {
					$this->save();
				} catch (Jelly_Validation_Exception $e) {
					$errors = $e->errors();
				}
			}
		}
		return $errors;
	}

	public function save($validation = NULL) {
		$this->login = strip_tags($this->login);
		$this->surname = strip_tags($this->surname);
		$this->name = strip_tags($this->name);
		$this->patronymic = strip_tags($this->patronymic);

		return parent::save($validation);
	}

} // End Model_User
