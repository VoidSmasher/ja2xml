<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper
 * User: legion
 * Date: 04.01.2020
 * Time: 1:49
 */
class Helper {

	/**
	 * @param Jelly_Model $model
	 * @param $field
	 * @return array
	 */
	public static function get_json_as_array(Jelly_Model $model, $field) {
		if (empty($model->{$field})) {
			return array();
		}
		$field_key_value = $field . '_key_value';
		if (empty($model->{$field_key_value}) || !is_array($model->{$field_key_value})) {
			$values = json_decode($model->{$field}, true);
			if (!is_array($values)) {
				$values = array();
			}
			$list = array();
			foreach ($values as $value) {
				$list[$value] = $value;
			}
			$model->{$field_key_value} = $list;
		}
		return $model->{$field_key_value};
	}

	public static function round_to_five($value, $min = 0) {
		$value = round(round($value * 2 / 10) / 2 * 10);
		if ($min !== false) {
			if ($value < $min) {
				$value = $min;
			}
		}
		if ($value == 0) {
			$value = NULL;
		}
		return $value;
	}

	public static function set_label_color(Force_Label $label, array $params) {
		$color = array_key_exists('color', $params) ? $params['color'] : 'gray';
		switch ($color) {
			case 'red':
				$label->color_red();
				break;
			case 'yellow':
				$label->color_yellow();
				break;
			case 'green':
				$label->color_green();
				break;
			case 'cyan':
				$label->color_cyan();
				break;
			case 'blue':
				$label->color_blue();
				break;
		}
	}

	/*
	 * BONUSES
	 */
	/**
	 * @param string $field
	 * @return string
	 */
	public static function get_bonus_caption($field) {
		return str_replace('_', ' ', $field);
	}

	/**
	 * @param array $bonuses
	 * @param array $bonus_values
	 * @param null $section_name
	 * @param null $name_prefix
	 * @return Force_Form_Section
	 */
	public static function form_checkboxes(array $bonuses, array $bonus_values, $section_name = null, $name_prefix = null) {
		if (empty($section_name)) {
			$section_name = '';
		}

		$bonus_section = Force_Form_Section::factory($section_name)
			->attribute('class', 'attachment_bonuses');

		if (empty($section_name)) {
			$bonus_section
				->hide_label()
				->attribute('class', 'row')
				->attribute('style', 'padding:0');
		}

		$cols = 4;

		$col_sm = floor(12 / $cols);

		if (empty($bonuses)) {
			return $bonus_section;
		}

		$bonuses_in_row = ceil(count($bonuses) / $cols);

		$col = 1;
		$bonus_number = 1;

		$section = Force_Form_Section::factory('', [])
			->attribute('class', 'col-sm-' . $col_sm)
			->hide_label();

		foreach ($bonuses as $bonus => $label) {
			if ($bonus_number > $bonuses_in_row && $col < $cols) {
				$bonus_section->control = $section;
				$col++;
				$bonus_number = 1;
				$section = Force_Form_Section::factory('', [])->attribute('class', 'col-sm-' . $col_sm)
					->hide_label();
			}

			$section->control = Force_Form_Checkbox::factory($name_prefix . $bonus)
				->value(array_key_exists($bonus, $bonus_values))
				->label(Helper::get_bonus_caption($bonus));

			$bonus_number++;
		}

		$bonus_section->control = $section;

		return $bonus_section;
	}

} // End Helper

function d($value, $caption = null) {
	Helper_Error::var_dump($value, $caption);
}

function dd($value, $caption = null) {
	Helper_Error::var_dump($value, $caption);
	exit(0);
}
