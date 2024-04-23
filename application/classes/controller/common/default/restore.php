<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Default_Restore
 * User: legion
 * Date: 26.09.19
 * Time: 18:20
 */
trait Controller_Common_Default_Restore {

	/*
	 * RESTORE
	 */

	protected static function render_button_restore_default($item_index, $item_name, $attach_index, $attach_name) {
		$button = Force_Button::factory($attach_name);
		$button->btn_danger();

		return $button
			->modal('restore_default_modal')
			->attribute('data-item_index', $item_index)
			->attribute('data-item_name', $item_name)
			->attribute('data-attach_index', $attach_index)
			->attribute('data-attach_name', $attach_name)
			->btn_sm()
			->link('#')
			->render();
	}

	protected function _restore_default(Core_Common $item_data_object) {
		$this->_restore_default_apply($item_data_object);

		Helper_Assets::add_scripts('/assets/ja2/js/attachments/attachments.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'restore_default'),
			Force_Form_Hidden::factory('item_index', 0),
			Force_Form_Hidden::factory('attach_index', 0),
			Force_Form_Input::factory('item_name', 'Item')->attribute('disabled'),
			Force_Form_Input::factory('attach_name', 'Attachment')->attribute('disabled'),
		])->button_submit(Force_Button::factory('Восстановить')->btn_primary()->submit())
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('restore_default_modal')
			->label('Восстановление связи')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _restore_default_apply(Core_Common $item_data_object) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'restore_default') {
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

		Core_Weapon_Data::set_default_attachment($item_data, $attach_index);

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

} // End Controller_Common_Default_Restore