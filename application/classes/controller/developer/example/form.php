<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Example_Form
 * User: legion
 * Date: 16.06.14
 * Time: 11:01
 */
class Controller_Developer_Example_Form extends Controller_Developer_Template {

	protected $_image_file = null;

	public function before() {
		parent::before();
		$this->template->data_spy_target = '.docs-sidebar';
	}

	public function action_index() {
		$test_select_array = [
			'ити',
			'ни',
			'сан',
			'ён',
			'го',
			'року',
			'нана',
			'хати',
			'кю',
			'дзю',
		];

		$test_selected = rand(0, count($test_select_array) - 1);

		$input_section = Force_Form_Section::factory('Input', [
			Force_Form_Input::factory('input', 'Input')
				->attributes(['placeholder' => 'пишите письма'])
				->description('Normal input'),
			Force_Form_Input::factory('input_disabled', 'Disabled')
				->attribute('disabled')
				->attributes(['placeholder' => 'Disabled input']),
			Force_Form_Alias::factory('alias', 'ALIAS', 'alias-value-1')
				->value('alias-value-2')// пример переопределения значения
				->description('Alias, keyboard input is limited by alias rules'),
		]);

		$checkboxes_section = Force_Form_Section::factory('Checkboxes', [
			Force_Form_Checkbox::factory('checkbox1'),
			Force_Form_Checkbox::factory('checkbox2')->description('Описание поля'),
			Force_Form_Radio::factory('radio', null, $test_select_array, $test_selected)
//					->horizontal()
				->description('Описание поля'),
		]);

		$time_begin = new DateTime();
		$time_end = new DateTime();
		$time_end = $time_end->modify('+5day');

		$date_section = Force_Form_Section::factory('Date', [
			Force_Form_Date::factory('date', 'Date', time())->description('Только дата'),
			Force_Form_Date::factory('datetime', 'Time', time())
				->description('Только время')
				->pick_date(false)
				->pick_time(),
			Force_Form_Date::factory('time', 'Date and Time', time())
				->description('И дата и время')
				->pick_time(),
			Force_Form_Date_Range::factory('daterange', 'Date Range')
				->default_begin($time_begin->getTimestamp())
				->default_end($time_end->getTimestamp())
				->description('Диапазон'),
		]);

		$text_area_section = Force_Form_Section::factory('Textarea', [
			Force_Form_Textarea::factory('textarea', 'Textarea', 'Какой-то текст'),
			Force_Form_Markdown::factory('markdown', 'Markdown', 'Отмаркдауненный текст. Полностью.'),
		]);

		$select_section = Force_Form_Section::factory('Select', [
			Force_Form_Select::factory('select', 'Select', $test_select_array, $test_selected),
			Force_Form_Multiselect::factory('multi-select', 'Multi-select', $test_select_array, $test_selected),
		]);

		$image_section = Force_Form_Section::factory('Select', [
			/*
			 * См. application/config/images.php
			 * image_type = avatar_large указывает главную нарезку
			 * остальные указаны во вложении 'types'.
			 * Все нарезки осуществляются рекурсивно.
			 */
			Force_Form_Image::factory('image', 'Image', 'avatar_large'),
		]);

		$data = array();

		$test_select_data = array_flip($test_select_array);

		for ($i = 0; $i < rand(5, 10); $i++) {
			$data[] = array_map(function ($v) {
				return rand(1, 10);
			}, $test_select_data);
		}

		$rest_section = Force_Form_Section::factory('HTML', [
			Force_Form_HTML::factory(Force_List::factory($test_select_array)->apply($data)->preset_simple()),
		]);

		$form = Force_Form::factory([
			$input_section,
			$checkboxes_section,
			$date_section,
			$text_area_section,
			$select_section,
			$image_section,
			$rest_section,
		])->title('Force Form Example')
			->preset_for_admin();

		$this->template->content[] = $form->render();
	}

	public function action_jelly() {
		$model = Core_User::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();

		$path = '/developer/example_form/jelly';

		$form = Jelly_Form::factory($model)->title('Jelly Form')->preset_for_admin()->auto($path, $path);

		$this->template->content[] = $form->render();
	}

	/*
	 * Использование Jelly_Form_Generator для создания формы из модели.
	 * Обработку такой формы придётся выполнять вручную.
	 */
	public function action_generate() {
		$model = $this->user;
		$form = Force_Form::factory(Jelly_Form_Generator::factory($model)->exclude_fields([
			'avatar',
			'logins',
		])->generate_controls())->preset_for_admin();

		$this->template->content[] = $form->render();
	}

	protected function _save_image($field, $name) {
		if (!array_key_exists($name, $_FILES)) {
			return false;
		}
		if (array_key_exists('image_type', $field) && !empty($field['image_type'])) {
			$filename = Helper_Image::upload($_FILES[$name], $this->_image_file, $field['image_type'], $name);
			if ($filename) {
				$this->_image_file = $filename;
			}
		}
		return true;
	}

	protected function _remove_image($field, $name) {
		$value = (boolean)Arr::get($_POST, $name, false);
		if (!$value) {
			return false;
		}
		if (array_key_exists('image_type', $field) && !empty($field['image_type'])) {
			Helper_Image::remove_image($this->_image_file, $field['image_type']);
			$this->_image_file = null;
		}
		return true;
	}

} // End Controller_Developer_Example_Form