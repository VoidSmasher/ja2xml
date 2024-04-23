<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Incompatible_Restore
 * User: legion
 * Date: 26.09.19
 * Time: 18:20
 */
trait Controller_Common_Incompatible_Restore {

	/*
	 * RESTORE
	 */

	protected static function render_button_restore(Force_Button $button, $item_index, $item_name, $attach_index, $attach_name) {
		return $button
			->btn_xs()
			->modal('restore_modal')
			->attribute('data-item_index', $item_index)
			->attribute('data-item_name', $item_name)
			->attribute('data-attach_index', $attach_index)
			->attribute('data-attach_name', $attach_name)
			->link('#')
			->render();
	}

	protected function _restore() {
		$this->_restore_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/attachments/incompatible.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'restore'),
			Force_Form_Hidden::factory('item_index', 0),
			Force_Form_Hidden::factory('attach_index', 0),
			Force_Form_Input::factory('item_name', 'Item')->attribute('disabled'),
			Force_Form_Input::factory('attach_name', 'Incompatible Attachment')->attribute('disabled'),
		])->button_submit(Force_Button::factory('Восстановить')->btn_primary()->submit())
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('restore_modal')
			->label('Восстановление связи')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _restore_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'restore') {
			return false;
		}

		$item_index = $this->request->post('item_index');
		$attach_index = $this->request->post('attach_index');

		try {
			$model = Core_Incompatible_Mod::factory()->get_builder()
				->where('itemIndex', '=', $item_index)
				->where('incompatibleattachmentIndex', '=', $attach_index)
				->limit(1)
				->select();

			$model->itemIndex = $item_index;
			$model->incompatibleattachmentIndex = $attach_index;

			$model->save();

			$model = Core_Incompatible_Mod::factory()->get_builder()
				->where('itemIndex', '=', $attach_index)
				->where('incompatibleattachmentIndex', '=', $item_index)
				->limit(1)
				->select();

			$model->itemIndex = $attach_index;
			$model->incompatibleattachmentIndex = $item_index;

			$model->save();

		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Incompatible_Restore