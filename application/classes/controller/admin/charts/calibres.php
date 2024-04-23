<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Calibres
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Calibres extends Controller_Admin_Charts_Template {

	public function action_index() {
		$builder = Core_Calibre::factory()->preset_for_admin()->get_builder()
			->order_by('bullet_start_energy')
			->where('damage', '>', 0)
			->where('auto_recoil', '>', 0);

		$filter = Force_Filter::factory(array(
			Core_Bullet::get_filter_control()
				->where('bullet_type'),
		))->apply($builder);

		Core_Bullet::check_filter_control($filter);

		$table = $builder->select_all();

		$data_old = [];
		$data_new = [];

		foreach ($table as $model) {
			/** @var Model_Calibre $model */

			$speed = Core_Calibre::calculate_bullet_speed($model);
			$energy = Core_Calibre::calculate_bullet_energy($model, $speed);
			$damage = Core_Calibre::calculate_damage($model, $energy);

			$x = $model->damage;

			$data_old[$x] = $model->auto_recoil;
			$data_new[$x] = Core_Calibre::calculate_auto_recoil($model, $damage);
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'damage');
		Helper_Assets::add_js_vars('yAxisLabel', 'auto recoil');

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

} // End Controller_Admin_Charts_Calibres