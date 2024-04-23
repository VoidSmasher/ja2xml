<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Jelly
 * User: legion
 * Date: 06.12.12
 * Time: 22:33
 */
class Helper_Jelly {

	public static function is_model($model, $model_class_name, $check_for_loaded = false) {
		$is_model = ($model instanceof $model_class_name);
		if ($check_for_loaded && $is_model) {
			$is_model = $model->loaded();
		}
		return $is_model;
	}

	public static function is_collection($collection, $check_for_not_empty = false) {
		$is_collection = ($collection instanceof Jelly_Collection);
		if ($check_for_not_empty && $is_collection) {
			$is_collection = ($collection->count() > 0);
		}
		return $is_collection;
	}

	public static function set_links(&$builder, $link_field) {
		$link_field_meta = $builder->meta()->field($link_field);
		if (($link_field_meta instanceof Jelly_Field_ManyToMany) || ($link_field_meta instanceof Jelly_Field_HasMany)) {
			$builder->set(array($link_field => NULL));
			$link_field_values = Arr::get($_POST, $link_field, null);
			if (is_array($link_field_values) && !empty($link_field_values)) {
				$builder->set(array($link_field => $link_field_values));
			}
		}
	}

	public static function apply_limit(&$builder, $limit) {
		if (is_int($limit) && ($limit > 0)) {
			$builder->limit($limit);
		}
	}

	public static function apply_columns(&$builder, $columns) {
		if (!empty($columns)) {
			$builder->select_column($columns);
		}
	}

	public static function apply_exclude(&$builder, $id_or_array_of_ids, $primary_key = null) {
		if (!is_null($id_or_array_of_ids)) {
			if (!empty($primary_key)) {
				if (strpos($primary_key, '.') === false) {
					$primary_key .= '.id';
				}
			} else {
				$primary_key = 'id';
			}
			if (is_array($id_or_array_of_ids)) {
				$builder->where($primary_key, 'NOT IN', $id_or_array_of_ids);
			} else {
				$builder->where($primary_key, '<>', (integer)$id_or_array_of_ids);
			}
		}
	}

	/*
	 * FILED PARAMS AND RULES
	 */

	public static function get_max_length($model, $field, $default = '') {
		return self::get_rule_value_from_jelly_model($model, $field, 'max_length', $default);
	}

	public static function get_min_length($model, $field, $default = '') {
		return self::get_rule_value_from_jelly_model($model, $field, 'min_length', $default);
	}

	public static function get_field_label($model, $field, $default = null) {
		if (is_string($model)) {
			$model = Jelly::factory($model);
		}
		if ($model instanceof Jelly_Model) {
			$result = $model->get_field_label($field, $default);
		} else {
			$result = $field;
		}
		return $result;
	}

	public static function get_field_choices($model, $field, array $remove_choices_array = null, $i18n_prefix = null) {
		if (is_string($model)) {
			$model = Jelly::factory($model);
		}

		$choices = array();
		$field = $model->meta()->field($field);
		if ($field instanceof Jelly_Field_Enum) {
			$choices = $field->choices;
		}

		if (is_array($remove_choices_array)) {
			foreach ($remove_choices_array as $choice) {
				if (array_key_exists($choice, $choices)) {
					unset($choices[$choice]);
				}
			}
		}

		if (!empty($i18n_prefix)) {
			foreach ($choices as $index => $choice) {
				$choices[$index] = __($i18n_prefix . $choice);
			}
		}

		return $choices;
	}

	public static function get_rule_value_from_jelly_model($model, $field, $param, $default = false) {
		if (is_string($model)) {
			$model = Jelly::factory($model);
		}
		if ($model instanceof Jelly_Model) {
			$rules = $model
				->meta()
				->field($field)->rules;
			$result = self::get_rule_value_from_jelly_rules($rules, $param, $default);
		} else {
			$result = $default;
		}
		return $result;
	}

	public static function get_rule_value_from_jelly_rules($rules, $param, $default = false) {
		$result = $default;
		foreach ($rules as $rule) {
			if (is_array($rule)) {
				reset($rule);
				if (current($rule) == $param) {
					$value = next($rule);
					if ($value && is_array($value)) {
						reset($value);
						$result = next($value);
					} elseif ($value && !is_array($value)) {
						$result = $value;
					} else {
						$result = TRUE;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Краткая версия var_export
	 * @param Jelly_Model $model
	 *
	 * @return string
	 */
	public static function debug_model(Jelly_Model $model) {
//		return var_export($model, true);
		$data = array(
			'_original' => $model->get_original_array(),
			'_changed' => $model->get_changed_array(),
			'_retrieved' => $model->get_retrieved_array(),
			'_unmapped' => $model->get_unmapped_array(),
			'_loaded' => $model->loaded(),
			'_saved' => $model->saved(),
		);
		$data = var_export($data, true);
		$data = substr($data, 5);
		return get_class($model) . $data;
	}

} // End Helper_Jelly
