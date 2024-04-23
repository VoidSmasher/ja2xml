<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Control_DateTime
 * User: legion
 * Date: 26.07.18
 * Time: 14:19
 */
trait Force_Control_DateTime {

	protected $_pick_date = true;
	protected $_pick_time = false;
	protected $_pick_seconds = false;

	protected $_default_value = NULL;

	protected $_view = 'input_datetime';
	protected $_icon_class = 'fa-calendar';

	public function _render() {
		Helper_Assets::add_styles('assets/daterangepicker/css/daterangepicker.css');
		Helper_Assets::add_scripts('assets/daterangepicker/js/moment.min.js');
		Helper_Assets::add_scripts('assets/daterangepicker/js/daterangepicker.js');


	}

} // End Force_Control_DateTime
