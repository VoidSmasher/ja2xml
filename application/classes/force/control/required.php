<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Required
 * User: legion
 * Date: 09.08.17
 * Time: 2:05
 * Require
 * - Force_Control_Label
 */
trait Force_Control_Required {

	protected $_required = false;

	/*
	 * SET
	 */

	public function required() {
		$this->_required = true;
		return $this;
	}

	public function not_required() {
		$this->_required = false;
		return $this;
	}

	protected function _apply_required() {
		if ($this->_required && isset($this->_label)) {
			$this->_label .= '&nbsp;<span class="text-danger">*</span>';
		}
	}

	/*
	 * GET
	 */

	public function is_required() {
		return $this->_required;
	}

} // End Force_Control_Required
