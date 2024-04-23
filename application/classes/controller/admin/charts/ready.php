<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Ready
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Ready extends Controller_Admin_Charts_Template {

	public function action_index() {
		$builder = Core_Weapon_Mod::get_weapons_builder()
			->where('length_max', '>', 0);

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

		$table = $builder->select_all();

		$data_old = [];
		$data_new = [];

		foreach ($table as $model) {
			$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);
			$weight = Core_Weapon_Data::get_weight($model);
			$length = $model->length_max;

			$ready_weight = Core_Weapon::get_ready_weight($model, $weight_front_percent);
			$ready_weight_front = Core_Weapon::get_ready_weight_front($model, $weight_front_percent);
			$ready_length = Core_Weapon::get_ready_length($model, $weight_front_percent);

			$ready = $length + ($weight * 10);
//			$ready = $weight * 10;
//			$ready = $length;

			$x = $ready;

			$data_old[$x] = $model->ubReadyTime;
//			$data_new[$x] = $ready_length;
//			$data_old[$x] = $ready_weight;
//			$data_old[$x] = $ready_weight_front;
			$data_new[$x] = Core_Weapon::calculate_ready($model);
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Weight + Length');
		Helper_Assets::add_js_vars('yAxisLabel', 'Ready');

		Helper_Assets::add_js_vars('chartOptions', array(
			'inGraphDataShow' => false,
			'datasetFill' => true,
			'pointDot' => false,
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

		Helper_Assets::add_scripts('assets/ja2/js/charts/duality.js');

		$this->render_chart(1200, 600, $data_old, $data_new);
	}

} // End Controller_Admin_Charts_Ready