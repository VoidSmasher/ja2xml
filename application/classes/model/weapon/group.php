<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Weapon_Group
 * User: legion
 * Date: 02.01.2020
 * Time: 19:08
 *
 * Weapons Fields
 * @property integer uiIndex
 * @property string szWeaponName
 * @property integer ubWeaponClass
 * @property integer ubWeaponType
 * @property integer ubCalibre
 * @property integer ubReadyTime
 * @property float ubShotsPer4Turns
 * @property integer ubShotsPerBurst
 * @property integer ubBurstPenalty
 * @property integer ubBulletSpeed
 * @property integer ubImpact
 * @property integer ubDeadliness
 * @property integer bAccuracy
 * @property integer ubMagSize
 * @property integer usRange
 * @property integer usReloadDelay
 * @property integer BurstAniDelay
 * @property integer ubAttackVolume
 * @property integer ubHitVolume
 * @property integer sSound
 * @property integer sBurstSound
 * @property integer sSilencedBurstSound
 * @property integer sReloadSound
 * @property integer sLocknLoadSound
 * @property integer SilencedSound
 * @property integer bBurstAP
 * @property integer bAutofireShotsPerFiveAP
 * @property integer APsToReload
 * @property integer SwapClips
 * @property integer MaxDistForMessyDeath
 * @property integer AutoPenalty
 * @property integer NoSemiAuto
 * @property integer EasyUnjam
 * @property integer APsToReloadManually
 * @property integer ManualReloadSound
 * @property integer nAccuracy
 * @property integer bRecoilX
 * @property integer bRecoilY
 * @property integer ubAimLevels
 * @property integer ubRecoilDelay
 * @property integer Handling
 * @property integer usOverheatingJamThreshold
 * @property integer usOverheatingDamageThreshold
 * @property integer usOverheatingSingleShotTemperature
 *
 * Item Fields
 * @property integer ubWeight
 * @property integer ubCoolness
 * @property integer ItemSize
 * @property integer PercentNoiseReduction
 * @property integer TwoHanded
 *
 * Weapon Data Fields
 * @property string calibre
 * @property string weapon_name
 * @property string weapon_name_short
 * @property string weapon_description
 * @property string weapon_description_br
 * @property integer year_of_adoption
 * @property integer year_of_withdrawal
 * @property integer amount_built
 * @property integer $rarity
 * @property integer $weapon_class
 * @property integer $weapon_type
 * @property float length_min
 * @property float length_max
 * @property float length_barrel
 * @property float length_front_and_handle
 * @property float length_front_to_trigger
 * @property float height_diff_stock_barrel
 * @property integer weight
 * @property integer weight_empty
 * @property integer weight_loaded
 * @property integer weight_front_percent
 * @property integer fire_rate_semi
 * @property integer fire_rate_burst
 * @property integer fire_rate_auto_min
 * @property integer fire_rate_auto_max
 * @property integer burst_length
 * @property boolean no_semi_auto
 * @property boolean no_full_auto
 * @property integer muzzle_velocity
 * @property integer mag_size
 * @property string mechanism_action
 * @property string mechanism_trigger
 * @property string mechanism_feature
 * @property string mechanism_reload
 * @property string comfort
 * @property string weapon_qualities
 * @property string default_attachments
 * @property string possible_attachments
 * @property string attachment_mounts
 * @property string integrated_stock_name
 * @property integer integrated_stock_index
 * @property string integrated_scope_name
 * @property integer integrated_scope_index
 * @property string integrated_sight_name
 * @property integer integrated_sight_index
 * @property string integrated_laser_name
 * @property integer integrated_laser_index
 * @property string integrated_bipod_name
 * @property integer integrated_bipod_index
 * @property string integrated_foregrip_name
 * @property integer integrated_foregrip_index
 * @property string integrated_suppressor_name
 * @property integer integrated_suppressor_index
 * @property boolean has_hp_iron_sights
 * @property boolean has_hp_scope_mount
 * @property boolean has_hk_trigger
 * @property boolean has_mag_stanag
 * @property boolean has_drum_mag
 * @property boolean has_calico_mag
 * @property boolean has_long_mag
 * @property boolean has_bolt_hold_open
 * @property boolean has_balanced_automatic
 * @property boolean has_recoil_reducing_stock
 * @property boolean has_recoil_buffer_in_stock
 * @property boolean has_compensator
 * @property boolean has_muzzle_break
 * @property boolean has_heavy_barrel
 * @property boolean has_ported_barrel
 * @property boolean has_sniper_barrel
 * @property boolean has_floating_barrel
 * @property boolean has_replaceable_barrel
 * @property boolean has_cheek_piece
 * @property boolean has_adjustable_cheek_piece
 * @property boolean has_adjustable_butt_stock
 * @property boolean has_adjustable_grip
 * @property boolean is_two_handed
 * @property boolean is_secondary_weapon
 * @property integer targeting_range
 * @property integer effective_range
 * @property float moa_claimed
 * @property integer moa_claimed_range
 * @property float moa_test_average
 * @property string test_data
 * @property integer accuracy_bonus
 * @property integer accuracy_bonus_percent
 * @property integer range_bonus
 * @property integer range_bonus_percent
 * @property integer ready_bonus
 * @property integer ready_bonus_percent
 * @property integer sp4t_bonus
 * @property integer sp4t_bonus_percent
 * @property integer aptrm_bonus
 * @property integer aptrm_bonus_percent
 * @property integer reload_bonus
 * @property integer reload_bonus_percent
 * @property integer burst_ap_bonus
 * @property integer afsp5ap_bonus
 * @property integer recoil_x_bonus
 * @property integer recoil_x_bonus_percent
 * @property integer recoil_y_bonus
 * @property integer recoil_y_bonus_percent
 * @property integer handling_bonus
 * @property integer handling_bonus_percent
 *
 * Calibre fields
 * @property float calibre_damage
 * @property float calibre_semi_speed
 * @property float calibre_burst_recoil
 * @property float calibre_auto_recoil
 */
