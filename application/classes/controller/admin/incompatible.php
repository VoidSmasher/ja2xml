<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Incompatible
 * User: legion
 * Date: 17.10.19
 * Time: 8:16
 */
class Controller_Admin_Incompatible extends Controller_Admin_Template {

	use Controller_Common_Incompatible_Remove;
	use Controller_Common_Incompatible_Restore;

	public function action_index() {
		$this->_remove();
		$this->_restore();

		$builder = Core_Incompatible::factory()->preset_for_admin()->get_builder()
			->join(array('items_mod', 'items_from'), 'LEFT')->on('items_from.uiIndex', '=', 'incompatible.itemIndex')
			->join(array('items_mod', 'items_to'), 'LEFT')->on('items_to.uiIndex', '=', 'incompatible.incompatibleattachmentIndex')
			->select_column('incompatible.*')
			->select_column('items_from.szLongItemName', 'name_from')
			->select_column('items_to.szLongItemName', 'name_to');

		$builder_mod = Core_Incompatible_Mod::factory()->preset_for_admin()->get_builder()
			->join(array('items_mod', 'items_from'), 'LEFT')->on('items_from.uiIndex', '=', 'incompatible_mod.itemIndex')
			->join(array('items_mod', 'items_to'), 'LEFT')->on('items_to.uiIndex', '=', 'incompatible_mod.incompatibleattachmentIndex')
			->select_column('incompatible_mod.*')
			->select_column('items_from.szLongItemName', 'name_from')
			->select_column('items_to.szLongItemName', 'name_to')
			->order_by('name_from');

		$attachments = Core_Attachment_Data::get_attachments_list();

		$filter = Force_Filter::factory(array(
			Force_Filter_Select::factory('attachment', 'Attachment', $attachments)
				->where([
					'itemIndex',
					'incompatibleattachmentIndex',
				]),
		))->apply($builder)->apply($builder_mod);

		$attachment = $filter->get_value('attachment');

		$collection = $builder->select_all();
		$collection_mod = $builder_mod->select_all();

		$data = array();
		$data_mod = array();

		$list = Force_List::factory()->preset_for_admin()
			->title('Incompatible Attachments');

		$list->column('line')->label('#')->col_number();
		$list->column('itemIndex')->label('uiIndex')->col_control();
		$list->column('name_from')->label('name')->col_no_wrap();

		foreach ($collection as $model) {
			$item_index = $model->itemIndex;
			$item_name = $model->name_from;
			$attach_index = $model->incompatibleattachmentIndex;
			$attach_name = $model->name_to;

			$data_mod[$item_index]['itemIndex'] = $item_index;
			$data_mod[$item_index]['name_from'] = $item_name;
			$data[$item_index][$attach_index] = $attach_name;
		}

		foreach ($collection_mod as $model) {
			$item_index = $model->itemIndex;
			$item_name = $model->name_from;
			$attach_index = $model->incompatibleattachmentIndex;
			$attach_name = $model->name_to;

			$data_mod[$item_index]['itemIndex'] = $item_index;
			$data_mod[$item_index]['name_from'] = $item_name;

			$button = Force_Button::factory($attach_name);

			if (!array_key_exists($item_index, $data) || !array_key_exists($attach_index, $data[$item_index])) {
				$button->btn_success();
			}

			$data_mod[$item_index][$attach_index] = self::render_button_remove($button, $item_index, $item_name, $attach_index, $attach_name);

			$list->column($attach_index)->label($attach_name)->col_no_wrap();
		}

		foreach ($data as $item_index => $field_data) {
			if (!array_key_exists($item_index, $data_mod)) {
				$data_mod[$item_index]['itemIndex'] = $data[$item_index]['itemIndex'];
				$data_mod[$item_index]['name_from'] = $data[$item_index]['name_from'];
			}
			$item_name = $data_mod[$item_index]['name_from'];
			foreach ($field_data as $attach_index => $attach_name) {
				switch ($attach_index) {
					case 'itemIndex':
					case 'name_from':
						continue;
				}
				if (!array_key_exists($attach_index, $data_mod[$item_index])) {
					$button = Force_Button::factory($attach_name);
					$button->btn_danger();

					$data_mod[$item_index][$attach_index] = self::render_button_restore($button, $item_index, $item_name, $attach_index, $attach_name);

					$list->column($attach_index)->label($attach_name)->col_no_wrap();
				}
			}
		}

		$add_link = Force_URL::current_clean()->action('add');

		if (!empty($attachment)) {
			$add_link->query_param('attachment', $attachment);
		}

		$list->apply($data_mod, null, false)
			->button_add($add_link->get_url())
			->each(function (array &$data) {
				$data['line'] = $data[Force_List::ROW_NUMBER];

				$item_index = $data['itemIndex'];

				$item_link = Force_URL::current()
					->query_param('attachment', $item_index)
					->get_url();

				$data['itemIndex'] = Force_Button::factory($item_index)->link($item_link)->btn_xs()->render();
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		/** @var Model_Incompatible_Mod $model */
		$model = Core_Incompatible_Mod::factory()->create();

		if ($attachment = Request::current()->query('attachment')) {
			/** @var Model_Attachment_Data $data */
			$data = Core_Attachment_Data::factory()->get_builder()
				->where('uiIndex', '=', $attachment)
				->limit(1)
				->select();

			if ($data->loaded()) {
				$model->itemIndex = $data->uiIndex;
			}
		}

		$this->_form($model);
	}

	public function action_edit() {
		/** @var Model_Incompatible_Mod $model */
		$model = Core_Incompatible_Mod::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Model_Incompatible_Mod $model) {

		$attachments = Core_Attachment_Data::get_attachments_list();

		$exclude_ids = Core_Incompatible_Mod::factory()->get_builder()
			->where('itemIndex', '=', $model->itemIndex)
			->select_all()
			->as_array('incompatibleattachmentIndex', 'incompatibleattachmentIndex');

		foreach ($exclude_ids as $exclude_id) {
			if (array_key_exists($exclude_id, $attachments)) {
				unset($attachments[$exclude_id]);
			}
		}

		$form = Jelly_Form::factory($model)->preset_for_admin();

		$form->control = Force_Form_Select::factory('itemIndex')
			->label('Item')
			->add_options($attachments);

		if (array_key_exists($model->itemIndex, $attachments)) {
			unset($attachments[$model->itemIndex]);
		}

		$form->control = Force_Form_Select::factory('incompatibleattachmentIndex')
			->label('Incompatible Attachment')
			->add_options($attachments);

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();

			if ($model->itemIndex == $model->incompatibleattachmentIndex) {
				Helper_Error::add('Item cannot be incompatible with itself');
			}

			$form->save();
		}

		if ($model->saved()) {
			$pair = Core_Incompatible_Mod::factory()->get_builder()
				->where('itemIndex', '=', $model->incompatibleattachmentIndex)
				->where('incompatibleattachmentIndex', '=', $model->itemIndex)
				->limit(1)
				->select();

			$pair->itemIndex = $model->incompatibleattachmentIndex;
			$pair->incompatibleattachmentIndex = $model->itemIndex;

			try {
				$pair->save();
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
		}

		$this->template->content = $form->render();
	}

} // End Controller_Admin_Incompatible