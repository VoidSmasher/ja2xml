<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Merges
 * User: legion
 * Date: 25.04.2021
 * Time: 10:49
 */
class Controller_Admin_Merges extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Merge_Mod::factory()->preset_for_admin()->get_builder()
			->join(['items_mod', 'first_item'], 'LEFT')->on('first_item.uiIndex', '=', 'merge_mod.firstItemIndex')
			->join(['items_mod', 'second_item'], 'LEFT')->on('second_item.uiIndex', '=', 'merge_mod.secondItemIndex')
			->join(['items_mod', 'first_result'], 'LEFT')->on('first_result.uiIndex', '=', 'merge_mod.firstResultingItemIndex')
			->join(['items_mod', 'second_result'], 'LEFT')->on('second_result.uiIndex', '=', 'merge_mod.secondResultingItemIndex')
			->select_column('merge_mod.*')
			->select_column('first_item.szLongItemName', 'firstItemName')
			->select_column('second_item.szLongItemName', 'secondItemName')
			->select_column('first_result.szLongItemName', 'firstResultingItemName')
			->select_column('second_result.szLongItemName', 'secondResultingItemName');

		$filter = Force_Filter::factory([
			Force_Filter_Input::factory('source', 'Source Item'),
			Force_Filter_Input::factory('target', 'Target Item'),
			Force_Filter_Input::factory('first_result', 'First Result'),
			Force_Filter_Input::factory('second_result', 'Second Result'),
			Force_Filter_Input::factory('type', 'Merge Type')
				->where('mergeType'),
		])->apply($builder);

		$this->combo_filter($filter, $builder, 'source', 'firstItemIndex', 'first_item.szLongItemName');
		$this->combo_filter($filter, $builder, 'target', 'secondItemIndex', 'second_item.szLongItemName');
		$this->combo_filter($filter, $builder, 'first_result', 'firstResultingItemIndex', 'first_result.szLongItemName');
		$this->combo_filter($filter, $builder, 'second_result', 'secondResultingItemIndex', 'second_result.szLongItemName');

		$list = Force_List::factory()->preset_for_admin();

		$list->column('firstItemIndex');
		$list->column('secondItemIndex');
		$list->column('firstResultingItemIndex');
		$list->column('secondResultingItemIndex');
		$list->column('mergeType');
		$list->column('APCost');
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$list->apply($builder)
			->button_add()
			->each(function (Model_Merge_Mod $model) {
				$model->format('firstItemIndex', $model->firstItemIndex . ' ' . $model->firstItemName);
				$model->format('secondItemIndex', $model->secondItemIndex . ' ' . $model->secondItemName);
				$model->format('firstResultingItemIndex', $model->firstResultingItemIndex . ' ' . $model->firstResultingItemName);
				$model->format('secondResultingItemIndex', $model->secondResultingItemIndex . ' ' . $model->secondResultingItemName);
				$model->format('mergeType', Core_Merge_Mod::get_merge_type_name($model->mergeType));
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		/** @var Model_Merge_Mod $model */
		$model = Core_Merge_Mod::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		/** @var Model_Merge_Mod $model */
		$model = Core_Merge_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Model_Merge_Mod $model) {
		$form = Jelly_Form::factory($model, array())->preset_for_admin();

		$form->control = Force_Form_Section::factory('Merge', [
			'firstItemIndex',
			'secondItemIndex',
			Force_Form_Checkbox::factory('add_mirror', 'Also add merge with swapped source and target.'),
			'firstResultingItemIndex',
			'secondResultingItemIndex',
			'mergeType',
			'APCost',
		]);

		$form->control = Force_Form_Note::factory('Merge Types', Core_Merge_Mod::get_merge_types())->alert_info();

		$form->control = Force_Form_Note::factory('Merge Types Info', [
			'USE_ITEM/USE_ITEM_HARD type merges do NOT work with the new secondResultingItemIndex. They should just ignore it completely.',
			'USE_ITEM = use one item on another (ie: cut a shirt with a knife) - retains original item',
			'USE_ITEM_HARD = use one item on another, but do a mechanics check to see if it works',
		])->alert_warning();

		$form->control = Force_Form_Note::factory('How merge works', [
			'The new secondResultingItemIndex will convert the firstItemIndex into the new item type.',
			'So it goes:',
			'source item (firstItemIndex) + target item (secondItemIndex) = new result (target) item (firstResultingItemIndex) + changed source item (secondResultingItemIndex)',
		])->alert_success();

		if ($form->is_ready_to_apply()) {
			if ($form->get_value('add_mirror') === 'on') {
				$form->no_auto_redirect();
				$form->auto();

				/** @var Model_Merge_Mod $mirror */
				$mirror = Core_Merge_Mod::factory()->create();
				$mirror->firstItemIndex = $model->secondItemIndex;
				$mirror->secondItemIndex = $model->firstItemIndex;
				$mirror->firstResultingItemIndex = $model->firstResultingItemIndex;
				$mirror->secondResultingItemIndex = $model->secondResultingItemIndex;
				$mirror->mergeType = $model->mergeType;
				$mirror->APCost = $model->APCost;

				try {
					$mirror->save();
				} catch (Jelly_Validation_Exception $e) {
					Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
					Helper_Error::add_from_jelly($this->_model, $e->errors());
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
					if (Kohana::$environment == Kohana::DEVELOPMENT) {
						Helper_Error::add($e->getMessage());
					} else {
						Helper_Error::add(__('db.error.save'));
					}
				}
				$form->redirect();
			}
		}

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Merge_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

}