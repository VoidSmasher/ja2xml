<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Charts_Template
 * User: legion
 * Date: 29.09.19
 * Time: 5:09
 */
class Controller_Admin_Charts_Template extends Controller_Admin_Template {

	public function before() {
		parent::before();

	}

	public function after() {

		parent::after();
	}

	protected function render_chart($canvas_width, $canvas_height, $data_old, $data_new) {
		if (!array($data_old)) {
			$data_old = array();
		}

		if (!array($data_new)) {
			$data_old = array();
		}

		if (empty($data_old) || empty($data_new)) {
			$this->template->content[] = $this->get_empty();
			return;
		}

		if (count($data_old) == 1) {
			foreach ($data_old as $x => $y) {
				$data_old[$x+1] = $y;
			}
		}
		if (count($data_new) == 1) {
			foreach ($data_new as $x => $y) {
				$data_new[$x+1] = $y;
			}
		}
		ksort($data_old);
		ksort($data_new);

		$data_old_X = array_keys($data_old);
		$data_old_Y = array_values($data_old);
		$data_new_X = array_keys($data_new);
		$data_new_Y = array_values($data_new);

		$x_min = $data_old_X[0];
		if ($x_min > $data_new_X[0]) {
			$x_min = $data_new_X[0];
		}

		$data_old_X_max = count($data_old_X) - 1;
		$data_new_X_max = count($data_new_X) - 1;
		$x_max = $data_old_X[$data_old_X_max];
		if ($x_max < $data_new_X[$data_new_X_max]) {
			$x_max = $data_new_X[$data_new_X_max];
		}

		$x_min = floor($x_min);
		$x_max = ceil($x_max);

		$labels = [];

		$x_delta = ceil(($x_max - $x_min) / 20);
		for ($i = $x_min; $i <= $x_max; $i += $x_delta) {
			$labels[] = $i;
		}

		foreach ($data_old_X as $index => $x) {
			$data_old_X[$index] -= $x_min;
		}

		foreach ($data_new_X as $index => $x) {
			$data_new_X[$index] -= $x_min;
		}

		$x_max -= $x_min;
		$x_min = 0;

		Helper_Assets::add_js_vars('labels', $labels);

		Helper_Assets::add_js_vars('xDataBegin', $x_min);
		Helper_Assets::add_js_vars('xDataEnd', $x_max);

//		Helper_Error::var_dump($x_min, 'x_min');
//		Helper_Error::var_dump($x_max, 'x_max');
//		Helper_Error::var_dump(count($data_old), 'old');
//		Helper_Error::var_dump($data_old, 'old');
//		Helper_Error::var_dump(count($data_new), 'new');
//		Helper_Error::var_dump($data_new, 'new');
//		die;

		Helper_Assets::add_js_vars('yDataOld', $data_old_Y);
		Helper_Assets::add_js_vars('xDataOld', $data_old_X);

		Helper_Assets::add_js_vars('yDataNew', $data_new_Y);
		Helper_Assets::add_js_vars('xDataNew', $data_new_X);

		Helper_Assets::add_js_vars('xTitleOld', 'old data');
		Helper_Assets::add_js_vars('xTitleNew', 'new data');

		Helper_Assets::add_before_scripts('assets/chart/Add-ins/shapesInChart.js');
		Helper_Assets::add_before_scripts('assets/chart/ChartNew.js');

		$this->template->content[] = $this->get_canvas($canvas_height, $canvas_width);
	}

	protected function get_empty() {
		return '<div class="jumbotron"><p>Нет данных</p></div>';
	}

	protected function get_canvas($canvas_height, $canvas_width) {
		return '<canvas id="canvas_line" height="' . $canvas_height . '" width="' . $canvas_width . '"></canvas>';
	}

} // End Controller_Admin_Charts_Template