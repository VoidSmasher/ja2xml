<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Translations
 * User: legion
 * Date: 14.04.17
 * Time: 20:55
 */
class Controller_Developer_Translations extends Controller_Developer_Template {

	public function action_index() {

		$ru = i18n::load('ru');
		$en = i18n::load('en');

		// default strings
		$_d = i18n::load('_d');

		foreach ($en as $_key => $_value) {
			if (!array_key_exists($_key, $ru)) {
				$ru[$_key] = $_value;
			}
		}

		ksort($ru);

		$data = array(
			'ru' => $ru,
			'en' => $en,
		);

		$_data = array();
		$previous_key = null;
		$include_key = Arr::get($_POST, 'include_key', false);

		foreach ($data as $_col => $_rows) {
			foreach ($_rows as $_row => $_value) {
				if (!array_key_exists($_row, $_d)) {
					$main_key = $_row;
					if (!Form::is_post()) {
						$_dot_pos = strpos($_row, '.');
						if ($_dot_pos !== false) {
							$main_key = substr($_row, 0, $_dot_pos);
						}

						if ($previous_key != $main_key) {
							$_data[$main_key]['key'] = Force_Form_Checkbox::factory($main_key, '<b>' . strtoupper($main_key) . '</b>')
								->attribute('checked')
								->render();
						}

						$_data[$_row]['key'] = $_row;
					} else {
						if ($include_key) {
							$_data[$_row]['key'] = $_row;
						}
					}

					$_data[$_row][$_col] = $_value;

					$previous_key = $main_key;
				}
			}
		}

		if (Form::is_post()) {
//			$filename = date('Y_m_d') . '_translation.csv';
//			header("Content-type: text/csv; charset=UTF-8");
//			header("Content-Disposition: attachment; filename=" . $filename);
//			header("Pragma: no-cache");
//			header("Expires: 0");
//			header('Content-Encoding: UTF-8');

			$header = array();
			if ($include_key) {
				$header[] = 'Key';
			}
			$header[] = __('menu.ru');
			$header[] = __('menu.en');

			$sheet = array(
				$header,
			);

			foreach ($_data as $_row => $_cols) {
				$main_key = $_row;
				$_dot_pos = strpos($_row, '.');
				if ($_dot_pos !== false) {
					$main_key = substr($_row, 0, $_dot_pos);
				}

				$main_value = Arr::get($_POST, $main_key, false);

				if ($main_value) {
					foreach ($_cols as $_index => $_col) {
						preg_replace("/<br\W*?\/>/", " ", $_col);
						$_col = strip_tags($_col);
						$_cols[$_index] = $_col;
					}

					$sheet[] = $_cols;
				}
			}

			require_once MODPATH . 'phpexcel/Classes/PHPExcel.php';

			$doc = new PHPExcel();
			$doc->setActiveSheetIndex(0);

			$doc->getActiveSheet()->fromArray($sheet, null, 'A1');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="translation.xls"');
			header('Cache-Control: max-age=0');

			// Do your stuff here
			$writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');

			$writer->save('php://output');

			exit(0);
		}

		$submit = Force_Button::factory('Скачать XLS')
			->submit()
			->color_green();

		$list = Force_List::factory();

		if (!empty($_data)) {
			$list->button($submit);
		}

		$label_key = Force_Form_Checkbox::factory('include_key', 'Key')
			->attribute('checked')
			->simple()
			->render();
//		$list->column('key')->label('Key');
		$list->column('key')->label($label_key);
		$list->column('ru')->label(__('menu.ru'));
		$list->column('en')->label(__('menu.en'));

		$list->apply_array($_data);

		$this->template->content[] = Form::open();
		$this->template->content[] = $list->render();

		if (!empty($_data)) {
			$this->template->content[] = $submit->render();
		}

		$this->template->content[] = Form::close();
	}

} // End Controller_Translations