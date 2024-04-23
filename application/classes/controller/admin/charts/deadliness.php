<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Deadliness
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Deadliness extends Controller_Admin_Charts_Template {

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

		$attachment_models = Core_Attachment_Data::get_attachments_list_of_models(true);

		foreach ($table as $model) {
			/** @var Model_Weapon_Group $model */
			$damage = Core_Weapon::calculate_damage($model);
			$range = Core_Weapon::calculate_range($model);
			$accuracy = Core_Weapon::calculate_accuracy($model);
			$ready = Core_Weapon::calculate_ready($model);

			$sp4t = Core_Weapon::calculate_sp4t($model);

			$recoil_x = Core_Weapon::calculate_recoil_x($model);
			$recoil_y = Core_Weapon::calculate_recoil_y($model);

			$deadliness = Core_Weapon::calculate_deadliness($model, $attachment_models);

			$x = $damage+$accuracy+$range+$ready+$sp4t+$recoil_x+$recoil_y;
			$x += $model->uiIndex;
//			$x = number_format($x, 3, '.', '');

			$data_old[$x] = $model->ubDeadliness;
			$data_new[$x] = $deadliness;
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Damage + Accuracy + Range + Ready + SP4T + Recoil_X + Recoil_Y');
		Helper_Assets::add_js_vars('yAxisLabel', 'Deadliness');

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

} // End Controller_Admin_Charts_Deadliness