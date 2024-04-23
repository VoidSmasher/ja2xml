<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Roles
 * User: legion
 * Date: 15.05.14
 * Time: 10:41
 */
class Controller_Developer_Roles extends Controller_Developer_Template {

	public function action_index() {
		$builder = Core_User_Role::factory()->preset_for_admin()->get_builder();

		$filter = Force_Filter::factory(array(
			'name' => 'LIKE',
		))->apply($builder);

		$list = Force_List::factory();

		$list->column('name');
		$list->column('description')->label(__('common.description'));
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$list->apply($builder)
			->button_add()
			->each(function (Jelly_Model $model) {
				if (in_array($model->name, Core_User_Role::get_fixed_roles())) {
					$model->button_edit = '';
					$model->button_delete = '';
				}
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_User_Role::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_User_Role::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model) {
		$form = Jelly_Form::factory($model)
			->preset_for_admin();
		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_User_Role::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		};
	}

} // End Controller_Developer_Roles