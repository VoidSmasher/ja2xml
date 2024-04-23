<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Bullet
 * User: legion
 * Date: 16.09.19
 * Time: 15:09
 */
class Controller_Admin_Charts_Bullet extends Controller_Admin_Template {

	public function action_index() {
		$y_data_old_2H = [];
		$x_data_old_2H = [];

		// 5.45
		$y_data_old_2H[] = 0.79;
		$x_data_old_2H[] = 3.62 * 880;
		// 5.56
		$y_data_old_2H[] = 0.8;
		$x_data_old_2H[] = 4.02 * 885;
		// 7.62x39
		$y_data_old_2H[] = 0.9;
		$x_data_old_2H[] = 7.9 * 715;
		// 7.62x51
		$y_data_old_2H[] = 1.1;
		$x_data_old_2H[] = 10 * 833;
		// 7.62x54R
		$y_data_old_2H[] = 1.18;
		$x_data_old_2H[] = 11.7 * 782;
		// .300 WinMag
		$y_data_old_2H[] = 1.6;
		$x_data_old_2H[] = 12.96 * 923;
		// .338 Lapua
		$y_data_old_2H[] = 2.3;
		$x_data_old_2H[] = 16.2 * 905;

		foreach ($x_data_old_2H as $i => $x) {
			$x_data_old_2H[$i] = $x / 1000;
		}

		$y_data_new_2H = [];
		$x_data_new_2H = [];

		$x_max = 17;
		$x_min = 0;

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

		Helper_Assets::add_js_vars('yDataNew2H', $y_data_new_2H);
		Helper_Assets::add_js_vars('xDataNew2H', $x_data_new_2H);

		$defCanvasWidth = 1200;
		$defCanvasHeight = 500;

		Helper_Assets::add_scripts('assets/chart/ChartNew.js');
		Helper_Assets::add_scripts('assets/chart/Add-ins/shapesInChart.js');
		Helper_Assets::add_scripts('assets/ja2/js/charts/bullet.js');

		$this->template->content[] = '<canvas id="canvas_line" height="' . $defCanvasHeight . '" width="' . $defCanvasWidth . '"></canvas>';
	}

} // End Controller_Admin_Charts_Bullet