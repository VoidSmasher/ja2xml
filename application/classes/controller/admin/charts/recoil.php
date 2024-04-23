<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Recoil
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Recoil extends Controller_Admin_Charts_Template {

	public function action_index() {
		$builder = Core_Weapon_Mod::get_weapons_builder()
			->where('bRecoilY', '>', 0);

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
			/** @var Model_Weapon_Group $model */

			$energy = Core_Weapon::calculate_bullet_energy($model);
			$recoil = Core_Weapon::calculate_recoil($model);
			$recoil2 = Core_Weapon::calculate_recoil2($model);

			$recoil_X = Core_Weapon::calculate_recoil_x($model);
			$recoil_Y = Core_Weapon::calculate_recoil_y($model);

			$x = $energy;

//			$data_old[$x] = $model->bRecoilX + $model->bRecoilY;
			$data_old[$x] = $model->bRecoilY;
			$data_old[$x] = $recoil2;
//			$data_new[$x] = $recoil_X + $recoil_Y;
			$data_new[$x] = $recoil;
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Damage');
//		Helper_Assets::add_js_vars('yAxisLabel', 'X+Y Recoil');
		Helper_Assets::add_js_vars('yAxisLabel', 'Y Recoil');

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

} // End Controller_Admin_Charts_Recoil