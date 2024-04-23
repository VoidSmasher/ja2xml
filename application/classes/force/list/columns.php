<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_List_Columns
 * User: legion
 * Date: 21.10.14
 * Time: 20:34
 */
abstract class Force_List_Columns extends Force_Attributes {

	protected $_columns = array();
	protected static $_registered_columns = array(
		'Force_List_Column',
	);

	public function __construct(array $columns = array()) {
		$this->add_columns($columns);
	}

	/*
	 * CHECK
	 */

	public static function check_column(&$column) {
		if (!is_object($column)) {
			return false;
		}
		foreach (self::$_registered_columns as $_class_name) {
			if (is_a($column, $_class_name)) {
				return true;
			}
		}
		return false;
	}

	protected static function _check_column(&$column) {
		if (!self::check_column($column)) {
			throw new Exception(get_class($column) . ' is not a valid column for Force_List');
		}
		return true;
	}

	/*
	 * COLUMNS
	 */

	public function add_column($column) {
		if (is_numeric($column) || (is_string($column) && !empty($column))) {
			$label = $column;
			$column = Force_List_Column::factory($column);
			$column->label($label);
		}
		if (self::_check_column($column)) {
			$name = $column->get_name();
			if (!is_null($name)) {
				$this->_columns[$name] = $column;
			} else {
				$this->_columns[] = $column;
			}
		}
		return $this;
	}

	public function add_columns(array $columns) {
		foreach ($columns as $column) {
			$this->add_column($column);
		}
		return $this;
	}

	public function remove_column($name) {
		if (array_key_exists($name, $this->_columns)) {
			unset($this->_columns[$name]);
		}
		return $this;
	}

	/*
	 * GET
	 */

	public function column($name) {
		$name = (string)$name;
		if (array_key_exists($name, $this->_columns)) {
			return $this->_columns[$name];
		} else {
			return $this->_columns[$name] = Force_List_Column::factory($name);
		}
	}

	public function get_columns_count() {
		return count($this->_columns);
	}

} // End Force_List_Columns
