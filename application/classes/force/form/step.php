<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Step
 * User: legion
 * Date: 10.12.15
 * Time: 18:58
 * @todo доделать, но так ли оно вообще должно быть?
 */
class Force_Form_Step extends Force_Attributes {

	use Force_Control_Name;
	use Force_Control_Label;
	use Force_Control_Horizontal;
	use Force_Form_Controls;

	private $_number = 1;

	public function __construct($number = 1, array $controls = array()) {
		$this->_number = (int)$number;
		$this->add_controls($controls);
	}

	public function __set($name, $value) {
		$this->_set_controls($name, $value);
	}

	public static function factory($number = 1, array $controls = array()) {
		return new self($number, $controls);
	}

	public function render($container_body = null) {
		$container_body = (string)$container_body;
		return $container_body;
	}

	public function get_number() {
		return $this->_number;
	}

} // End Force_Form_Step
