<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_Range
 * User: legion
 * Date: 26.09.19
 * Time: 18:13
 */
trait Controller_Common_Weapons_Range {

	/*
	 * RANGE
	 */

	/**
	 * @param Jelly_Model $model
	 * @param $caption
	 * @return Force_Button
	 */
	protected static function get_button_range(Jelly_Model $model, $caption) {
		$button = Force_Button::factory($caption)
			->modal('range_modal')
			->attribute('data-calibre', $model->ubCalibre)
			->attribute('data-range_angle', $model->range_angle)
			->attribute('data-range_mult', $model->range_mult)
			->attribute('data-range_div', $model->range_div)
			->attribute('data-range_delta', $model->range_delta)
			->attribute('data-range_weapon_id', $model->range_weapon_id)
			->attribute('data-range_weapon', $model->range_weapon)
			->link('#');

		return $button;
	}

	protected static function button_range(Jelly_Model $model, $field, $caption = NULL) {
		if (empty($caption)) {
			$caption = $model->{$field};
		}

		$button = self::get_button_range($model, $caption)
			->attribute('style', 'color:inherit')
			->simple();

		$model->format($field, $button);
	}

	protected function _range() {
		$this->_range_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/weapons/range.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'range'),
			Force_Form_Hidden::factory('calibre', 0),
			Force_Form_Section::factory('', [
				Force_Form_Float::factory('range_angle')
					->group_attribute('class', 'col-sm-6'),
//				Force_Form_Input::factory('range_mult')
//					->group_attribute('class', 'col-sm-6')
//					->attribute('disabled'),
				Force_Form_Input::factory('range_mult')
					->group_attribute('class', 'col-sm-6'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),

			Force_Form_Section::factory('', [
//				Force_Form_Float::factory('range_div')
//					->group_attribute('class', 'col-sm-6'),
//				Force_Form_Float::factory('range_delta')
//					->group_attribute('class', 'col-sm-6'),
				Force_Form_Float::factory('range_delta')
					->group_attribute('class', 'col-sm-12'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),

			Force_Form_Section::factory('', [
				Force_Form_Float::factory('range_weapon_id')
					->group_attribute('class', 'col-sm-6'),
				Force_Form_Float::factory('range_weapon')
					->group_attribute('class', 'col-sm-6'),
			])->attribute('class', 'row')
				->attribute('style', 'padding:0')
				->hide_label(),
		])->button_submit()
			->no_cache()
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('range_modal')
			->label('Настройки дальности')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _range_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'range') {
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

		$model->range_angle = $this->request->post('range_angle');
		$model->range_div = $this->request->post('range_div');
//		$model->range_delta = $this->request->post('range_delta');
		$model->range_mult = $this->request->post('range_mult');

		$weapon_id = $this->request->post('range_weapon_id');
		if ($weapon_id) {
			$range = $this->request->post('range_weapon');

			$model->range_weapon_id = $weapon_id;
			$model->range_weapon = $range;

			$weapon = Core_Weapon_Mod::get_weapon($weapon_id);

			if ($weapon->loaded()) {
				$weapon->range_angle = $model->range_angle;
				$weapon->range_mult = $model->range_mult;
//				$weapon->range_delta = $model->range_delta;

				$model->range_delta = Core_Weapon::get_range_delta($weapon, $range);
//				$model->range_mult = Core_Weapon::get_range_mult($weapon, $range);
			}
		} else {
			$model->range_mult = NULL;
			$model->range_weapon_id = NULL;
			$model->range_weapon = NULL;
		}

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

} // End Controller_Common_Weapons_Range