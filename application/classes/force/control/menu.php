<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_Menu
 * User: legion
 * Date: 06.11.17
 * Time: 22:13
 */
trait Force_Control_Menu {

	protected $_show_menu = false;
	protected $_menu;

	/*
	 * MENU
	 */

	public function show_menu($value = true) {
		$this->_show_menu = boolval($value);
		return $this;
	}

	public function hide_menu() {
		$this->_show_menu = false;
		return $this;
	}

	public function is_show_menu() {
		return $this->_show_menu;
	}

} // End Force_Control_Menu
