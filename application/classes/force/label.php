<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Label
 * User: legion
 * Date: 24.12.14
 * Time: 14:06
 */
class Force_Label extends Force_Control {

	use Force_Attributes_Group;
	use Force_Control_Icon;
	use Force_Control_Link;

	protected $_colors = array(
		'label-default',
		'label-primary',
		'label-success',
		'label-info',
		'label-warning',
		'label-danger',
	);

	protected $_confirmation = '';

	protected $_color = 'label-default';
	protected $_size = 0;

	public function __construct($label) {
		$this->label($label);
	}

	public static function factory($label) {
		return new self($label);
	}

	public function __toString() {
		return $this->render();
	}

	public function render() {
		// classes
		$this->attribute('class', 'label');
		$this->replace_attribute_class($this->_colors, $this->_color);

		// confirmation
		if (!empty($this->_confirmation)) {
			$this->attribute('onclick', 'return confirm(\'' . addslashes($this->_confirmation) . '\')');
		}

		$name = $this->get_name();
		if (!is_null($name) && ($name != '')) {
			$this->attribute('name', $name);
		}

		$value = $this->get_value();
		if (!is_null($value) && ($value != '')) {
			$this->attribute('value', $value);
		}

		// label

		$render = '<span ' . $this->render_attributes() . '>' . $this->get_icon() . $this->get_label() . '</span>';

		if ($this->_size > 0) {
			$render = '<h' . $this->_size . '>' . $render . '</h' . $this->_size . '>';
		}

		// as link
		if (!empty($this->_link)) {
			$this->group_attribute('href', $this->get_link());

			$render = '<a ' . $this->render_group_attributes() . '>' . $render . '</a>';
		}

		return $render;
	}

	/*
	 * SET
	 */

	public function confirmation($message) {
		$this->_confirmation = $message;
		return $this;
	}

	/*
	 * COLORS BOOTSTRAP NATIVE
	 */

	public function label_default() {
		$this->_color = 'label-default';
		return $this;
	}

	public function label_primary() {
		$this->_color = 'label-primary';
		return $this;
	}

	public function label_success() {
		$this->_color = 'label-success';
		return $this;
	}

	public function label_info() {
		$this->_color = 'label-info';
		return $this;
	}

	public function label_warning() {
		$this->_color = 'label-warning';
		return $this;
	}

	public function label_danger() {
		$this->_color = 'label-danger';
		return $this;
	}

	/*
	 * COLORS ALTERNATIVE
	 */

	public function color($value) {
		if (is_numeric($value)) {
			$value = '#' . $value;
		}
		if (is_string($value) && !empty($value)) {
			$this->attribute('style', 'background-color:' . $value);
		}
		return $this;
	}

	public function color_gray() {
		$this->label_default();
		return $this;
	}

	public function color_blue() {
		$this->label_primary();
		return $this;
	}

	public function color_green() {
		$this->label_success();
		return $this;
	}

	public function color_cyan() {
		$this->label_info();
		return $this;
	}

	public function color_yellow() {
		$this->label_warning();
		return $this;
	}

	public function color_red() {
		$this->label_danger();
		return $this;
	}

	/*
	 * SIZES
	 */

	/**
	 * @deprecated use size() instead
	 */
	public function size_1() {
		$this->_size = 1;
		return $this;
	}

	/**
	 * @deprecated use size() instead
	 */
	public function size_2() {
		$this->_size = 2;
		return $this;
	}

	/**
	 * @deprecated use size() instead
	 */
	public function size_3() {
		$this->_size = 3;
		return $this;
	}

	/**
	 * @deprecated use size() instead
	 */
	public function size_4() {
		$this->_size = 4;
		return $this;
	}

	/**
	 * @deprecated use size() instead
	 */
	public function size_5() {
		$this->_size = 5;
		return $this;
	}

	/**
	 * @deprecated use size() instead
	 */
	public function size_6() {
		$this->_size = 6;
		return $this;
	}

	public function size($value) {
		if (empty($value)) {
			$this->remove_attribute_style('font-size');
		} else {
			$this->attribute('style', 'font-size:' . (string)$value);
		}
//		if (!is_integer($int_from_0_to_6)) {
//			$int_from_0_to_6 = 0;
//		}
//
//		if ($int_from_0_to_6 < 0) {
//			$int_from_0_to_6 = 0;
//		} else if ($int_from_0_to_6 > 6) {
//			$int_from_0_to_6 = 6;
//		}
//
//		$this->_size = $int_from_0_to_6;
		return $this;
	}

	/*
	 * PREDEFINED SETUP
	 */

	public function preset_boolean($yes = null, $no = null, $hide_no = false) {
		if (is_array($yes)) {
			$no = Arr::get($yes, 0, __('common.no'));
			$yes = Arr::get($yes, 1, __('common.yes'));
		}

		if (!is_string($yes)) {
			$yes = __('common.yes');
		}

		if (!is_string($no)) {
			$no = __('common.no');
		}

		if ($this->_label) {
			$this->_label = $yes;
			$this->color_green();
		} else {
			if (!$hide_no) {
				$this->_label = $no;
				$this->color_red();
			}
		}

		return $this;
	}

	public function preset_boolean_yes_no() {
		return $this->preset_boolean();
	}

	public function preset_boolean_yes_no_hidden() {
		return $this->preset_boolean(null, null, true);
	}

	public function preset_boolean_published() {
		if ($this->_label) {
			$this->_label = __('common.published.1');
			$this->color_blue();
		} else {
			$this->_label = __('common.published.0');
			$this->color_gray();
		}
		return $this;
	}

} // End Force_Label
