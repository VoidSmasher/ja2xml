<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Burst
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Burst extends Controller_Admin_Charts_Template {

	public function action_index() {
		$builder = Core_Weapon_Mod::get_weapons_builder()
			->where('bBurstAP', '!=', 20);

		$filter = Force_Filter::factory(array(
			Core_Calibre::get_filter_control()
				->where('weapons_mod.ubCalibre'),
			Core_Bullet::get_filter_control()
				->where('bullet_type'),
			Force_Filter_Select::factory('weapon_type', 'Тип', Core_Weapon::get_type_list())
				->multiple()
				->multiple_reset_button()
				->where('weapons_mod.ubWeaponType'),
			Force_Filter_Input::factory('name', __('common.name'))
				->where('weapons_mod.szWeaponName', 'LIKE'),
		))->apply($builder);

		Core_Calibre::check_filter_control($filter);
		Core_Bullet::check_filter_control($filter);

		$table = $builder->select_all();

		$data_old = [];
		$data_new = [];

		foreach ($table as $model) {
			$fire_rate = Core_Weapon_Data::get_fire_rate($model);

			$x = $fire_rate;
//			$x = $model->BR_ROF;
			$data_old[$x] = $model->bBurstAP;
//			$data_old[$x] = round($model->BR_ROF / $model->bBurstAP, 3);
			$data_new[$x] = Core_Weapon::calculate_burst_ap($model);
//			$data_new[$x] = (pow($model->BR_ROF, 1.8) / 2100);
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Fire Rate');
		Helper_Assets::add_js_vars('yAxisLabel', 'Burst AP');

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

		Helper_Assets::add_scripts('assets/ja2/js/charts/duality.js');

		$this->render_chart(1200, 600, $data_old, $data_new);
	}

} // End Controller_Admin_Charts_Burst