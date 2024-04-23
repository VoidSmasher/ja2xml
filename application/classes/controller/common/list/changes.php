<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_List_Changes
 * User: legion
 * Date: 20.04.2020
 * Time: 0:56
 */
trait Controller_Common_List_Changes {

	private $changes = array();

	/**
	 * @param Jelly_Model $model
	 * @param Force_Button | Force_Label $button_or_label
	 * @param null $old_value_prefix
	 */
	function changes_for_index(Jelly_Model $model, $button_or_label, $old_value_prefix = null) {
		if (array_key_exists($model->uiIndex, $this->changes)
			&& ($button_or_label instanceof Force_Button || $button_or_label instanceof Force_Label)) {
			$changes = $this->changes[$model->uiIndex];
			$changes_list = array();

			foreach ($changes as $_field => $_value) {
				$new_value = empty($_value) ? 0 : $_value;
				if ($model->uiIndex > 0) {
					$old_value = empty($model->{$old_value_prefix . $_field}) ? 0 : $model->{$old_value_prefix . $_field};
				} else {
					$old_value = $model->get_original($_field);
					if (empty($old_value)) {
						$old_value = 0;
					}
				}
				$changes_list[] = '<i>' . $_field . '</i>: <b>' . $new_value . '</b> (' . $old_value . ')';
			}

			$button_or_label
				->color_red()
				->popover('Changes', Helper_String::to_string($changes_list, '<br/>'));
		}
	}

	protected function set_value(Jelly_Model $model, $field, $value) {
		if ($model->{$field} != $value) {
			$this->changes[$model->uiIndex][$field] = $value;
		}
	}

	protected function get_value(Jelly_Model $model, $field) {
		if (isset($this->changes[$model->uiIndex][$field])) {
			return $this->changes[$model->uiIndex][$field];
		} else {
			return $model->{$field};
		}
	}

	protected function get_changes_count() {
		return count($this->changes);
	}

	protected function save_changes(Jelly_Model $model) {
		if (array_key_exists($model->uiIndex, $this->changes)) {
			foreach ($this->changes[$model->uiIndex] as $field => $value) {
				$model->{$field} = $value;
			}
		}

		try {
			$model->save();
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}
	}

} // End Controller_Common_List_Changes
