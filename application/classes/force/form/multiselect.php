<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Multiselect
 * User: legion
 * Date: 26.07.14
 * Time: 10:20
 */
class Force_Form_Multiselect extends Force_Form_Select {

	public function __construct($name = null, $label = null, array $options = array(), $selected = null) {
		parent::__construct($name, $label, $options, $selected);
	}

	public static function factory($name = null, $label = null, array $options = array(), $selected = null) {
		return new self($name, $label, $options, $selected);
	}

	public function render() {
		$this->attribute('multiple');
		$this->attribute('size', 10);

		$this->_name .= '[]';
		return parent::render();
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	// @todo что-то тут не то, надо разобраться
	// @todo если сохранять через json, то нужен метод для декодирования
	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		if (!is_array($new_value)) {
			$new_value = array();
		}
		$new_value = json_encode($new_value, JSON_UNESCAPED_UNICODE);
		$this->value($new_value);
		return $this->get_value();
	}

} // End Force_Form_Multiselect
