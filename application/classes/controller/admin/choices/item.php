<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Choices_Item
 * User: legion
 * Date: 27.03.2021
 * Time: 15:50
 */
class Controller_Admin_Choices_Item extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Choices_Item_Mod::factory()->preset_for_admin()->get_builder()
			->join('items', 'left')->on('items.uiIndex', '=', 'choices_item_mod.uiIndex')
			->join('items_mod', 'left')->on('items_mod.uiIndex', '=', 'choices_item_mod.uiIndex')
			->select_column('choices_item_mod.*')
			->select_column('items_mod.szLongItemName', 'szLongItemName')
			->select_column('items.ubCoolness', 'ubCoolness')
			->select_column('items_mod.ubCoolness', 'ubCoolnessMod')
			->order_by('npc_side')
			->order_by('npc_type')
			->order_by('list_index');

		$filter = Force_Filter::factory(array(
			Force_Filter_Select::factory('npc', 'NPC', Core_Choices_Item::get_combo_filter(), false),
			Force_Filter_Select::factory('list', 'List', Core_Choices_Item::get_lists())
				->where('list_index'),
		))->apply($builder);

		Core_Choices_Item::apply_combo_filter($builder, $filter);

		$selected_list = $filter->get_value('list');
		$show_numbers = is_numeric($selected_list);

		$list = Force_List::factory()->preset_for_admin()
			->title('Item Choices');

		if ($show_numbers) {
			$list->column(Force_List::ROW_NUMBER)->label('N')->col_control();
		}

		$list->column('uiIndex');
		$list->column('weapon_image')->col_control();
		$list->column('szLongItemName')->label('Long item name')->col_main();
		$list->column('npc_side');
		$list->column('npc_type');
		$list->column('ubCoolnessMod')->label('Coolness Mod')->col_control();
		$list->column('ubCoolness')->label('Coolness Original')->col_control();
		$list->column('list_name');

		$list->apply($builder, null, false)
			->each(function (Model_Choices_Item_Mod $model) {
				$row = Force_List_Row::factory();

				Core_Item::row_image($model, $row, 'weapon_image');
//				$model->format('list', Core_Choices_Item::get_list_name($model->list));

				return $row;
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Choices_Item_Mod::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Choices_Item_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form($model) {
		$form = Jelly_Form::factory($model, array())->preset_for_admin();

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Choices_Item_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

}