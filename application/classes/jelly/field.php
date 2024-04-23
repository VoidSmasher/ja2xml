<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Jelly_Field
 * User: legion
 * Date: 04.08.15
 * Time: 18:01
 */
abstract class Jelly_Field extends Jelly_Core_Field {

	public $editable = NULL;

	public $description = NULL;

	public function _is_unique(Validation $data, Jelly_Model $model, $field, $value) {
		// According to the SQL standard NULL is not checked by the unique constraint
		// We also skip this test if the value is the same as the default value
		if ($value !== NULL AND $value !== $this->default) {
			// Primary (ID) Field
			$primary_key = $model->meta()->primary_key();
			$primary_value = $model->get($primary_key);

			// Build query
			$query = Jelly::query($model)->where($field, '=', $value);

			if (!is_null($primary_value)) {
				$query->where($primary_key, '!=', $primary_value);
			}

			// Limit to one
			$query->limit(1);

			if ($query->count()) {
				// Add error if duplicate found
				$data->error($field, 'unique');
			}
		}
	}

} // End Jelly_Field
