<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Attachment_Combo_Merges
 * User: legion
 * Date: 25.04.2021
 * Time: 10:49
 */
class Controller_Admin_Attachment_Combo_Merges extends Controller_Admin_Template {

	public function action_index() {
		/** @var Jelly_Builder $builder */
		$builder = Core_Attachment_Combo_Merge_Mod::factory()->preset_for_admin()->get_builder()
			->join(['items_mod', 'source_item'], 'LEFT')->on('source_item.uiIndex', '=', 'attachment_combo_merges_mod.usItem')
			->join(['items_mod', 'attachment_item_1'], 'LEFT')->on('attachment_item_1.uiIndex', '=', 'attachment_combo_merges_mod.usAttachment1')
			->join(['items_mod', 'attachment_item_2'], 'LEFT')->on('attachment_item_2.uiIndex', '=', 'attachment_combo_merges_mod.usAttachment2')
			->join(['items_mod', 'attachment_item_3'], 'LEFT')->on('attachment_item_3.uiIndex', '=', 'attachment_combo_merges_mod.usAttachment3')
			->join(['items_mod', 'result_item'], 'LEFT')->on('result_item.uiIndex', '=', 'attachment_combo_merges_mod.usResult')
			->select_column('attachment_combo_merges_mod.*')
			->select_column('source_item.szLongItemName', 'sourceItemName')
			->select_column('attachment_item_1.szLongItemName', 'attachment1Name')
			->select_column('attachment_item_2.szLongItemName', 'attachment2Name')
			->select_column('attachment_item_3.szLongItemName', 'attachment3Name')
			->select_column('result_item.szLongItemName', 'resultItemName');

		$filter = Force_Filter::factory([
			Force_Filter_Input::factory('source', 'Source Item'),
			Force_Filter_Input::factory('result', 'Result Item'),
		])->apply($builder);

		$this->combo_filter($filter, $builder, 'source', 'usItem', 'source_item.szLongItemName');
		$this->combo_filter($filter, $builder, 'result', 'usResult', 'result_item.szLongItemName');

		$collection = $builder->select_all();

		$list = Force_List::factory()->preset_for_admin();

		$list->column('usItem')->col_left();

		$attachment_fields = array();

		foreach ($collection as $model) {
			foreach ($model->meta()->fields() as $field_name => $field_data) {
				if (strpos($field_name, 'Attachment') && $model->{$field_name}) {
					$attachment_fields[$field_name] = $field_name;
				}
			}
		}

		foreach ($attachment_fields as $field_name) {
			$list->column($field_name)->col_left();
		}

		$list->column('usResult')->col_left();
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$list->apply($builder, null, false)
			->title('Attachment Combo Merges')
			->button_add()
			->each(function (Model_Attachment_Combo_Merge_Mod $model) {
				$model->format('usItem', $model->usItem . ' ' . $model->sourceItemName);
				$this->format_attachment_fields($model);
				$model->format('usResult', $model->usResult . ' ' . $model->resultItemName);
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function format_attachment_fields(Model_Attachment_Combo_Merge_Mod $model) {
		for ($i = 1; $i < 21; $i++) {
			$field = 'usAttachment' . $i;
			$value = $model->{$field};
			if ($value < 1) {
				$value = '';
			}
			if ($value && $i < 4) {
				$title = 'attachment' . $i . 'Name';
				$model->format($field, $value . ' ' . $model->{$title});
			} else {
				$model->format($field, $value);
			}
		}
	}

	public function action_add() {
		$model = Core_Attachment_Combo_Merge_Mod::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Attachment_Combo_Merge_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form($model) {
		$form = Jelly_Form::factory($model, array())->preset_for_admin();

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Attachment_Combo_Merge_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

}