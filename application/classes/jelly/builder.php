<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Builder extends Jelly_Core_Builder {

	public function apply_pagination(Pagination $pagination, $additional_offset = null) {
		$offset = $pagination->offset();
		if (is_int($additional_offset)) {
			$offset = $pagination->offset() + $additional_offset;
			if ($offset < 0) {
				$offset = 0;
			}
		}

		$this->limit($pagination->items_per_page);
		$this->offset($offset);
		return $this;
	}

	/**
	 * Aliases a field to its actual representation in the database. Meta-aliases
	 * are resolved and table-aliases are taken into account.
	 *
	 * Note that the aliased field will be returned in the format you pass it in:
	 *
	 *    model.field => table.column
	 *    field => column
	 *
	 * @param   mixed $field The field to alias, in field or model.field format
	 * @param   null  $value A value to pass to unique_key, if necessary
	 * @param   bool  $join_if_sure
	 *
	 * @return  array|mixed|string
	 */
	protected function _field_alias($field, $value = NULL, $join_if_sure = TRUE) {
		$original = $field;

		// Do nothing for Database Expressions and sub-queries
		if ($field instanceof Database_Expression OR $field instanceof Database_Query) {
			return $field;
		}

		// Alias the field(s) in FUNC("field")
		if (strpos($field, '"') !== FALSE) {
//			return preg_replace('/"(.+?)"/e', '"\\"".$this->_field_alias("$1")."\\""', $field);
			return preg_replace_callback('/"(.+?)"/', function ($m) { return $this->_field_alias($m[1]); }, $field);
		}

		// We always return fields as they came
		$join = (bool)strpos($field, '.');

		// Determine the default model
		if (!$join) {
			$model = $this->_model;
		} else {
			list($model, $field) = explode('.', $field, 2);
		}

		// Have the column default to the field
		$column = $field;

		// Alias the model
		list(, $alias, $model) = $this->_model_alias($model);

		// Expand meta-aliases
		if (strpos($field, ':') !== FALSE) {
			extract($this->_meta_alias($field, array(
				'model' => $model,
				'field' => $field,
				'value' => $value,
			)));

			$column = $field;
		}

		// Alias to the column
		if ($meta = Jelly::meta($model) AND $field_obj = $meta->field($field) AND $field_obj->in_db) {
			$column = $field_obj->column;

			// We're 99% sure adding the table name in front won't cause problems now
			$join = $join_if_sure ? TRUE : $join;
		}

		return $join ? ($alias . '.' . $column) : $column;
	}

	public function clear_order_by() {
		$this->_order_by = array();
		return $this;
	}

	public function get_selected_columns() {
		return $this->_select;
	}

	public function get_selected_aliases() {
		$aliases = array();
		foreach ($this->_select as $data) {
			if (is_array($data) && array_key_exists(1, $data)) {
				$aliases[$data[1]] = $data[1];
			}
		}

		return $aliases;
	}

}
