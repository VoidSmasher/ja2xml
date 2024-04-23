<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Localization
 * User: legion
 * Date: 15.01.15
 * Time: 18:38
 */
class Controller_Developer_Localization extends Controller_Developer_Template {

	public function action_index() {
//		$token = fopen(APPPATH . '/i18n/ru.php', 'w')
//		$data =

		$columns = array(
			'key',
			'ru',
			'en',
		);

		$values = array();
		foreach ($columns as $column) {
			$values[$column] = null;
		}

		function row(&$data, $key, $lang, $value) {
			global $values;
			if (!array_key_exists($key, $data)) {
				$data[$key] = $values;
			}
			$data[$key]['key'] = $key;
			$data[$key][$lang] = $value;
		}

		$data_ru = i18n::load('ru-custom');
		$data_en = i18n::load('en-custom');
		$data = array();
		foreach ($data_ru as $key => $value) {
			row($data, $key, 'ru', $value);
		}
		foreach ($data_en as $key => $value) {
			row($data, $key, 'en', $value);
		}

		$list = Force_List::factory($columns)->apply($data)->attribute('id', 'locales')
			->button(Force_Button::factory('show modal')
				->attribute('data-toggle', 'modal')
				->attribute('data-target', '#modal_locale'));

		$form = Force_Form::factory(array(
			Force_Form_Input::factory('lang'),
		))->hide_buttons();

		Helper_Assets::add_script_embedded("
			$('#locales td').click(function(){
				$('#modal_locale').modal({
					keyboard: true
				});
			});
		");

		$this->template->content = $list->render();
		$this->template->modal = Force_Modal::factory('modal_locale')
			->label(__('common.edit.title'))
			->content($form->render())
			->render();
	}

	public static function set_params($params) {
		$filename = APPPATH . 'config/' . self::$filename . '.php';

		$file_source = "<?php defined('SYSPATH') or die('Access denied.');\n\n";

		$file_source .= "return array(\n";

		foreach ($params as $key => $value) {
			$file_source .= "\t'{$key}' => '{$value}',\n";
		}

		$file_source .= ");\n";

		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'w')) {
				return false;
			}

			if (fwrite($handle, $file_source) === FALSE) {
				return false;
			}

			fclose($handle);

		} else {
			return false;
		}

		return true;
	}

} // End Controller_Developer_Localization