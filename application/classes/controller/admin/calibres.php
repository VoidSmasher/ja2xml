<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Calibres
 * User: legion
 * Date: 01.06.18
 * Time: 3:10
 */
class Controller_Admin_Calibres extends Controller_Admin_Template {

	use Controller_Common_List_Cell;

	public function action_index() {
		$builder = Core_Calibre::factory()->preset_for_admin()->get_builder()
			->order_by('bullet_start_energy', 'desc');

		$filter = Force_Filter::factory(array(
			Core_Calibre::get_filter_control()
				->where('ubCalibre'),
			Core_Bullet::get_filter_control()
				->where('bullet_type'),
		))->apply($builder);

		Core_Calibre::check_filter_control($filter);
		Core_Bullet::check_filter_control($filter);

		$this->_save($builder);

		$list = Force_List::factory()->preset_for_admin();

		$list->column('ubCalibre')->label('ubID');
		$list->column('bullet_type')->label('Type')->col_control();
		$list->column('name')->label('Name');
		$list->column('cartridge_name')->label('Cartridge Name');
		$list->column('bullet_name')->label('Bullet');
		$list->column('coolness_bonus')->label('Cool ness bns')->col_number();
		$list->column('coolness_new')->label('Cool ness new')->col_number();
		$list->column('coolness')->label('Cool ness old');
		$list->column('damage_new')->label('Dam new')->col_number();
		$list->column('damage')->label('Dam old');
		$list->column('bullet_weight')->label('Bullet weight');
		$list->column('bullet_start_speed_new')->label('Bullet speed new')->col_number();
		$list->column('bullet_start_speed')->label('Bullet speed old');
		$list->column('bullet_start_energy_new')->label('Energy std')->col_number();
		$list->column('bullet_start_energy')->label('Energy base');
		$list->column('test_barrel_length')->label('Barrel');
		$list->column('semi_speed_new')->label('Semi new')->col_number();
		$list->column('semi_speed')->label('Semi old')->col_number();
		$list->column('burst_recoil_new')->label('Brst new')->col_number();
		$list->column('burst_recoil')->label('Brst old');
		$list->column('auto_recoil_new')->label('Rec new')->col_number();
		$list->column('auto_recoil')->label('Rec old');

		$save = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'save'),
		])->button_submit();

		$list->button_html($save->render());

		$list->apply($builder, null, false)
			->each(function (Model_Calibre $model) {
				$row = Force_List_Row::factory();
				Bonus::clear();

				$standard_barrel_length = $this->_get_standard_barrel_length($model->bullet_type);

				$model->format('name', Force_Button::factory($model->name)
					->link(Force_URL::current_clean()
						->back_url()
						->action('edit')
						->route_param('id', $model->id)
						->get_url()));
				$model->format('bullet_type', Core_Calibre::get_type_label($model));
				$model->format('bullet_weight', number_format($model->bullet_weight, 2));

				$speed_std = Core_Calibre::calculate_bullet_speed($model, $standard_barrel_length);
				$this->cell_duo_new($row, $model, 'bullet_start_speed', $speed_std, '#337777');

				$energy_std = Core_Calibre::calculate_bullet_energy($model, $speed_std);
				$this->cell_duo_new($row, $model, 'bullet_start_energy', $energy_std, '#009900');

				$damage_std = Core_Calibre::calculate_damage($model, $energy_std);
				$this->cell_duo_new($row, $model, 'damage', $damage_std, 'red');

				$auto_recoil = Core_Calibre::calculate_auto_recoil($model, $damage_std);
				$this->cell_duo_new($row, $model, 'auto_recoil', $auto_recoil, 'red');

				$burst_recoil = Core_Calibre::calculate_burst_recoil($model, $damage_std);
				$this->cell_duo_new($row, $model, 'burst_recoil', $burst_recoil, '#3388FF');

				$semi_speed = Core_Calibre::calculate_semi_speed($model, $auto_recoil);
				$this->cell_duo_new($row, $model, 'semi_speed', $semi_speed, '#337777');
//				$model->format('semi_speed', number_format($model->semi_speed, 2));

				$range = Core_Calibre::calculate_coolness($model, $damage_std);
				$this->cell_duo_new($row, $model, 'coolness', $range, '#009900', 'coolness_bonus');

				Bonus::clear();
				return $row;
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_edit() {
		/** @var Model_Calibre $model */
		$model = Core_Calibre::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Model_Calibre $model) {
		$bullets = Core_Bullet::factory()->get_builder()
			->where('ubCalibre', '=', $model->ubCalibre)
			->order_by('bullet_start_energy')
			->select_all();

		$bullets_list = array();

		foreach ($bullets as $bullet) {
			$bullets_list[$bullet->id] = '[' . $bullet->bullet_start_energy . '] ' . $bullet->bullet_name;
		}

		$form = Jelly_Form::factory($model)->preset_for_admin();

		$form->control = Force_Form_Section::factory('Патрон', [
			Force_Form_Input::factory('name', __('common.name')),
			Force_Form_Input::factory('cartridge_name', 'Патрон'),
			Force_Form_Float::factory('cartridge_weight', 'Вес патрона (г)'),
			Force_Form_Float::factory('cartridge_length', 'Длина патрона (мм)'),
			Force_Form_Float::factory('case_length', 'Длина гильзы (мм)'),
		]);

		$form->control = Force_Form_Section::factory('Пуля', [
			Force_Form_Select::factory('bullet_type', 'Тип снаряда')
				->add_option(null, '---')
				->add_options(Core_Calibre::get_types_list()),
			Force_Form_Select::factory('bullet_id', __('common.name'), $bullets_list),
			Force_Form_Float::factory('bullet_diameter', 'Диаметр пули (мм)'),
			Force_Form_Float::factory('bullet_diameter_in', 'Диаметр пули (in)'),
		]);

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();

			if (!empty($model->bullet_id)) {
				$bullets = $bullets->as_array('id');
				$bullet = Arr::get($bullets, $model->bullet_id, []);
				foreach ($bullet as $key => $value) {
					if ($key == 'id' || $key == 'ubCalibre') {
						continue;
					}
					$model->{$key} = $value;
				}
			}

			if (empty($model->bullet_weight) && !empty($model->pellet_weight)) {
				$model->bullet_weight = $model->pellet_weight * $model->pellet_number;
			}

			if (empty($model->bullet_start_energy) && !empty($model->bullet_start_speed)) {
				$model->bullet_start_energy = Core_Calibre::calculate_bullet_energy($model, $model->bullet_start_speed);
			}
		}

		$this->template->content = $form->render();
	}

	/*
	 * SAVE
	 */

	protected function _save(Jelly_Builder $builder) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'save') {
			return false;
		}

		$data = $builder->select_all();

		foreach ($data as $model) {
			/** @var Model_Calibre $model */

			Bonus::clear();

			$standard_barrel_length = $this->_get_standard_barrel_length($model->bullet_type);

			$speed_std = Core_Calibre::calculate_bullet_speed($model, $standard_barrel_length);
			$energy_std = Core_Calibre::calculate_bullet_energy($model, $speed_std);
			$damage_std = Core_Calibre::calculate_damage($model, $energy_std);
			$model->damage = $damage_std;
			$auto_recoil = Core_Calibre::calculate_auto_recoil($model, $damage_std);
			$model->auto_recoil = $auto_recoil;
			$model->burst_recoil = Core_Calibre::calculate_burst_recoil($model, $damage_std);
			$model->semi_speed = Core_Calibre::calculate_semi_speed($model, $auto_recoil);
			$model->coolness = Core_Calibre::calculate_coolness($model, $damage_std);

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

	/*
	 * HELPERS
	 */

	protected function _get_standard_barrel_length($type) {
		switch ($type) {
			case Core_Calibre::TYPE_PISTOL:
			case Core_Calibre::TYPE_PISTOL_LONG:
			case Core_Calibre::TYPE_SHOTGUN:
			case Core_Calibre::TYPE_RIFLE:
			case Core_Calibre::TYPE_RIFLE_ADVANCED:
			case Core_Calibre::TYPE_SNIPER:
				$standard_barrel_length = 400;
				break;
			default:
				$standard_barrel_length = 0;
				break;
		}
		return $standard_barrel_length;
	}

} // End Controller_Admin_Calibres