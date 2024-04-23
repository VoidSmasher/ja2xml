<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Jelly_Field_Generator
 * User: legion
 * Date: 21.11.17
 * Time: 14:23
 */
class Jelly_Field_Generator {

	protected $_type;
	protected $_params = array();
	protected $_rules = array();
	protected $_filters = array();

	public function __construct($type, $label, array $params = array()) {
		$this->_type = $type;
		$this->_params = $params;
		$this->label($label);
	}

	public function apply() {
		$params = $this->_params;
		if (!empty($this->_rules)) {
			$params['rules'] = $this->_rules;
		}
		$class = 'Jelly_Field_' . $this->_type;
		return new $class($params);
	}

	/*
	 * FACTORY TYPES
	 */

	public static function primary($label) {
		return new self('Primary', $label);
	}

	public static function integer($label) {
		return new self('Integer', $label);
	}

	public static function string($label) {
		return new self('String', $label);
	}

	public static function alias($label = null) {
		if (empty($label)) {
			$label = __('common.alias');
		}
		return new self('String', $label);
	}

	public static function text($label) {
		return new self('Text', $label);
	}

	public static function enum($label, array $choices) {
		return new self('Enum', $label, [
			'choices' => $choices,
		]);
	}

	public static function timestamp($label, $format = Force_Date::FORMAT_SQL) {
		return new self('Timestamp', $label, [
			'format' => $format,
		]);
	}

	public static function created_at($format = Force_Date::FORMAT_SQL) {
		return new self('Timestamp', __('common.created_at'), [
			'format' => $format,
			'auto_now_create' => TRUE,
		]);
	}

	public static function updated_at($format = Force_Date::FORMAT_SQL) {
		return new self('Timestamp', __('common.updated_at'), [
			'format' => $format,
			'auto_now_update' => TRUE,
		]);
	}

	public static function deleted_at($format = Force_Date::FORMAT_SQL) {
		return new self('Timestamp', __('common.deleted_at'), [
			'format' => $format,
		]);
	}

	/**
	 * @param      $db_expression
	 * @param null $column_type принимает значения какой тип колонки симулировать, например: Integer или String или Text
	 */
	public static function expression($db_expr_string, $db_expr_params = array(), $column_type = null) {
		if (!is_array($db_expr_params)) {
			$db_expr_params = array();
		}
		$params = [
			'column' => DB::expr($db_expr_string, $db_expr_params),
		];
		if (!empty($column_type) && is_string($column_type)) {
			$params['cast'] = $column_type;
		}
		return new self('Expression', null, $params);
	}

	/*
	 * SETTERS
	 */

	public function label($value) {
		$this->_params['label'] = (string)$value;
		return $this;
	}

	public function description($value) {
		$this->_params['description'] = (string)$value;
		return $this;
	}

	public function default_value($value) {
		$this->_params['default'] = $value;
		return $this;
	}

	public function not_empty() {
		$this->_rules[] = array('not_empty');
		return $this;
	}

	public function empty_value($value) {
		$this->_params['empty_value'] = $value;
		return $this;
	}

	/*
	 * BOOLEAN SETTERS
	 */

	public function in_db($value = true) {
		$this->_params['in_db'] = (boolean)$value;
		return $this;
	}

	public function unique($value = true) {
		$this->_params['unique'] = (boolean)$value;
		return $this;
	}

	public function allow_null($value = true) {
		$this->_params['allow_null'] = (boolean)$value;
		return $this;
	}

	public function convert_empty($value = true) {
		$this->_params['convert_empty'] = (boolean)$value;
		return $this;
	}

	/*
	 * SETTERS FOR RULES
	 */

	public function min_length($value) {
		$this->_rules[] = array(
			'min_length',
			array(
				':value',
				(int)$value,
			),
		);
		return $this;
	}

	public function max_length($value) {
		$this->_rules[] = array(
			'max_length',
			array(
				':value',
				(int)$value,
			),
		);
		return $this;
	}

} // End Jelly_Field_Generator
