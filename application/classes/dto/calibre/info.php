<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 09.05.2021
 * Time: 0:59
 */
class DTO_Calibre_Info {

	public $cartridge_weight = 0;
	public $cartridge_action = 0;
	public $empty_cartridge_weight = 0;
	public $empty_cartridge_action = 0;

	public $manual_reload = 1;
	public $manual_extract = 1;
	public $manual_pre_wind = 2;

	public $bolt_weight;
	public $bolt_action;
	public $move_bolt;
	public $rotate_bolt;
	public $rotate_bolt_up;
	public $rotate_bolt_down;
	public $drop_bolt;
	public $charge_bolt;

	public $extract_empty_case = 3;
	public $load_new_cartridge = 3;

	public function __construct($cartridge_weight) {
		$action_exponent = 0.15;
		$action_multiplier = 3;

		$this->cartridge_weight = $cartridge_weight / 100;
		$this->cartridge_action = pow($this->cartridge_weight, $action_exponent) * $action_multiplier;

		$this->bolt_weight = $this->cartridge_weight * 3;
		$this->bolt_action = pow($this->bolt_weight, 0.3) * $action_multiplier;

		$this->move_bolt = round($this->bolt_action, 2);
		$this->rotate_bolt = round($this->bolt_action / 2, 2);
		$this->rotate_bolt_up = round($this->rotate_bolt * 1.2, 2);
		$this->rotate_bolt_down = round($this->rotate_bolt * 0.8, 2);
		$this->drop_bolt = round($this->bolt_action / 2, 2);
		$this->charge_bolt = $this->move_bolt + $this->drop_bolt;

		$this->empty_cartridge_weight = $this->cartridge_weight * 0.1;
		$this->empty_cartridge_action = pow($this->empty_cartridge_weight, $action_exponent) * $action_multiplier;

		$this->extract_empty_case = round($this->empty_cartridge_action, 2);
		$this->load_new_cartridge = round($this->cartridge_action, 2);
	}
}