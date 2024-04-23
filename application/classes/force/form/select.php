<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Select
 * User: legion
 * Date: 26.07.14
 * Time: 10:26
 */
class Force_Form_Select extends Force_Form_Control {

	protected $_options = array();

	protected $_before_input = NULL;
	protected $_after_input = NULL;

	protected $_view = 'select';
	protected $_icon_class = 'fa-list';

	public function __construct($name = null, $label = null, array $options = array(), $selected = null) {
		$this->name($name);
		$this->value($selected);
		$this->label($label);
		$this->add_options($options);
		$this->attribute('class', 'form-control');
		$this->group_attribute('class', 'form-group');
	}

	public static function factory($name = null, $label = null, array $options = array(), $selected = null) {
		return new self($name, $label, $options, $selected);
	}

	protected function _render_simple() {
		return Form::select($this->_name, $this->_options, $this->_value, $this->get_attributes());
	}

	public function render() {
		$this->_view_data['before_input'] = $this->_before_input;
		$this->_view_data['after_input'] = $this->_after_input;
		$this->_view_data['options'] = $this->_options;

		return parent::render();
	}

	/*
	 * OPTIONS
	 */

	public function add_option($key, $value) {
		$this->_options[$key] = $value;
		return $this;
	}

	public function add_options(array $options) {
		foreach ($options as $key => $value) {
			$this->add_option($key, $value);
		}
		return $this;
	}

	public function get_options() {
		return $this->_options;
	}

	/*
	 * SUGGEST
	 */

	public function suggest($uri, array $params = array()) {
		$suggest = array(
			'name' => $this->_name,
			'uri' => Helper_Uri::auto_fill($uri),
			'data' => $params,
			'min_length' => 2,
			'options' => [],
		);

		Helper_Assets::js_vars_push_array('form_suggests', $suggest);

		Helper_Assets::add_scripts('assets/common/js/selectize.min.js');
		Helper_Assets::add_scripts('/assets/common/js/form.suggest.js');

		Helper_Assets::add_styles('/assets/common/css/selectize.bootstrap3.css');

		return $this;
	}

	/*
	 * GROUP
	 */

	public function before_input($value) {
		$this->_before_input = self::render_input_group_addon($value);
		return $this;
	}

	public function after_input($value) {
		$this->_after_input = self::render_input_group_addon($value);
		return $this;
	}

	final private static function render_input_group_addon($value) {
		if ($value instanceof Force_Button) {
			return '<span class="input-group-btn">' . $value->render() . '</span>';
		}
		if (is_object($value)) {
			if (method_exists($value, 'render')) {
				$value = $value->render();
			}
		}
		if (is_string($value)) {
			return '<span class="input-group-addon">' . $value . '</span>';
		}
		return '';
	}

} // End Force_Form_Select
