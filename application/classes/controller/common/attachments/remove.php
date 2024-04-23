<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Attachments_Remove
 * User: legion
 * Date: 26.09.19
 * Time: 18:20
 */
trait Controller_Common_Attachments_Remove {

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
	protected static function get_button_remove(Force_Button $button, $item_index, $item_name, $attach_index, $attach_name, $ap_cost) {
		$button
			->modal('remove_modal')
			->attribute('data-item_index', $item_index)
			->attribute('data-item_name', $item_name)
			->attribute('data-attach_index', $attach_index)
			->attribute('data-attach_name', $attach_name)
			->attribute('data-ap_cost', $ap_cost)
			->link('#');

		$button->btn_sm();

		return $button;
	}

	protected function _remove(Core_Common $item_data_object) {
		$this->_change_apply($item_data_object);
		$this->_remove_apply($item_data_object);
		Helper_Assets::add_scripts('/assets/ja2/js/attachments/attachments.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('item_index', 0),
			Force_Form_Hidden::factory('attach_index', 0),
			Force_Form_Input::factory('item_name', 'Item')->attribute('disabled'),
			Force_Form_Input::factory('attach_name', 'Attachment')->attribute('disabled'),
			Force_Form_Input::factory('ap_cost', 'AP Cost'),
		])->button_submit(Force_Button::factory('Сохранить')->name('action')->value('change')->btn_primary()->submit())
			->button(Force_Button::factory('Удалить')->name('action')->value('remove')->btn_danger()->submit())
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('remove_modal')
			->label('Удаление связи')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _change_apply(Core_Common $item_data_object) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'change') {
			return false;
		}

		$item_index = $this->request->post('item_index');
		$attach_index = $this->request->post('attach_index');
		$ap_cost = $this->request->post('ap_cost');

		$item_data = $item_data_object->get_builder()
			->where('uiIndex', '=', $item_index)
			->limit(1)
			->select();

		if (!$item_data->loaded()) {
			return false;
		}

		Core_Weapon_Data::set_possible_attachment($item_data, $attach_index, $ap_cost);

		$attach = Core_Attachment_Mod::set_attachment($item_index, $attach_index, $ap_cost);

		try {
			$item_data->save();
			$attach->save();
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

	protected function _remove_apply(Core_Common $item_data_object) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'remove') {
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

		Core_Weapon_Data::remove_possible_attachment($item_data, $attach_index);

		$attach = Core_Attachment_Mod::get_attachment($item_index, $attach_index);

		try {
			$item_data->save();
			$attach->delete();
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Attachments_Remove