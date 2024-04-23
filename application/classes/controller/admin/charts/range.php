<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Range
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Range extends Controller_Admin_Charts_Template {

	use Controller_Common_Weapons_Range;

	public function action_index() {
		self::_range();

		$builder = Core_Weapon_Mod::get_weapons_builder()
			->where('usRange', '>', 0)
			->where('length_barrel', '>', 0)
//			->where('ubCalibre', 'IN', [2, 8])
			->order_by('length_barrel');

		$filter = Force_Filter::factory(array(
			Core_Calibre::get_filter_control()
				->where('weapons_mod.ubCalibre'),
			Force_Filter_Select::factory('weapon_type', 'Тип', Core_Weapon::get_type_list())
				->multiple()
				->multiple_reset_button()
				->where('weapons_mod.ubWeaponType'),
			Force_Filter_Input::factory('name', __('common.name'))
				->where('weapons_mod.szWeaponName', 'LIKE'),
		))->apply($builder);

		Core_Calibre::check_filter_control($filter);

		$calibre = $filter->get_value('calibre');

		$table = $builder->select_all();

		$data_old = [];
		$data_new = [];

		/*
				function get_range($range_unmodified) {
		//			$range = atan($range_unmodified / 40 - 3.5) * 500 + 700;
					$range = pow($range_unmodified, 0.5) * 50 - 400;
					$range = (int)$range;

					if ($range < 0) {
						$range = 0;
					}

					return $range;
				}
		*/
		/*
				if (!empty($calibre)) {
					$model = $table[0];
					$bullet_weight = $model->bullet_weight;
					$bullet_diameter = $model->bullet_diameter;
					$bullet_coefficient = $model->bullet_coefficient;

					// Переводим граммы в килограммы
					$bullet_weight = $bullet_weight / 1000;

					for ($i = 0; $i < 400; $i += 10) {
						$energy = $i;
						$bullet_speed = sqrt(($energy * 2) / $bullet_weight);

						$range_unmodified = Core_Bullet::get_range($bullet_speed, $model->bullet_weight, $bullet_diameter, $bullet_coefficient);

						$data_new[$i] = get_range($range_unmodified);
		//				$data_new[$i] = $range_unmodified;
					}
				}
		*/

		foreach ($table as $model) {
			/** @var Model_Weapon_Group $model */

			$bullet_weight = $model->bullet_weight;
			$bullet_diameter = $model->bullet_diameter;
			$bullet_coefficient = $model->bullet_coefficient;
			$energy = Core_Weapon::calculate_bullet_energy($model);

//			$range_unmodified = Core_Bullet::get_range($bullet_speed, $bullet_weight, $bullet_diameter, $bullet_coefficient);

			$x = $energy;

			$data_old[$x] = $model->usRange;
//			$data_new[$x] = $model->usRange;
//			$data_new[$x] = round($range_unmodified);
//			$data_new[$x] = get_range($range_unmodified);
			$data_new[$x] = Core_Weapon::calculate_range($model);
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Energy');
		Helper_Assets::add_js_vars('yAxisLabel', 'Range');

		Helper_Assets::add_js_vars('chartOptions', array(
			'inGraphDataShow' => false,
			'datasetFill' => true,
			'pointDot' => true,
			'scaleLabel' => '<%=value%>',
//			'scaleFontSize' => 16,
//			'canvasBorders' => true,
			'legend' => true,
			'annotateDisplay' => true,
			'dynamicDisplay' => true,
			// graphTitle : "Duality",
			// graphTitleFontFamily : "'Arial'",
			// graphTitleFontSize : 24,
			// graphTitleFontStyle : "bold",
			// graphTitleFontColor : "#555",
			// yAxisUnit : "Y Unit",
		));

		$this->template->content[] = $filter->render();

		if ($calibre && $table->count() > 0) {
			$model = $table[0];
			$this->template->content[] = self::row(self::get_button_range($model, 'Range')->btn_primary()->render());
		}

		Helper_Assets::add_scripts('assets/ja2/js/charts/duality.js');

		$this->render_chart(1200, 600, $data_old, $data_new);
	}

	protected static function row($content) {
		return '<div class"row">' . $content . '</div>';
	}

} // End Controller_Admin_Charts_Range