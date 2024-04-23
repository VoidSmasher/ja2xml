<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Class_NasAttachments
 * User: legion
 * Date: 28.07.2020
 * Time: 15:45
 */
class Controller_Admin_Class_NasAttachments extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Class_NasAttachment::factory()->preset_for_admin()->get_builder();

		$filter = Force_Filter::factory(array())->apply($builder);

		$list = Force_List::factory()->preset_for_admin()
			->title('nasAttachment Classes');

		$list->column('nasAttachmentClass');
		$list->column('nasAttachmentClassName');
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$list->apply($builder, null, false)
			->button_add()
			->each(function (Jelly_Model $model) {

			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Class_NasAttachment::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Class_NasAttachment::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form($model) {
		$form = Jelly_Form::factory($model, array())->preset_for_admin();

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Class_NasAttachment::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

}