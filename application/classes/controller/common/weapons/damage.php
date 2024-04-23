<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_Damage
 * User: legion
 * Date: 26.09.19
 * Time: 18:20
 */
trait Controller_Common_Weapons_Damage {

	/*
	 * DAMAGE
	 */

	protected static function button_damage(Jelly_Model $model, $field, $caption = NULL) {
		if (empty($caption)) {
			$caption = $model->{$field};
		}

		$button = Force_Button::factory($caption)
			->modal('damage_modal')
			->attribute('data-calibre', $model->ubCalibre)
			->attribute('data-calibre_damage', $model->calibre_damage)
			->simple()
			->attribute('style', 'color:inherit')
			->link('#')
			->render();

		$model->format($field, $button);
	}

	protected function _damage() {
		$this->_damage_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/weapons/damage.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'damage'),
			Force_Form_Hidden::factory('calibre', 0),
			Force_Form_Float::factory('calibre_damage'),
		])->button_submit()
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('damage_modal')
			->label('Настройки урона')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _damage_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'damage') {
			return false;
		}

		$ubCalibre = $this->request->post('calibre');

		$model = Core_Calibre::factory()->get_builder()
			->where('ubCalibre', '=', $ubCalibre)
			->limit(1)
			->select();

		if (!$model->loaded()) {
			return false;
		}

		$model->damage = $this->request->post('calibre_damage');

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

} // End Controller_Common_Weapons_Damage