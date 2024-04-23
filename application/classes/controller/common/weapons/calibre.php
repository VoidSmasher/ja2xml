<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Weapons_Calibre
 * User: legion
 * Date: 26.09.19
 * Time: 18:19
 */
trait Controller_Common_Weapons_Calibre {

	/*
	 * CALIBRE
	 */

	protected static function button_calibre(Model_Weapon_Group $model, $field, array $calibres) {
		$calibre = Core_Weapon_Data::get_calibre($model);
		$mag_size = Core_Weapon_Data::get_mag_size($model);
		$mag_size_list = Core_Calibre::get_mag_size_list($model->ubCalibre);

		if (array_key_exists($calibre, $calibres)) {
			$caption = $calibres[$calibre];
		} elseif (!empty($model->calibre_name)) {
			$caption = $model->calibre_name;
		} else {
			$caption = 'Unknown';
		}

		$button = Force_Button::factory($caption)
			->modal('calibre_modal')
			->attribute('data-id', $model->uiIndex)
			->attribute('data-calibre', $calibre)
			->attribute('data-mag_size', $mag_size)
			->attribute('data-mag_size_list', json_encode($mag_size_list))
			->link('')
			->btn_xs();

		if ($calibre != $model->ubCalibre) {
			$button->color_red();
		} elseif (!empty($model->ubCalibre)) {
			$button->color_blue();
		}

		$button = $button->render();

		$model->format($field, $button);
	}

	protected function _calibre($calibres) {
		$this->_calibre_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/weapons/calibre.js');

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'calibre'),
			Force_Form_Hidden::factory('id', 0),
			Force_Form_Select::factory('calibre')
				->add_option(NULL, '---')
				->add_options($calibres)
				->group_attribute('class', 'col-sm-6'),
			Force_Form_Select::factory('mag_size')
				->group_attribute('class', 'col-sm-6'),
			Force_Form_HTML::factory()->attribute('class', 'clearfix'),
		])->button_submit()
			->no_cache()
			->button(Force_Button::factory('Отмена')->modal_close());

		$this->template->modal[] = Force_Modal::factory('calibre_modal')
			->label('Настройки калибра')
			->content($form->render())
			->hide_buttons()
			->render();
	}

	protected function _calibre_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'calibre') {
			return false;
		}

		$uiIndex = $this->request->post('id');

		$weapon = Core_Weapon_Data::factory()->get_builder()
			->join('weapons_mod', 'left')->on('weapons_mod.uiIndex', '=', 'data_weapons.uiIndex')
			->where('uiIndex', '=', $uiIndex)
			->select_column('data_weapons.*')
			->select_column('weapons_mod.ubMagSize', 'ubMagSize')
			->limit(1)
			->select();

		if (!$weapon->loaded()) {
			throw new HTTP_Exception_404();
			return false;
		}

		$mag_size = $this->request->post('mag_size');
		if (!is_null($weapon->mag_size) || $mag_size != $weapon->ubMagSize) {
			$weapon->mag_size = $mag_size;
		}

		$calibre = $this->request->post('calibre');
		if (!is_null($weapon->calibre) || $calibre != $weapon->ubCalibre) {
			$weapon->calibre = $calibre;
		}

		try {
			$weapon->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Weapons_Calibre