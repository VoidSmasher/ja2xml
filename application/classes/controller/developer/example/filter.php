<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Example_Filter
 * User: legion
 * Date: 19.08.14
 * Time: 8:07
 */
class Controller_Developer_Example_Filter extends Controller_Developer_Template {

	public function action_index() {
		$builder = Core_User::factory()->preset_for_admin()->get_builder()
			->select_column('users.*')
			->select_column(DB::expr('CONCAT_WS(" ", users.surname, users.name, users.patronymic)'), 'full_name');

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('name', 'Любые данные пользователя')
				->where(DB::expr('CONCAT_WS(" ", users.username, users.email, users.name, users.surname, users.patronymic)'), 'LIKE'),
			Force_Filter_Select::factory('role', 'по привелегиям', Core_User_Role::factory()->get_list_as_array())
				->join_on('roles_users', 'users.id', '=', 'roles_users.user_id')->where('roles_users.role_id'),
			Force_Filter_Date::factory('created_from', 'дата от')->where('created_at', '>='),
			Force_Filter_Date::factory('created_to', 'дата до')->where('created_at', '<='),
		))->apply($builder);

		$list = Force_List::factory();

		$list->column('id');
		$list->column('full_name');
		$list->column('created_at');

		$list->apply($builder)
			->button_add();

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

} // End Controller_Developer_Example_Filter