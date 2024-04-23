<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Jelly_Meta
 * User: legion
 * Date: 28.11.17
 * Time: 16:01
 */
class Jelly_Meta extends Jelly_Core_Meta {

	public function finalize($model) {
		if ($this->_initialized) {
			return;
		}

		foreach ($this->_fields as $column => $field) {
			if ($field instanceof Jelly_Field_Generator) {
				$this->_fields[$column] = $field->apply();
			}
		}

		parent::finalize($model);
	}

} // End Jelly_Meta
