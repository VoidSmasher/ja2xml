<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter_Conditions
 * User: legion
 * Date: 15.07.14
 * Time: 1:29
 */
trait Force_Filter_Conditions {

	protected $_conditions = array();

	public function get_conditions() {
		return $this->_conditions;
	}

	/*
	 * CONDITIONS
	 */

	public function where_open() {
		$this->_conditions[] = array(
			'type' => 'where_open',
		);
		return $this;
	}

	public function where_close() {
		$this->_conditions[] = array(
			'type' => 'where_close',
		);
		return $this;
	}

	public function and_where_open() {
		$this->_conditions[] = array(
			'type' => 'and_where_open',
		);
		return $this;
	}

	public function and_where_close() {
		$this->_conditions[] = array(
			'type' => 'and_where_close',
		);
		return $this;
	}

	public function or_where_open() {
		$this->_conditions[] = array(
			'type' => 'or_where_open',
		);
		return $this;
	}

	public function or_where_close() {
		$this->_conditions[] = array(
			'type' => 'or_where_close',
		);
		return $this;
	}

	public function and_where($column, $op = '=', $value = '') {
		$this->_where('and_where', $column, $op, $value);
		return $this;
	}

	public function or_where($column, $op = '=', $value = '') {
		$this->_where('or_where', $column, $op, $value);
		return $this;
	}

	public function where($column, $op = '=', $value = '') {
		$this->_where('where', $column, $op, $value);
		return $this;
	}

	protected function _where($type, $column, $op = '=', $value = '') {
		if (is_array($column)) {
			$this->and_where_open();
			$first = true;
			foreach ($column as $_column) {
				if ($first) {
					$this->where($_column, $op, $value);
					$first = false;
				} else {
					$this->or_where($_column, $op, $value);
				}
			}
			$this->and_where_close();
		} else {
			$result = array(
				'type' => $type,
				'column' => $column,
				'op' => $op,
			);
			if (is_object($value) && $value instanceof Database_Expression) {
				$result['db_expr'] = $value;
			} elseif ($value != '') {
				$result['value'] = $value;
			}
			$this->_conditions[] = $result;
		}
	}

	public function join_on($table, $c1, $op, $c2, $join_type = null) {
		$this->_conditions[] = array(
			'type' => 'join',
			'join_type' => $join_type,
			'table' => $table,
			'on' => array(
				'c1' => $c1,
				'op' => $op,
				'c2' => $c2,
			),
		);
		return $this;
	}

	public function group_by($columns) {
		$this->_conditions[] = array(
			'type' => 'group_by',
			'columns' => $columns,
		);
		return $this;
	}

	/*
	 * ADVANCED CONDITIONS
	 */

	public function where_array($column, $op = '=', $value = '') {
		$this->_where('where_array', $column, $op, $value);
		return $this;
	}

	public function where_numeric_array($column, $op = '=', $value = '') {
		$this->_where('where_numeric_array', $column, $op, $value);
		return $this;
	}

} // End Force_Filter_Conditions
