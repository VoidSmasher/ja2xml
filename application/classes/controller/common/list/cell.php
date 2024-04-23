<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_List_Cell
 * User: legion
 * Date: 19.11.19
 * Time: 9:47
 */
trait Controller_Common_List_Cell {

	protected function cell_one(Force_List_Row $row, Jelly_Model $model, $field, $value = NULL, $caption = NULL, $color = NULL) {
		$popover_position = 'top';

		$field_cell = $row->cell($field);

		if (!empty($color)) {
			$field_cell->attribute('style', 'color:' . $color);
		}

		if (is_null($value)) {
			$value = $model->{$field};
		}

		if (!empty($model->{$field})) {
			$field_meta = $model->meta()->field($field);
			if ($field_meta instanceof Jelly_Field) {
				$description = $field_meta->description;
				$label = $field_meta->label;
			} else {
				$description = NULL;
				$label = $field;
			}

			if ($label != $field) {
				$caption = $label;
			}

			$description = nl2br($description);

			$bonus = Bonus::instance($field);
			$bonus_data = $bonus->get_bonus_data();

			if (!empty($description) && !empty($bonus_data)) {
				$description .= '<br/><br/>';
			}

			$description .= $bonus_data;

			$field_cell->popover($caption, $description, $popover_position);
		}

		$model->format($field, $value);
	}

	protected function cell_duo_new(Force_List_Row $row, Jelly_Model $model, $field, $new_value, $color = NULL, $bonus_field = NULL) {
		$old_field = $field;
		$new_field = $field . '_new';

		if ($bonus_field === true) {
			$bonus_field = $field . '_bonus';
		}

		$bonus = Bonus::instance($field);

		$this->cell_duo($row, $model, $bonus, $old_field, $new_field, $new_value, $color, $bonus_field);
	}

	protected function cell_duo_original(Force_List_Row $row, Jelly_Model $model, $field, $color = NULL, $bonus_field = NULL) {
		$old_field = $field . '_original';
		$new_field = $field;

		if ($bonus_field === true) {
			$bonus_field = $field . '_bonus';
		}

		$new_value = $model->{$new_field};

		$bonus = Bonus::instance($field);

		$this->cell_duo($row, $model, $bonus, $old_field, $new_field, $new_value, $color, $bonus_field);
	}

	protected function cell_duo(Force_List_Row $row, Jelly_Model $model, Bonus $bonus, $old_field, $new_field, $new_value, $color = NULL, $bonus_field = NULL) {
		$popover_position = 'top';

		$old_value = $model->{$old_field};

		$old_field_cell = $row->cell($old_field);
		$new_field_cell = $row->cell($new_field);

		if (!empty($color)) {
			$old_field_cell->attribute('style', 'color:' . $color);
			$new_field_cell->attribute('style', 'color:' . $color);
		}

		$caption = $old_field;

		$bonus_field_cell = $row->cell($bonus_field);
		$bonus_field_cell->attribute('style', 'color:gray');
		$model->format($bonus_field, $bonus->get_bonus_line());

		$bonus_data = $bonus->get_bonus_data();

		if (!empty($old_value) || (!empty($new_value))) {
			$field_meta = $model->meta()->field($old_field);
			if ($field_meta instanceof Jelly_Field) {
				$description = $field_meta->description;
				$label = $field_meta->label;
			} else {
				$description = NULL;
				$label = $old_field;
			}

			if ($label != $old_field) {
				$caption = $label;
			}

			$description = nl2br($description);

			if (!empty($old_value)) {
				$old_field_cell->popover($caption, $description, $popover_position);
			}
			if (!empty($new_value)) {
				if (empty($bonus_field) && !empty($description) && !empty($bonus_data)) {
					$description .= '<br/><br/>' . $bonus_data;
				}

				$new_field_cell->popover($caption, $description, $popover_position);
			}
		}

		$model->format($old_field, $old_value);
		$model->format($new_field, $new_value);

		if (!empty($bonus_field)) {
			if (!empty($bonus_data)) {
				$bonus_field_cell->popover($caption, $bonus_data, $popover_position);
			}
		}
	}

} // End Controller_Common_List_Cell