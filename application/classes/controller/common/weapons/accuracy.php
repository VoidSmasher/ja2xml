<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_Accuracy
 * User: legion
 * Date: 26.09.19
 * Time: 18:23
 */
trait Controller_Common_Weapons_Accuracy {

	/*
	 * ACCURACY
	 */

	protected static function button_accuracy(Jelly_Model $model, $field, $caption = NULL) {
		if (empty($caption)) {
			$caption = $model->{$field};
		}

		if ($model->sniper_range_bonus_percent) {
			$sniper_range_bonus = $model->sniper_range_bonus_percent . '%';
		} else {
			$sniper_range_bonus = $model->sniper_range_bonus;
		}

		if ($model->sniper_accuracy_bonus_percent) {
			$sniper_accuracy_bonus = $model->sniper_accuracy_bonus_percent . '%';
		} else {
			$sniper_accuracy_bonus = $model->sniper_accuracy_bonus;
		}

		$button = Force_Button::factory($caption)
			->modal('accuracy_modal')
			->attribute('data-calibre', $model->ubCalibre)
			->attribute('data-accuracy_angle', $model->accuracy_angle)
			->attribute('data-accuracy_mult', $model->accuracy_mult)
			->attribute('data-accuracy_delta', $model->accuracy_delta)
			->attribute('data-accuracy_x', $model->accuracy_x)
			->attribute('data-accuracy_weapon_id', $model->accuracy_weapon_id)
			->attribute('data-accuracy_weapon', $model->accuracy_weapon)
			->attribute('data-sniper_range_bonus', $sniper_range_bonus)
			->attribute('data-sniper_accuracy_bonus', $sniper_accuracy_bonus)
			->attribute('data-velocity_mult', $model->velocity_mult)
			->simple()
			->attribute('style', 'color:inherit')
			->link('#')
			->render();

		$model->format($field, $button);
	}

	protected function _accuracy() {
		$this->_accuracy_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/weapons/accuracy.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'accuracy'),
			Force_Form_Hidden::factory('calibre', 0),
			Force_Form_Section::factory('', [
				Force_Form_Float::factory('accuracy_angle')
					->group_attribute('class', 'col-sm-6'),
				Force_Form_Float::factory('accuracy_mult')
					->group_attribute('class', 'col-sm-6'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),

			Force_Form_Section::factory('', [
				Force_Form_Float::factory('accuracy_delta')
					->group_attribute('class', 'col-sm-6')
					->attribute('disabled'),
				Force_Form_Float::factory('accuracy_x')
					->group_attribute('class', 'col-sm-6'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),

			Force_Form_Section::factory('', [
				Force_Form_Float::factory('accuracy_weapon_id')
					->group_attribute('class', 'col-sm-6'),
				Force_Form_Float::factory('accuracy_weapon')
					->group_attribute('class', 'col-sm-6'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),

			Force_Form_Section::factory('', [
				Force_Form_Float::factory('sniper_range_bonus')
					->group_attribute('class', 'col-sm-6'),
				Force_Form_Float::factory('sniper_accuracy_bonus')
					->group_attribute('class', 'col-sm-6'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),

			Force_Form_Float::factory('velocity_mult'),

//			Force_Form_Section::factory('', [
//				Force_Form_Float::factory('velocity_mult')
//					->group_attribute('class', 'col-sm-12'),
//			])->attribute('class', 'row')
//				->attribute('style', 'padding:0')
//				->hide_label(),
		])->button_submit()
			->no_cache()
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('accuracy_modal')
			->label('Настройки меткости')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _accuracy_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'accuracy') {
			return false;
		}

		$ubCalibre = $this->request->post('calibre');

		$calibre = Core_Calibre::factory()->get_builder()
			->where('ubCalibre', '=', $ubCalibre)
			->limit(1)
			->select();

		if (!$calibre->loaded()) {
			return false;
		}

		$calibre->accuracy_angle = $this->request->post('accuracy_angle');
		$calibre->accuracy_mult = $this->request->post('accuracy_mult');
		$calibre->accuracy_x = $this->request->post('accuracy_x');

		$weapon_id = $this->request->post('accuracy_weapon_id');
		if ($weapon_id) {
			$accuracy = $this->request->post('accuracy_weapon');

			$calibre->accuracy_weapon_id = $weapon_id;
			$calibre->accuracy_weapon = $accuracy;

			$weapon = Core_Weapon_Mod::get_weapon($weapon_id);

			if ($weapon->loaded()) {
				$weapon->accuracy_angle = $calibre->accuracy_angle;
				$weapon->accuracy_mult = $calibre->accuracy_mult;
				$weapon->accuracy_x = $calibre->accuracy_x;

				$calibre->accuracy_delta = Core_Weapon::get_accuracy_delta($weapon, $accuracy);
			}
		} else {
			$calibre->accuracy_delta = $this->request->post('accuracy_delta');
			$calibre->accuracy_weapon_id = NULL;
			$calibre->accuracy_weapon = NULL;
		}

		$sniper_range_bonus = $this->request->post('sniper_range_bonus');
		if (strpos($sniper_range_bonus, '%')) {
			$calibre->sniper_range_bonus_percent = intval($sniper_range_bonus);
			$calibre->sniper_range_bonus = 0;
		} else {
			$calibre->sniper_range_bonus_percent = 0;
			$calibre->sniper_range_bonus = $sniper_range_bonus;
		}

		$sniper_accuracy_bonus = $this->request->post('sniper_accuracy_bonus');
		if (strpos($sniper_accuracy_bonus, '%')) {
			$calibre->sniper_accuracy_bonus_percent = intval($sniper_accuracy_bonus);
			$calibre->sniper_accuracy_bonus = 0;
		} else {
			$calibre->sniper_accuracy_bonus_percent = 0;
			$calibre->sniper_accuracy_bonus = $sniper_accuracy_bonus;
		}

		$calibre->velocity_mult = $this->request->post('velocity_mult');

		try {
			$calibre->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Weapons_Accuracy