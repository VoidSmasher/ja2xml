<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_List_Row
 * User: legion
 * Date: 21.10.14
 * Time: 22:01
 */
class Force_List_Row extends Force_Attributes {

	protected $_cells = array();

	public function __construct() {
	}

	public static function factory() {
		return new self;
	}

	public function render_open() {
		return self::render_open_tag($this->get_attributes());
	}

	public function render_close() {
		return self::render_close_tag();
	}

	public function render_cell($name, $value) {
		return self::render_cell_tag($value, $this->get_cell_attributes($name));
	}

	/**
	 * @param $name
	 * @return Force_List_Row_Cell
	 */
	public function cell($name) {
		$name = (string)$name;
		if (!array_key_exists($name, $this->_cells)) {
			$this->_cells[$name] = Force_List_Row_Cell::factory($name);
		}
		return $this->_cells[$name];
	}

	public function get_cell_attributes($name) {
		$attributes = array();
		if (array_key_exists($name, $this->_cells)) {
			$cell = &$this->_cells[$name];
			if ($cell instanceof Force_List_Row_Cell) {
				$attributes = $cell->get_attributes();
			}
		}
		return $attributes;
	}

	public function get_cell_overwrite($name) {
		$overwrite = null;
		if (array_key_exists($name, $this->_cells)) {
			$cell = &$this->_cells[$name];
			if ($cell instanceof Force_List_Row_Cell) {
				$overwrite = $cell->get_overwrite();
			}
		}
		return $overwrite;
	}

	/*
	 * HELPERS
	 */

	public static function render_open_tag(array $attributes = array()) {
		return '<tr' . HTML::attributes($attributes) . '>';
	}

	public static function render_close_tag() {
		return "</tr>\n";
	}

	public static function render_cell_tag($value, array $attributes = array()) {
		return '<td' . HTML::attributes($attributes) . '>' . $value . "</td>\n";
	}

} // End Force_List_Row
