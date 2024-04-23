<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Default_Remove
 * User: legion
 * Date: 26.09.19
 * Time: 18:20
 */
trait Controller_Common_Default_Remove {

	/*
	 * REMOVE
	 */

	/**
	 * @param Force_Button $button
	 * @param $item_index
	 * @param $item_name
	 * @param $attach_index
	 * @param $attach_name
	 * @param $ap_cost
	 * @return Force_Button
	 */
	protected static function get_button_remove_default(Force_Button $button, $item_index, $item_name, $attach_index, $attach_name) {
		$fixed_attachments = Core_Attachment_Data::get_fixed_attachments();

		if (array_key_exists($attach_index, $fixed_attachments)) {
			$button
				->btn_disabled();
		} else {
			$button
				->modal('remove_default_modal')
				->attribute('data-item_index', $item_index)
				->attribute('data-item_name', $item_name)
				->attribute('data-attach_index', $attach_index)
				->attribute('data-attach_name', $attach_name)
				->link('#');
		}

		$button->btn_sm();

		return $button;
	}

	protected function _remove_default(Core_Common $item_data_object) {
		$this->_remove_default_apply($item_data_object);

		Helper_Assets::add_scripts('/assets/ja2/js/attachments/attachments.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'remove_default'),
			Force_Form_Hidden::factory('item_index', 0),
			Force_Form_Hidden::factory('attach_index', 0),
			Force_Form_Input::factory('item_name', 'Item')->attribute('disabled'),
			Force_Form_Input::factory('attach_name', 'Attachment')->attribute('disabled'),
		])->button_submit(Force_Button::factory('Удалить')->btn_danger()->submit())
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('remove_default_modal')
			->label('Удаление связи')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _remove_default_apply(Core_Common $item_data_object) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'remove_default') {
			return false;
		}

		$item_index = $this->request->post('item_index');
		$attach_index = $this->request->post('attach_index');

		$item_data = $item_data_object->get_builder()
			->where('uiIndex', '=', $item_index)
			->limit(1)
			->select();

		if (!$item_data->loaded()) {
			return false;
		}

		$item_mod = Core_Item_Mod::factory()->get_builder()
			->where('uiIndex', '=', $item_index)
			->limit(1)
			->select();

		if (!$item_mod->loaded()) {
			return false;
		}

		Core_Weapon_Data::remove_default_attachment($item_data, $attach_index);

		$item_mod->DefaultAttachment = $item_data->default_attachments;

		try {
			$item_data->save();
			$item_mod->save();
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Default_Remove