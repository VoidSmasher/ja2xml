<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 08.05.2021
 * Time: 14:11
 */
trait Controller_Common_List_Filter {

	public function combo_filter(Force_Filter $filter, Jelly_Builder $builder, $name, $field_index, $field_name) {
		$values = $filter->get_value($name);

		$index_values = [];

		if ($values) {
			$values = explode(',', $values);
			foreach ($values as $key => $value) {
				if (is_numeric($value)) {
					$index_values[] = intval($value);
				} else {
					$builder->where($field_name, 'LIKE', '%' . strval($value) . '%');
				}
			}
			if ($index_values) {
				$builder->where($field_index, 'IN', $index_values);
			}
		}
	}

} // End Controller_Common_List_Filter
