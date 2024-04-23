<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Alias
 * User: legion
 * Date: 24.07.14
 * Time: 20:15
 */
class Force_Form_Alias extends Force_Form_Input {

	public function __construct($name = null, $label = null, $value = null) {
		$this->attribute('class', 'input-alias');
		parent::__construct($name, $label, $value);
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$this->value(Helper_String::translate_to_alias($new_value));
		return $this->get_value();
	}

} // End Force_Form_Alias
