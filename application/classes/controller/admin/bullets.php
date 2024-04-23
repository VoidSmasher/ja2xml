<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Bullets
 * User: legion
 * Date: 26.09.19
 * Time: 5:20
 */
class Controller_Admin_Bullets extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Bullet::factory()->preset_for_admin()->get_builder()
			->join('calibres')->on('calibres.ubCalibre', '=', 'bullets.ubCalibre')
			->select_column('bullets.*')
			->select_column('calibres.name', 'calibre_name')
			->order_by('calibre_name')
			->order_by('bullets.bullet_name');

		$filter = Force_Filter::factory(array(
			Core_Calibre::get_filter_control()
				->where('bullets.ubCalibre'),
			Force_Filter_Input::factory('name', __('common.name'))
				->where('bullets.bullet_name', 'LIKE'),
		))->apply($builder);

		$calibre = Core_Calibre::check_filter_control($filter);

		$list = Force_List::factory()->preset_for_admin();

		$list->column('calibre_name')->label('Калибр')->col_control();
		$list->column('bullet_name')->label(__('common.name'));
		$list->column('bullet_weight')->label('Вес (г)');
		$list->column('bullet_start_speed')->label('Скорость (м/с)');
		$list->column('bullet_start_energy')->label('Энергия (Дж)');
		$list->column('test_barrel_length')->label('Тестовый ствол (мм)');
		$list->column('bullet_coefficient')->label('Баллистический коэффициент');
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$add_link = Force_URL::current_clean()->action('add');

		if (!empty($calibre)) {
			$add_link->query_param('calibre', $calibre);
		}

		$list->apply($builder)
			->button_add($add_link->get_url())
			->each(function (Jelly_Model $model) {

			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Bullet::factory()->create();

		$calibre = Request::current()->query('calibre');
		if (!empty($calibre)) {
			$calibre = Core_Calibre::factory()->get_builder()
				->where('ubCalibre', '=', $calibre)
				->limit(1)
				->select();

			if ($calibre->loaded()) {
				$model->ubCalibre = $calibre->ubCalibre;
				$model->bullet_coefficient = $calibre->bullet_coefficient;
			}
		}

		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Bullet::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model) {
		$calibres = Core_Calibre::factory()->get_list()->as_array('ubCalibre', 'name');

		$form = Jelly_Form::factory($model)->preset_for_admin();

		$form->control = Force_Form_Section::factory('Калибр', [
			Force_Form_Select::factory('ubCalibre')->label('Калибр')->add_options($calibres),
			Force_Form_Float::factory('test_barrel_length', 'Длина тестового ствола (мм)'),
			Force_Form_Float::factory('test_barrel_length_in', 'Длина тестового ствола (in)'),
		]);

		$form->control = Force_Form_Section::factory('Пуля', [
			Force_Form_Input::factory('bullet_name'),
			Force_Form_Float::factory('bullet_weight', 'Вес (г)')
				->description('Вес в граммах будет высчитан автоматически, если не указан и указан вес в гранах.'),
			Force_Form_Float::factory('bullet_weight_gran', 'Вес (гран)'),
			Force_Form_Float::factory('bullet_start_speed', 'Начальная скорость (м/с)'),
			Force_Form_Float::factory('bullet_start_energy', 'Начальная энергия (Дж)')
				->description('Начальная энергия будет высчитана автоматически, если не указана и указаны скорость и масса пули в граммах.'),
		]);

		$form->control = Force_Form_Section::factory('Дробь', [
			Force_Form_Note::factory(NULL, 'Вес * количество дроби - будет вписано в вес пули (в граммах), если он не указан.'),
			Force_Form_Float::factory('pellet_weight', 'Вес дроби (г)'),
			Force_Form_Float::factory('pellet_number', 'Количество дроби'),
		]);

		$form->control = Force_Form_Section::factory('Баллистический коэффициент', [
			Force_Form_Float::factory('bullet_coefficient', 'Коэффициент'),
			Force_Form_Float::factory('bullet_coefficient_g1', 'G1 BC'),
			Force_Form_Float::factory('bullet_coefficient_g7', 'G7 BC'),
		]);

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();

			if (empty($model->test_barrel_length) && !empty($model->test_barrel_length_in)) {
				$model->test_barrel_length = round($model->test_barrel_length_in * 25.4, 2);
			} elseif (!empty($model->test_barrel_length) && empty($model->test_barrel_length_in)) {
				$model->test_barrel_length_in = round($model->test_barrel_length / 25.4, 2);
			}

			if (empty($model->bullet_weight) && !empty($model->bullet_weight_gran)) {
				$model->bullet_weight = round($model->bullet_weight_gran / 15.432, 2);
			} elseif (!empty($model->bullet_weight) && empty($model->bullet_weight_gran)) {
				$model->bullet_weight_gran = round($model->bullet_weight * 15.432, 2);
			}

			if (empty($model->bullet_weight) && !empty($model->pellet_weight) && !empty($model->pellet_number)) {
				$model->bullet_weight = $model->pellet_weight * $model->pellet_number;
			}

			if (empty($model->bullet_start_energy) && !empty($model->bullet_start_speed) && !empty($model->bullet_weight)) {
				$model->bullet_start_energy = Core_Bullet::get_bullet_energy($model->bullet_start_speed, $model->bullet_weight);
			}

			$form->auto();
		}

		if ($model->saved()) {
			$calibre = Core_Calibre::factory()->get_builder()
				->where('ubCalibre', '=', $model->ubCalibre)
				->limit(1)
				->select();

			if ($calibre->loaded() && $calibre->bullet_id == $model->id) {
				$bullet = $model->as_array();
				foreach ($bullet as $key => $value) {
					if ($key == 'id' || $key == 'ubCalibre') {
						continue;
					}
					$calibre->{$key} = $value;
				}
				try {
					$calibre->save();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}
			}
		}

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Bullet::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

} // End Controller_Admin_Bullets