class Model_Weapon_Group extends Model_Calibre {

	private $check_fire_rate_auto;
	private $check_fire_rate_burst;
	private $check_has_full_auto;
	private $check_has_burst_fire;
	private $check_is_burst_fire_possible;
	private $check_is_automatic;

	public $attachments_info;

	public $calc_weight_front_percent = 'undefined';
	public $calc_recoil = 'undefined';
	public $calc_weight = 'undefined';

	public $calc_damage = 'undefined';
	public $calc_ready = 'undefined';
	public $calc_handling = 'undefined';
	public $calc_range = 'undefined';
	public $calc_accuracy = 'undefined';
	public $calc_recoil_x = 'undefined';
	public $calc_recoil_y = 'undefined';
	public $calc_sp4t = 'undefined';
	public $calc_aptrm = 'undefined';
	public $calc_reload = 'undefined';
	public $calc_burst_ap = 'undefined';
	public $calc_auto_shots = 'undefined';
	public $calc_br_rof = 'undefined';
	public $calc_messy_range = 'undefined';
	public $calc_deadliness = 'undefined';
	public $calc_coolness = 'undefined';

	private $attack_volume = 'undefined';

	private $changes = array();

	private function set_value($field, $value) {
		if ($this->{$field} != $value) {
			$this->changes[$field] = $value;
		}
	}

	public function get_value($field) {
		if (array_key_exists($field, $this->changes)) {
			return $this->changes[$field];
		}
		return $this->{$field};
	}

	public function get_fire_rate_auto() {
		if (is_null($this->check_fire_rate_auto)) {
			$this->_check_weapon_data();
		}
		return $this->check_fire_rate_auto;
	}

	public function get_fire_rate_burst() {
		if (is_null($this->check_fire_rate_burst)) {
			$this->_check_weapon_data();
		}
		return $this->check_fire_rate_burst;
	}

	public function is_automatic() {
		if (is_null($this->check_is_automatic)) {
			$this->_check_weapon_data();
		}
		return $this->check_is_automatic;
	}

	public function is_burst_fire_possible() {
		if (is_null($this->check_is_burst_fire_possible)) {
			$this->_check_weapon_data();
		}
		return $this->check_is_burst_fire_possible;
	}

	public function has_full_auto() {
		if (is_null($this->check_has_full_auto)) {
			$this->_check_weapon_data();
		}
		return $this->check_has_full_auto;
	}

	public function has_burst_fire() {
		if (is_null($this->check_has_burst_fire)) {
			$this->_check_weapon_data();
		}
		return $this->check_has_burst_fire;
	}

	private function _check_weapon_data() {
		$this->check_fire_rate_auto = Core_Weapon_Data::get_fire_rate_auto($this);
		$this->check_fire_rate_burst = Core_Weapon_Data::get_fire_rate_burst($this);

		if ($this->no_full_auto) {
			$this->check_has_full_auto = false;
		} else {
			$this->check_has_full_auto = ($this->check_fire_rate_auto > 0);
		}

		if ($this->burst_length < 1) {
			$this->check_has_burst_fire = false;
		} else {
			$this->check_has_burst_fire = ($this->check_fire_rate_burst > 0);
		}

		$this->check_is_burst_fire_possible = (!$this->check_has_burst_fire && $this->check_fire_rate_burst > 0 && $this->has_hk_trigger);
		$this->check_is_automatic = ($this->check_has_full_auto || $this->check_has_burst_fire);
	}

	public function get_attack_volume($percentNoiseReduction = null) {
		if (is_null($percentNoiseReduction)) {
			$percentNoiseReduction = $this->PercentNoiseReduction;
		}

		if ($this->attack_volume === 'undefined') {
			$this->attack_volume = $this->ubAttackVolume - round($this->ubAttackVolume * $percentNoiseReduction / 100);
		}

		return $this->attack_volume;
	}

} // End Model_Weapon_Group
