<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_SP4T
 * User: legion
 * Date: 26.09.19
 * Time: 18:24
 */
trait Controller_Common_Weapons_SP4T {

	/*
	 * SP4T
	 */

	protected static function button_sp4t(Jelly_Model $model, $field, $caption = NULL) {
		if (empty($caption)) {
			$caption = $model->{$field};
		}

		$calibre_semi_speed = ($model->calibre_semi_speed == 0) ? '' : $model->calibre_semi_speed;
		$calibre_burst_recoil = ($model->calibre_burst_recoil == 0) ? '' : $model->calibre_burst_recoil;
		$calibre_auto_recoil = ($model->calibre_auto_recoil == 0) ? '' : $model->calibre_auto_recoil;
		$sp4t_pistol_bonus = ($model->sp4t_pistol_bonus == 0) ? '' : $model->sp4t_pistol_bonus;
		$sp4t_mp_bonus = ($model->sp4t_mp_bonus == 0) ? '' : $model->sp4t_mp_bonus;
		$sp4t_rifle_bonus = ($model->sp4t_rifle_bonus == 0) ? '' : $model->sp4t_rifle_bonus;

		$button = Force_Button::factory($caption)
			->modal('sp4t_modal')
			->attribute('data-calibre', $model->ubCalibre)
			->attribute('data-calibre_semi_speed', $calibre_semi_speed)
			->attribute('data-calibre_burst_recoil', $calibre_burst_recoil)
			->attribute('data-calibre_auto_recoil', $calibre_auto_recoil)
			->attribute('data-sp4t_pistol_bonus', $sp4t_pistol_bonus)
			->attribute('data-sp4t_mp_bonus', $sp4t_mp_bonus)
			->attribute('data-sp4t_rifle_bonus', $sp4t_rifle_bonus)
			->simple()
			->attribute('style', 'color:inherit')
			->link('#')
			->render();

		$model->format($field, $button);
	}

	protected function _sp4t() {
		$this->_sp4t_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/weapons/sp4t.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'sp4t'),
			Force_Form_Hidden::factory('calibre', 0),
			Force_Form_Float::factory('calibre_semi_speed')->attribute('autocomplete', 'off'),
			Force_Form_Float::factory('calibre_burst_recoil')->attribute('autocomplete', 'off'),
			Force_Form_Float::factory('calibre_auto_recoil')->attribute('autocomplete', 'off'),
			Force_Form_Float::factory('sp4t_pistol_bonus')->attribute('autocomplete', 'off'),
			Force_Form_Float::factory('sp4t_mp_bonus')->attribute('autocomplete', 'off'),
			Force_Form_Float::factory('sp4t_rifle_bonus')->attribute('autocomplete', 'off'),
		])->button_submit()
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('sp4t_modal')
			->label('Настройки калибра')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _sp4t_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'sp4t') {
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

		$model->semi_speed = $this->request->post('calibre_semi_speed');
		$model->burst_recoil = $this->request->post('calibre_burst_recoil');
		$model->auto_recoil = $this->request->post('calibre_auto_recoil');
		$model->sp4t_pistol_bonus = $this->request->post('sp4t_pistol_bonus');
		$model->sp4t_mp_bonus = $this->request->post('sp4t_mp_bonus');
		$model->sp4t_rifle_bonus = $this->request->post('sp4t_rifle_bonus');

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

} // End Controller_Common_Weapons_SP4T