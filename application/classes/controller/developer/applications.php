<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Applications
 * User: legion
 * Date: 04.09.12
 * Time: 16:38
 */
class Controller_Developer_Applications extends Controller_Developer_Template {

	public function action_index() {
		$builder = Core_Api_Application::factory()->preset_for_admin()->get_builder();

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('name', 'Название или ключ приложения')
				->where(DB::expr('CONCAT_WS(" ", LOWER(applications.name), LOWER(applications.key))'), 'LIKE'),
		))->apply($builder);

		$list = Force_List::factory();

		$list->column('id');
		$list->column('name');
		$list->column('domain');
		$list->column('key');
		$list->column('created_at');
		$list->column('updated_at');
		$list->column('button_edit')->button_edit();

		$list->apply($builder)
			->button_add();

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Api_Application::factory()->create();
		$model->key = $this->_generate_key();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Api_Application::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model) {
		$form = Jelly_Form::factory($model)
			->preset_for_admin();

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();
			if (empty($model->key)) {
				$model->key = $this->_generate_key();
			}
		}

		$this->template->content = $form->render();
	}

	protected function _generate_key() {
		return md5(uniqid());
	}

} // End Controller_Developer_Applications