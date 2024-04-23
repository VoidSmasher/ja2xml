<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Accuracy
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Accuracy extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Weapon_Mod::get_weapons_builder()
			->where('usRange', '>', 0)
			->where('nAccuracy', '>', 0)
			->order_by('usRange');

		$table = $builder->select_all();

		$y_data_old_2H = [];
		$x_data_old_2H = [];
		$y_data_old_1H = [];
		$x_data_old_1H = [];

		// Рассматриваемый диапазон
		$x_min = 0;
		$x_max = 250;

		foreach ($table as $model) {
			if ($model->usRange <= $x_max) {
				if ($model->TwoHanded) {
					$y_data_old_2H[] = $model->nAccuracy;
					$x_data_old_2H[] = $model->usRange;
				} else {
					$y_data_old_1H[] = $model->nAccuracy;
					$x_data_old_1H[] = $model->usRange;
				}
			}
		}

		$y_data_new_2H = [];
		$x_data_new_2H = [];

		for ($x = 0; $x <= $x_max; $x++) {
			$x_data_new_2H[] = $x;
			$y_data_new_2H[] = Core_Weapon::get_accuracy_unmodified($x);
		}

		Helper_Assets::add_js_vars('xAxisLabel', 'Range');
		Helper_Assets::add_js_vars('yAxisLabel', 'Accuracy');

		$labels = [];

		$x_min = floor($x_min);
		$x_max = ceil($x_max);

		$x_delta = ceil(($x_max - $x_min) / 20);
		for ($i = $x_min; $i <= $x_max; $i += $x_delta) {
			$labels[] = $i;
		}

		Helper_Assets::add_js_vars('labels', $labels);
		Helper_Assets::add_js_vars('xDataBegin', $x_min);
		Helper_Assets::add_js_vars('xDataEnd', $x_max);

		Helper_Assets::add_js_vars('yDataOld2H', $y_data_old_2H);
		Helper_Assets::add_js_vars('xDataOld2H', $x_data_old_2H);

		Helper_Assets::add_js_vars('yDataOld1H', $y_data_old_1H);
		Helper_Assets::add_js_vars('xDataOld1H', $x_data_old_1H);

		Helper_Assets::add_js_vars('yDataNew2H', $y_data_new_2H);
		Helper_Assets::add_js_vars('xDataNew2H', $x_data_new_2H);

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

		$defCanvasWidth = 1200;
		$defCanvasHeight = 600;

		Helper_Assets::add_scripts('assets/chart/ChartNew.js');
		Helper_Assets::add_scripts('assets/chart/Add-ins/shapesInChart.js');
		Helper_Assets::add_scripts('assets/ja2/js/charts/accuracy.js');

		$this->template->content[] = '<canvas id="canvas_line" height="' . $defCanvasHeight . '" width="' . $defCanvasWidth . '"></canvas>';
	}

} // End Controller_Admin_Charts_Accuracy