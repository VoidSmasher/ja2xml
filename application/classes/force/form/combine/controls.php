<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Combine_Controls
 * User: legion
 * Date: 10.06.17
 * Time: 20:52
 */
abstract class Force_Form_Combine_Controls extends Force_Form_Control {

	use Force_Form_Controls;

	const CONTROL_NAME = 'combine';

	private $_combine_controls = array();

	/*
	 * COMBINE CONTROLS
	 */

	protected function add_combine_control(Force_Form_Control $control, $name) {
		$control->name($name);

		$control->attribute('class', 'combine-control');

		if ($control instanceof Force_Form_Image) {
			$control->allow_remove_image(false);
		}

		$this->_update_control_label($control, $control->is_show_label());

		$this->_combine_controls[$name] = $control;

		return true;
	}

	protected function remove_combine_controls() {
		$this->_combine_controls = array();
	}

	protected function get_combine_controls() {
		return $this->_combine_controls;
	}

	/*
	 * HELPERS
	 */

	protected function _update_control_label(Force_Form_Control $control, $show_label = true) {
		$label = $control->get_label();
		$type = $control->get_type();
		if (empty($label) && $show_label) {
			$label = i18n::get_default('common.' . $type, ucfirst($type));
			$control->label($label);
		}
	}

	/*
	 * CONTROL NAME
	 */

	protected function _get_control_name($name, $type, $id = 1) {
		return $name . '-' . $id . '-' . $type;
	}

	protected function _get_hidden_name($name, $type, $id = 1) {
		return $name . '[' . $id . '-' . $type . ']';
	}

} // End Force_Form_Combine_Controls
