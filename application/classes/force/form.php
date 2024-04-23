<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form
 * User: legion
 * Date: 24.07.14
 * Time: 11:26
 */
class Force_Form extends Force_Form_Core {

	public static function factory(array $controls = array(), $form_action = null, $form_method = 'post') {
		return new self($controls, $form_action, $form_method);
	}

} // End Force_Form
