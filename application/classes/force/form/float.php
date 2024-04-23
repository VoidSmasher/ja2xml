<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Float
 * User: legion
 * Date: 31.10.17
 * Time: 20:41
 * @todo И на морду его! На морду!
 */
class Force_Form_Float extends Force_Form_Input {

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$new_value = str_replace(',', '.', $new_value);
		$new_value = (float) $new_value;

		$this->value($new_value);
		return $this->get_value();
	}

} // End Force_Form_Float
