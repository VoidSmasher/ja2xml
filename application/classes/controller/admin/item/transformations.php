<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Item_Transformations
 * User: legion
 * Date: 25.04.2021
 * Time: 10:49
 */
class Controller_Admin_Item_Transformations extends Controller_Admin_Template {

	public function action_index() {
		/** @var Jelly_Builder $builder */
		$builder = Core_Item_Transformation_Mod::factory()->preset_for_admin()->get_builder()
			->join(['items_mod', 'source_item'], 'LEFT')->on('source_item.uiIndex', '=', 'item_transformations_mod.usItem')
			->join(['items_mod', 'result_item_1'], 'LEFT')->on('result_item_1.uiIndex', '=', 'item_transformations_mod.usResult1')
			->join(['items_mod', 'result_item_2'], 'LEFT')->on('result_item_2.uiIndex', '=', 'item_transformations_mod.usResult2')
			->join(['items_mod', 'result_item_3'], 'LEFT')->on('result_item_3.uiIndex', '=', 'item_transformations_mod.usResult3')
			->join(['items_mod', 'result_item_4'], 'LEFT')->on('result_item_4.uiIndex', '=', 'item_transformations_mod.usResult4')
			->join(['items_mod', 'result_item_5'], 'LEFT')->on('result_item_5.uiIndex', '=', 'item_transformations_mod.usResult5')
			->join(['items_mod', 'result_item_6'], 'LEFT')->on('result_item_6.uiIndex', '=', 'item_transformations_mod.usResult6')
			->join(['items_mod', 'result_item_7'], 'LEFT')->on('result_item_7.uiIndex', '=', 'item_transformations_mod.usResult7')
			->join(['items_mod', 'result_item_8'], 'LEFT')->on('result_item_8.uiIndex', '=', 'item_transformations_mod.usResult8')
			->join(['items_mod', 'result_item_9'], 'LEFT')->on('result_item_9.uiIndex', '=', 'item_transformations_mod.usResult9')
			->join(['items_mod', 'result_item_10'], 'LEFT')->on('result_item_10.uiIndex', '=', 'item_transformations_mod.usResult10')
			->select_column('item_transformations_mod.*')
			->select_column('source_item.szLongItemName', 'sourceItemName')
			->select_column('result_item_1.szLongItemName', 'usResult1Name')
			->select_column('result_item_2.szLongItemName', 'usResult2Name')
			->select_column('result_item_3.szLongItemName', 'usResult3Name')
			->select_column('result_item_4.szLongItemName', 'usResult4Name')
			->select_column('result_item_5.szLongItemName', 'usResult5Name')
			->select_column('result_item_6.szLongItemName', 'usResult6Name')
			->select_column('result_item_7.szLongItemName', 'usResult7Name')
			->select_column('result_item_8.szLongItemName', 'usResult8Name')
			->select_column('result_item_9.szLongItemName', 'usResult9Name')
			->select_column('result_item_10.szLongItemName', 'usResult10Name');

		$filter = Force_Filter::factory([
			Force_Filter_Input::factory('source', 'Source Item'),
			Force_Filter_Input::factory('result1', 'Result 1 Item'),
			Force_Filter_Input::factory('result2', 'Result 2 Item'),
		])->apply($builder);

		$this->combo_filter($filter, $builder, 'source', 'usItem', 'source_item.szLongItemName');
		$this->combo_filter($filter, $builder, 'result1', 'usResult1', 'result_item_1.szLongItemName');
		$this->combo_filter($filter, $builder, 'result2', 'usResult2', 'result_item_2.szLongItemName');

		$collection = $builder->select_all();

		$list = Force_List::factory()->preset_for_admin();

		$list->column('szMenuRowText')->col_left();
		$list->column('szTooltipText')->col_left();
		$list->column('usAPCost')->col_left();
		$list->column('iBPCost')->col_left();
		$list->column('usItem')->col_left();

		$attachment_fields = array();

		foreach ($collection as $model) {
			foreach ($model->meta()->fields() as $field_name => $field_data) {
				if (strpos($field_name, 'Result') && $model->{$field_name}) {
					$attachment_fields[$field_name] = $field_name;
				}
			}
		}

		foreach ($attachment_fields as $field_name) {
			$list->column($field_name)->col_left();
		}

		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$add_link = Force_URL::current_clean()->action('add');

		if ($source = $filter->get_value('source')) {
			if (is_numeric($source)) {
				$add_link->query_param('source', $source);
			}
		}

		if ($result1 = $filter->get_value('result1')) {
			if (is_numeric($result1)) {
				$add_link->query_param('result1', $result1);
			}
		}

		if ($result2 = $filter->get_value('result2')) {
			if (is_numeric($result2)) {
				$add_link->query_param('result2', $result2);
			}
		}

		$list->apply($builder, null, false)
			->title('Item Transformations')
			->button_add($add_link->get_url())
			->each(function (Model_Item_Transformation_Mod $model) {
				$model->format('usItem', $model->usItem . ' ' . $model->sourceItemName);
				$this->format_attachment_fields($model);
				$model->format('usResult', $model->usResult . ' ' . $model->resultItemName);
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function format_attachment_fields(Model_Item_Transformation_Mod $model) {
		for ($i = 1; $i < 21; $i++) {
			$field = 'usResult' . $i;
			$value = $model->{$field};
			if ($value < 1) {
				$value = '';
			}
			if ($value && $i < 4) {
				$title = 'usResult' . $i . 'Name';
				$model->format($field, $value . ' ' . $model->{$title});
			} else {
				$model->format($field, $value);
			}
		}
	}

	public function action_add() {
		/** @var Model_Item_Transformation_Mod $model */
		$model = Core_Item_Transformation_Mod::factory()->create();

		if ($source = $this->request->query('source')) {
			if (is_numeric($source)) {
				$model->usItem = $source;
			}
		}

		if ($result1 = $this->request->query('result1')) {
			if (is_numeric($result1)) {
				$model->usResult1 = $result1;
			}
		}

		if ($result2 = $this->request->query('result2')) {
			if (is_numeric($result2)) {
				$model->usResult2 = $result2;
			}
		}

		$this->_form($model);
	}

	public function action_edit() {
		/** @var Model_Item_Transformation_Mod $model */
		$model = Core_Item_Transformation_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Model_Item_Transformation_Mod $model) {
		$form = Jelly_Form::factory($model, array())->preset_for_admin();

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Item_Transformation_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

}