<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Slots
 * User: legion
 * Date: 13.10.19
 * Time: 1:48
 */
class Controller_Admin_Slots extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Slot_Mod::factory()->preset_for_admin()->get_builder()
			->where('uiSlotIndex', '<>', 0)
			->order_by('nasLayoutClass')
			->order_by('uiSlotIndex');

		$filter = Force_Filter::factory(array())->apply($builder);

		$list = Force_List::factory()->preset_for_admin()
			->title('Attachment Slots');

		$list->column('uiSlotIndex');
		$list->column('szSlotName')->col_no_wrap();
		$list->column('nasAttachmentClass');
		$list->column('nasLayoutClass');
		$list->column('usDescPanelPosX');
		$list->column('usDescPanelPosY');
		$list->column('fMultiShot');
		$list->column('fBigSlot');
		$list->column('ubPocketMapping');
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$list->apply($builder, null, false)
			->button_add()
			->each(function (Jelly_Model $model) {
				$model->format('nasLayoutClass', Core_Item::get_nasLayoutClass_label($model->nasLayoutClass));
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Slot_Mod::factory()->create();
		$model->uiSlotIndex = Core_Slot_Mod::factory()->get_builder()
			->select_column(DB::expr('MAX(uiSlotIndex)'), 'max')
			->limit(1)
			->select()
			->get('max');
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Slot_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model) {
		$form = Jelly_Form::factory($model)->preset_for_admin();

		$nasAttachmentClasses = Core_Class_NasAttachment::factory()->get_list_as_array();
		$nasLayoutClasses = Core_Class_NasLayout::factory()->get_list_as_array();

		$form->control = Force_Form_Section::factory('Class and Name', [
			Force_Form_Input::factory('szSlotName'),
			Force_Form_Select::factory('nasAttachmentClass')
				->add_option(NULL, '---')
				->add_options($nasAttachmentClasses),
			Force_Form_Select::factory('nasLayoutClass')
				->add_option(NULL, '---')
				->add_options($nasLayoutClasses),
		]);

		$form->control = Force_Form_Section::factory('Position', [
			Force_Form_Input::factory('usDescPanelPosX'),
			Force_Form_Input::factory('usDescPanelPosY'),
		]);

		$form->control = Force_Form_Section::factory('Settings', [
			Force_Form_Input::factory('fMultiShot'),
			Force_Form_Input::factory('fBigSlot'),
			Force_Form_Input::factory('ubPocketMapping'),
		]);

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Slot_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

} // End Controller_Admin_Slots