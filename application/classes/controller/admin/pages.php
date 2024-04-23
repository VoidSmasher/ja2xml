<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Pages
 * User: legion
 * Date: 21.07.14
 * Time: 18:02
 */
class Controller_Admin_Pages extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Page::factory()->preset_for_admin()->get_builder();

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('title', __('common.title'))->where('pages.title', 'LIKE'),
		))->apply($builder);

		$list = Force_List::factory()->preset_for_admin();

		$list->column('id');
		$list->column('title');
		$list->column('is_published')->col_control();
		$list->column('created_at');
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_place();

		$list->apply($builder)
			->button_add()
			->each(function (Jelly_Model $model) {
				$model->format('is_published', Force_Label::factory($model->is_published)->preset_boolean_published());
				if (Core_Page::can_be_deleted($model->alias)) {
					$model->button_delete = Force_Button::preset_delete($model->id);
				}
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Page::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Page::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model) {
		$this->template->content = Jelly_Form::factory($model, array(
			Force_Form_Section::factory(null, array(
				Force_Form_Alias::factory('alias'),
				'title',
				Force_Form_Markdown::factory('content'),
//				Force_Form_Show_Value::factory('content'),
				'is_published',
			)),
		))
			->preset_for_admin()
			->render();
	}

	public function action_delete() {
		if (Core_Page::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

} // End Controller_Admin_Pages