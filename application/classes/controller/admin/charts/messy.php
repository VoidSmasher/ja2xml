<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Messy
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Messy extends Controller_Admin_Charts_Template {

	use Controller_Common_Weapons_Range;

	public function action_index() {
		self::_range();

		$builder = Core_Weapon_Mod::get_weapons_builder()
			->where('usRange', '>', 0)
			->where('length_barrel', '>', 0)
//			->where('ubCalibre', 'IN', [2, 8])
			->order_by('usRange');

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
			/** @var Model_Weapon_Group $model */

			Bonus::clear();

			$bullet_speed = Core_Calibre::calculate_bullet_speed($model, $model->length_barrel);
			$range = Core_Weapon::calculate_range($model);
			$accuracy = Core_Weapon::calculate_accuracy($model);

			$x = $range;

			$data_old[$x] = $model->MaxDistForMessyDeath;
			$data_new[$x] = Core_Weapon::calculate_messy_range($model);
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Range');
		Helper_Assets::add_js_vars('yAxisLabel', 'MaxDistForMessyDeath');

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

} // End Controller_Admin_Charts_Messy