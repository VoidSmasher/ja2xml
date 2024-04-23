<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Users
 * User: legion
 * Date: 19.01.14
 * Time: 15:29
 */
class Controller_Admin_Users extends Controller_Admin_Template {

	const ROLE_COLUMN_PREFIX = 'role_';

	public function action_index() {
		$builder = Core_User::factory()->preset_for_admin()->get_builder()
			->select_column('users.*')
			->select_column(DB::expr('CONCAT_WS(" ", users.surname, users.name, users.patronymic)'), 'full_name');

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('id', 'ID')
				->popover(__('user.filter_by_id.popover_title'), __('user.filter_by_id.popover_content'))
				->where_numeric_array('users.id'),

			Force_Filter_Input::factory('name', __('user.filter_by_name'))
				->where(DB::expr('CONCAT_WS(" ", users.username, users.email, users.name, users.surname, users.patronymic)'), 'LIKE'),

			Force_Filter_Select::factory('role', __('user.filter_by_role'), Core_User_Role::factory()
				->get_list_as_array())
				->join_on('roles_users', 'users.id', '=', 'roles_users.user_id')
				->where('roles_users.role_id'),
		))->apply($builder);

		$count = $builder->count();

		Force_List::apply_sorting($builder);

		$aliases = $builder->get_selected_aliases();

		$pagination = Helper_Pagination::get_admin_pagination($count);
		$collection = $builder->apply_pagination($pagination)->select_all();

		$roles = Core_User_Role::factory()->get_list_as_array(false);

		$model_ids = $collection->as_array(null, 'id');
		$user_roles = array();
		if (!empty($model_ids)) {
			$roles_link = Jelly::query('roles_users')->where('user_id', 'IN', $model_ids)->select_all();

			foreach ($roles_link as $role) {
				$user_roles[$role['user_id']][$role['role_id']] = $role['role_id'];
			}
		}

		$list = Force_List::factory()->preset_for_admin();

		$list->column('id');
		$list->column('email');
		$list->column('full_name');

		foreach ($roles as $role_name) {
			$list->column(self::ROLE_COLUMN_PREFIX . $role_name)
				->label(Core_User_Role::get_label($role_name))
				->col_control();
		}

		$list->column('created_at');
		$list->column('last_login');
		$list->column('button_edit')->button_edit();

		$list->apply($collection, $pagination)
			->aliases($aliases)
			->button_add()
			->each(function (Jelly_Model $model, $roles, $user_roles) {
				$role = array();
				foreach ($roles as $role_id => $role_name) {
					if (array_key_exists($model->id, $user_roles)) {
						$role[$role_name] = array_key_exists($role_id, $user_roles[$model->id]);
					} else {
						$role[$role_name] = false;
					}
				}
				foreach ($role as $name => $value) {
					$model->{self::ROLE_COLUMN_PREFIX . $name} = Force_Label::factory($value)->preset_boolean_yes_no();
				}
				$model->format('last_login', $this->_get_last_login($model, false));
			}, $roles, $user_roles);

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_User::factory()->create();
		$password = md5(uniqid());
		$model->password = $password;
		$model->password_confirm = $password;
		$this->_form($model, true);
	}

	public function action_edit() {
		$model = Core_User::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model, $new_user = false) {
		if ($new_user) {
			$user_roles = array();
		} else {
			$user_roles = Core_User_Role::factory()->preset_for_admin()->ignore_hidden_roles()->user_id($model->id)
				->get_list_as_array();
		}

		$form = Jelly_Form::factory($model, array(
			Force_Form_Section::factory(__('user.personal_data'), array(
				'username',
				'email',
				'surname',
				'name',
				'patronymic',
			)),
			Force_Form_Section::factory(__('user.registration_data'), array(
				Force_Form_Input::factory('new_password', __('user.password_new'))->password(),
				Force_Form_Input::factory('new_password_confirm', __('user.password_confirm'))->password(),
			)),
			Force_Form_Section::factory(__('user.roles'), array(
				Force_Form_ManyToMany::factory('roles')
					->add_available_options(Core_User_Role::factory()->preset_for_admin()->get_list_as_array())
					->add_selected_options($user_roles),
			)),
			Force_Form_Section::factory(__('user.info'), array(
				Force_Form_Show_Value::factory('created_at')->value(Force_Date::factory($model->created_at, '---')
					->show_today_instead_of_current_date()
					->show_seconds()
					->humanize()),
				Force_Form_Show_Value::factory('last_login')->value($this->_get_last_login($model)),
			)),
		))->preset_for_admin();

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();
			$model->username = mb_strtolower($model->username);
			$model->email = mb_strtolower($model->email);
			$new_password = $form->get_value('new_password');
			$new_password_confirm = $form->get_value('new_password_confirm');
			if (!empty($new_password)) {
				if ($new_password === $new_password_confirm) {
					$model->password = $new_password;
					$model->password_confirm = $new_password_confirm;
				} else {
					Helper_Error::add(__('auth.error.passwords_are_not_equal'), array(
						'new_password',
						'new_password_confirm',
					));
				}
			}
		}

		$this->template->content = $form->render();
	}

	public function action_show() {
		$model = Core_User::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();

		$form = Jelly_Form::factory($model, array(
			Force_Form_Section::factory(__('user.personal_data'), array(
				Force_Form_Show_Value::factory('username'),
				Force_Form_Show_Value::factory('email'),
				Force_Form_Show_Value::factory('surname'),
				Force_Form_Show_Value::factory('name'),
				Force_Form_Show_Value::factory('patronymic'),
			)),
			Force_Form_Section::factory(__('user.info'), array(
				Force_Form_Show_Value::factory('last_login')->value($this->_get_last_login($model)),
			)),
		))->preset_for_admin_show();

		$this->template->content = $form->render();
	}

	public function action_remove_avatar() {
		$errors = $this->user->remove_avatar();
		if ($errors) {
			throw new HTTP_Exception_500;
		} else {
			$this->request->redirect(Force_URL::current()->action('edit')->get_url());
		}
	}

	protected function _get_last_login(&$model, $show_seconds = true) {
		if ($model->last_login > 0) {
			$last_login = Force_Date::factory($model->last_login)
				->show_today_instead_of_current_date()
				->show_seconds($show_seconds)
				->humanize();
		} else {
			$last_login = __('user.last_login.never');
		}
		return $last_login;
	}

} // End Controller_Admin_Users
