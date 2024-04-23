<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 08.05.2021
 * Time: 22:31
 */
class DTO_Attachments_Info {

	public $max_scope_magnitude = 0;
	public $sight_has_scope = false;
	public $scope_has_sight = false;

	public $has_integral_scope = false;
	public $has_scope = false;
	public $has_sight = false;

	public $has_integral_laser = false;
	public $has_rifle_laser = false;
	public $has_laser = false;

	public $has_bipod = false;
	public $has_grippod = false;
	public $has_foregrip = false;

	public $has_integral_suppressor = false;
	public $has_suppressor = false;
	public $suppressor_effectiveness = 0;

	public $has_integral_flash_hider = false;
	public $has_flash_hider = false;

	public $has_integral_secondary_weapon = false;
	public $has_secondary_weapon = false;
	public $has_under_barrel_weapon = false;
	public $has_above_barrel_weapon = false;
	public $has_multi_charge_gl = false;

}
