<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_Velocity
 * User: legion
 * Date: 26.09.19
 * Time: 18:22
 */
trait Controller_Common_Weapons_Velocity {

	/*
	 * MUZZLE VELOCITY
	 */

	protected static function button_velocity(Jelly_Model $model, $field, $caption = NULL) {
		if (empty($caption)) {
			$caption = $model->{$field};
		}

		$button = Force_Button::factory($caption)
			->modal('velocity_modal')
			->attribute('data-id', $model->uiIndex)
			->attribute('data-muzzle_velocity', $model->muzzle_velocity)
			->simple()
			->attribute('style', 'color:inherit')
			->link('#')
			->render();

		$model->format($field, $button);
	}

	protected function _velocity() {
		$this->_velocity_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/weapons/velocity.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'velocity'),
			Force_Form_Hidden::factory('id', 0),
			Force_Form_Float::factory('muzzle_velocity'),
		])->button_submit()
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('velocity_modal')
			->label('Настройки V0')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _velocity_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'velocity') {
			return false;
		}

		$uiIndex = $this->request->post('id');

		$model = Core_Weapon_Data::factory()->get_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!$model->loaded()) {
			return false;
		}

		$muzzle_velocity = $this->request->post('muzzle_velocity');

		$model->muzzle_velocity = empty($muzzle_velocity) ? NULL : $muzzle_velocity;

		try {
			$model->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Weapons_Velocity