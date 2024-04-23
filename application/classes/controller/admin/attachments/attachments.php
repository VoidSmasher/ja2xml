<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Attachments_Attachments
 * User: legion
 * Date: 17.10.19
 * Time: 8:16
 */
class Controller_Admin_Attachments_Attachments extends Controller_Admin_Template {

	use Controller_Common_Attachments_Attachments;
	use Controller_Common_Attachments_Remove;
	use Controller_Common_Attachments_Restore;
	use Controller_Common_Default_Remove;
	use Controller_Common_Default_Restore;
	use Controller_Common_Weapons_Data;

	public function action_index() {
		$item_data_object = Core_Attachment_Data::factory();
		$list_of_weapons = false;

		$this->_remove($item_data_object);
		$this->_remove_default($item_data_object);
		$this->_restore($item_data_object);
		$this->_restore_default($item_data_object);
		$this->_weapon_data();

		$mounts_list = Attachment::instance()->get_mount_list();

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('id'),
			Force_Filter_Select::factory('type', 'Type', Attachment::get_types_menu())
				->where('data_attachments.attachment_types', 'LIKE'),
			Force_Filter_Input::factory('item_name')
				->where('items_mod.szItemNameLong', 'LIKE'),
			Force_Filter_Select::factory('mounts', 'Mounts', $mounts_list)
				->where('data_attachments.attachment_mounts_external', 'LIKE'),
			Force_Filter_Select::factory('comparison', 'Сравнить с', [
				0 => 'ORIGINAL',
				1 => 'MOD',
			], false)->default_value(0),
		));

		$compare_with_original = !$filter->get_value('comparison');

		$builder_item_mod = Core_Item_Mod::get_attachments_builder();
		$builder_item_data = Core_Attachment_Data::get_attachments_builder(false);
		$builder_attach = Core_Attachment_Mod::get_attachments_builder(false);
		$builder_attach_mod = Core_Attachment_Mod::get_attachments_builder(true);

		$builder_item_mod->order_by('szLongItemName');
		$builder_item_mod->order_by('uiIndex');

		$filter
			->apply($builder_item_mod)
			->apply($builder_item_data)
			->apply($builder_attach)
			->apply($builder_attach_mod);

		Core_Item::apply_filter_by_id($filter, $builder_item_mod);
		Core_Item::apply_filter_by_id($filter, $builder_item_data);
		Core_Item::apply_filter_by_id($filter, $builder_attach, 'itemIndex');
		Core_Item::apply_filter_by_id($filter, $builder_attach_mod, 'itemIndex');

		/*
		$filter_attachment = $filter->get_value('attachment');
		if ($filter_attachment && ($filter_attachment != '---')) {
			$builder_item
				->where_open()
				->where('default_attachments', 'LIKE', '%' . $filter_attachment . '%')
				->or_where('DefaultAttachment', 'LIKE', '%' . $filter_attachment . '%')
				->or_where('possible_attachments', 'LIKE', '%' . $filter_attachment . '%')
				->where_close();
			$builder_attach->where('attachmentIndex', '=', $filter_attachment);
			$builder_attach_mod->where('attachmentIndex', '=', $filter_attachment);
		}
		*/

		$collection_item_mod = $builder_item_mod->select_all();
		$collection_item_data = $builder_item_data->select_all();
		$collection_attach = $builder_attach->select_all();
		$collection_attach_mod = $builder_attach_mod->select_all();

		$list = $this->_list(
			$collection_item_mod,
			$collection_item_data,
			$collection_attach,
			$collection_attach_mod,
			$compare_with_original,
			$list_of_weapons
		);

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_append() {
		$item_id = $this->request->param('id');
		if (empty($item_id)) {
			throw new HTTP_Exception_404;
		}

		$item_data = Core_Attachment_Data::get_attachments_builder()
			->where('uiIndex', '=', $item_id)
			->limit(1)
			->select();

		if (!$item_data->loaded()) {
			throw new HTTP_Exception_404;
		}

		$this->_form($item_data);
	}

	public function action_default() {
		$item_id = $this->request->param('id');

		$item_data = Core_Attachment_Data::get_attachments_builder()
			->where('uiIndex', '=', $item_id)
			->limit(1)
			->select();

		if (!$item_data->loaded()) {
			throw new HTTP_Exception_404;
		}

		$item_mod = Core_Item_Mod::factory()->get_builder()
			->where('uiIndex', '=', $item_id)
			->limit(1)
			->select();

		if (!$item_mod->loaded()) {
			throw new HTTP_Exception_404;
		}

		$this->_form_default($item_data, $item_mod);
	}

} // End Controller_Admin_Attachments_Attachments