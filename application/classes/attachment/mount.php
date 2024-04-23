<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Attachment_Mount
 * User: legion
 * Date: 04.01.2020
 * Time: 1:37
 */
abstract class Attachment_Mount {

	const MOUNT_RAIL_RECEIVER_MINIMUM = 'Rail_Receiver_Minimum';
	const MOUNT_RAIL_RECEIVER_SHORT = 'Rail_Receiver_Short';
	const MOUNT_RAIL_RECEIVER_SHORT_MEDIUM = 'Rail_Receiver_Short_Medium';
	const MOUNT_RAIL_RECEIVER_SHORT_LONG = 'Rail_Receiver_Short_Long';
	const MOUNT_RAIL_RECEIVER_MEDIUM = 'Rail_Receiver_Medium';
	const MOUNT_RAIL_RECEIVER_MEDIUM_LONG = 'Rail_Receiver_Medium_Long';
	const MOUNT_RAIL_RECEIVER_LONG = 'Rail_Receiver_Long';
	const MOUNT_RAIL_RECEIVER_MAXIMUM = 'Rail_Receiver_Maximum';
	const MOUNT_RAIL_FRONT_TOP_SHORT = 'Rail_Front_Top_Short';
	const MOUNT_RAIL_FRONT_TOP_MEDIUM = 'Rail_Front_Top_Medium';
	const MOUNT_RAIL_FRONT_TOP_LONG = 'Rail_Front_Top_Long';
	const MOUNT_RAIL_FRONT_TOP_MAXIMUM = 'Rail_Front_Top_Maximum';
	const MOUNT_RAIL_FRONT_BOTTOM_PISTOL = 'Rail_Front_Bottom_Pistol';
	const MOUNT_RAIL_FRONT_BOTTOM_SHORT = 'Rail_Front_Bottom_Short';
	const MOUNT_RAIL_FRONT_BOTTOM_MEDIUM = 'Rail_Front_Bottom_Medium';
	const MOUNT_RAIL_FRONT_BOTTOM_LONG = 'Rail_Front_Bottom_Long';
	const MOUNT_RAIL_FRONT_BOTTOM_MAXIMUM = 'Rail_Front_Bottom_Maximum';
	const MOUNT_RAIL_FRONT_SIDE_SHORT = 'Rail_Front_Side_Short';
	const MOUNT_RAIL_FRONT_SIDE_MEDIUM = 'Rail_Front_Side_Medium';
	const MOUNT_RAIL_FRONT_SIDE_LONG = 'Rail_Front_Side_Long';
	const MOUNT_RAIL_FRONT_SIDE_MAXIMUM = 'Rail_Front_Side_Maximum';
	const MOUNT_PCAP_FRONT_BOTTOM_SHORT = 'PCAP_Front_Bottom_Short';
	const MOUNT_PCAP_FRONT_BOTTOM_MEDIUM = 'PCAP_Front_Bottom_Medium';
	const MOUNT_PCAP_FRONT_BOTTOM_LONG = 'PCAP_Front_Bottom_Long';
	const MOUNT_PCAP_FRONT_SIDE_SHORT = 'PCAP_Front_Side_Short';
	const MOUNT_PCAP_FRONT_SIDE_LONG = 'PCAP_Front_Side_Long';
	const MOUNT_HANDGUARD_M4 = 'Mount_Handguard_M4';
	const MOUNT_HANDGUARD_M16 = 'Mount_Handguard_M16';
	const MOUNT_SCOPE_PCAP = 'Mount_Scope_PCAP';
	const MOUNT_SCOPE_AR_HANDLE = 'Mount_Scope_AR_Handle';
	const MOUNT_SCOPE_STANAG = 'Mount_Scope_STANAG';
	const MOUNT_SCOPE_RINGS = 'Mount_Scope_Rings';
	const MOUNT_SCOPE_AK = 'Mount_Scope_AK';
	const MOUNT_SCOPE_HK = 'Mount_Scope_Claw';
	const MOUNT_SCOPE_FAL = 'Mount_Scope_FAL';
	const MOUNT_SCOPE_FNC = 'Mount_Scope_FNC';
	const MOUNT_SCOPE_MSG = 'Mount_Scope_MSG';
	const MOUNT_SCOPE_SG_S = 'Mount_Scope_SG-S';
	const MOUNT_SCOPE_SG_L = 'Mount_Scope_SG-L';
	const MOUNT_SCOPE_GROZA = 'Mount_Scope_Groza';
	const MOUNT_SCOPE_GALIL = 'Mount_Scope_Galil';
	const MOUNT_SCOPE_GALATZ = 'Mount_Scope_Galatz';
	const MOUNT_SCOPE_M1 = 'Mount_Scope_M1';
	const MOUNT_SCOPE_MAUSER = 'Mount_Scope_Mauser';
	const MOUNT_SCOPE_MOSIN = 'Mount_Scope_Mosin';
	const MOUNT_SCOPE_MAGNIFIER = 'Mount_Scope_Magnifier';
	const MOUNT_SCOPE_PISTOL = 'Mount_Scope_Pistol';
	const MOUNT_SIGHT_PISTOL = 'Mount_Sight_Pistol';
	const MOUNT_SIGHT_KOBRA = 'Mount_Sight_Kobra';
	const MOUNT_SIGHT_REFLEX = 'Mount_Sight_Reflex';
	const MOUNT_SIGHT_MICRO_RDS = 'Mount_Sight_Micro_RDS';
	const MOUNT_LASER = 'Mount_Laser';
	const MOUNT_LAM_PISTOL = 'Mount_LAM_Pistol';
	const MOUNT_LAM_RIFLE = 'Mount_LAM_Rifle';
	const MOUNT_SUPPRESSOR = 'Mount_Suppressor';
	const MOUNT_SUPPRESSOR_FLASH = 'Mount_Suppressor_Flash';
	const MOUNT_CHOKE_SHORT = 'Mount_Choke_Short';
	const MOUNT_CHOKE_LONG = 'Mount_Choke_Long';
	const MOUNT_FOREGRIP = 'Mount_Foregrip';
	const MOUNT_BIPOD = 'Mount_Bipod';
	const MOUNT_KNIFE = 'Mount_Knife';
	const MOUNT_STOCK_FOLDING = 'Mount_Stock_Folding';
	const MOUNT_STOCK_RETRACTABLE = 'Mount_Stock_Retractable';
	const MOUNT_GL_RIFLE = 'Mount_GL_Rifle';
	const MOUNT_GL_M203PI = 'Mount_GL_M203PI';
	const MOUNT_GL_AG36 = 'Mount_GL_AG36';
	const MOUNT_GL_HK79 = 'Mount_GL_HK79';
	const MOUNT_GL_GP30 = 'Mount_GL_GP30';
	const MOUNT_GL_FN_EGLM = 'Mount_GL_FN_EGLM';
	const MOUNT_GL_GP25 = 'Mount_GL_GP25';
	const MOUNT_GL_OICW = 'Mount_GL_OICW';
	const MOUNT_GL_AICW = 'Mount_GL_AICW';
	const MOUNT_GL_F2000 = 'Mount_GL_F2000';
	const MOUNT_BARREL_EXTENDER = 'Mount_Extended_Barrel';
	const MOUNT_RIFLE_SLING = 'Mount_Rifle_Sling';
	const MOUNT_BATTERIES = 'Mount_Batteries';

	private $mounts_list = array();
	/*
	 * COLORS:
	 * - red
	 * - yellow
	 * - green
	 * - cyan
	 * - blue
	 */
	private static $mounts = array(
		self::MOUNT_RAIL_RECEIVER_MINIMUM => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_SHORT => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_SHORT_MEDIUM => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_SHORT_LONG => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_MEDIUM => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_MEDIUM_LONG => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_LONG => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_RECEIVER_MAXIMUM => array(
			'color' => 'cyan',
		),
		self::MOUNT_RAIL_FRONT_TOP_SHORT => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_TOP_MEDIUM => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_TOP_LONG => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_TOP_MAXIMUM => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_BOTTOM_PISTOL => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_BOTTOM_SHORT => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_BOTTOM_MEDIUM => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_BOTTOM_LONG => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_BOTTOM_MAXIMUM => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_SIDE_SHORT => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_SIDE_MEDIUM => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_SIDE_LONG => array(
			'color' => 'blue',
		),
		self::MOUNT_RAIL_FRONT_SIDE_MAXIMUM => array(
			'color' => 'blue',
		),
		self::MOUNT_PCAP_FRONT_BOTTOM_SHORT => array(
			'color' => 'green',
		),
		self::MOUNT_PCAP_FRONT_BOTTOM_MEDIUM => array(
			'color' => 'green',
		),
		self::MOUNT_PCAP_FRONT_BOTTOM_LONG => array(
			'color' => 'green',
		),
		self::MOUNT_PCAP_FRONT_SIDE_SHORT => array(
			'color' => 'green',
		),
		self::MOUNT_PCAP_FRONT_SIDE_LONG => array(
			'color' => 'green',
		),
		self::MOUNT_HANDGUARD_M4 => array(
			'color' => 'green',
		),
		self::MOUNT_HANDGUARD_M16 => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_PCAP => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_STANAG => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_RINGS => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_AR_HANDLE => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_AK => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_HK => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_FAL => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_FNC => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_MSG => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_SG_S => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_SG_L => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_GROZA => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_GALIL => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_GALATZ => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_M1 => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_MAUSER => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_MOSIN => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_MAGNIFIER => array(
			'color' => 'green',
		),
		self::MOUNT_SCOPE_PISTOL => array(
			'color' => 'green',
		),
		self::MOUNT_SIGHT_PISTOL => array(
			'color' => 'green',
		),
		self::MOUNT_SIGHT_KOBRA => array(
			'color' => 'green',
		),
		self::MOUNT_SIGHT_REFLEX => array(
			'color' => 'green',
		),
		self::MOUNT_SIGHT_MICRO_RDS => array(
			'color' => 'green',
		),
		self::MOUNT_LASER => array(
			'color' => 'red',
		),
		self::MOUNT_LAM_PISTOL => array(
			'color' => 'red',
		),
		self::MOUNT_LAM_RIFLE => array(
			'color' => 'red',
		),
		self::MOUNT_STOCK_FOLDING => array(
			'color' => 'blue',
		),
		self::MOUNT_STOCK_RETRACTABLE => array(
			'color' => 'blue',
		),
		self::MOUNT_CHOKE_SHORT => array(
			'color' => 'yellow',
		),
		self::MOUNT_CHOKE_LONG => array(
			'color' => 'yellow',
		),
		self::MOUNT_SUPPRESSOR => array(
			'color' => 'yellow',
		),
		self::MOUNT_SUPPRESSOR_FLASH => array(
			'color' => 'green',
		),
		self::MOUNT_BARREL_EXTENDER => array(
			'color' => 'green',
		),
		self::MOUNT_KNIFE => array(
			'color' => 'blue',
		),
		self::MOUNT_BIPOD => array(
			'color' => 'green',
		),
		self::MOUNT_FOREGRIP => array(
			'color' => 'green',
		),
		self::MOUNT_RIFLE_SLING => array(
			'color' => 'blue',
		),
		self::MOUNT_BATTERIES => array(
			'color' => 'gray',
		),
		/*
		 * Навесное оружие
		 */
		self::MOUNT_GL_RIFLE => array(
			'color' => 'yellow',
		),
		self::MOUNT_GL_M203PI => array(
			'color' => 'green',
		),
		self::MOUNT_GL_AG36 => array(
			'color' => 'green',
		),
		self::MOUNT_GL_HK79 => array(
			'color' => 'green',
		),
		self::MOUNT_GL_GP30 => array(
			'color' => 'green',
		),
		self::MOUNT_GL_FN_EGLM => array(
			'color' => 'green',
		),
		/*
		 * Несъёмные аттачи
		 */
		self::MOUNT_GL_GP25 => array(
			'color' => 'red',
		),
		self::MOUNT_GL_OICW => array(
			'color' => 'red',
		),
		self::MOUNT_GL_AICW => array(
			'color' => 'red',
		),
		self::MOUNT_GL_F2000 => array(
			'color' => 'red',
		),
	);

	/*
	 * MOUNTS
	 */

	/**
	 * @param Jelly_Model $model
	 * @return array
	 */
	public static function get_mounts(Jelly_Model $model) {
		return Helper::get_json_as_array($model, 'attachment_mounts');
	}

	/**
	 * @param Jelly_Model $model
	 * @return array
	 */
	public static function get_external_mounts(Jelly_Model $model) {
		return Helper::get_json_as_array($model, 'attachment_mounts_external');
	}

	/*
	 * LABELS
	 */

	public static function get_mount_labels(Jelly_Model $model) {
		$attachment_mounts = Attachment::get_mounts($model);
		return self::_get_mount_labels($attachment_mounts);
	}

	public static function get_external_mount_labels(Jelly_Model $model) {
		$attachment_mounts = Attachment::get_external_mounts($model);
		return self::_get_mount_labels($attachment_mounts);
	}

	private static function _get_mount_labels($attachment_mounts) {
		if (empty($attachment_mounts) || !is_array($attachment_mounts)) {
			return '';
		}

		$integrated_mounts = array();

		foreach ($attachment_mounts as $attachment_mount) {

			$name = Helper::get_bonus_caption($attachment_mount);

			if (array_key_exists($attachment_mount, self::$mounts)) {
				$label = Force_Label::factory($name);
				$params = self::$mounts[$attachment_mount];
				Helper::set_label_color($label, $params);

				$integrated_mounts[] = $label->render();
			} else {
				$integrated_mounts[] = Force_Label::factory($name)
					->render();
			}
		}
		return implode(' ', $integrated_mounts);
	}

	/*
	 * HAS CHECK
	 */

	public static function has_mount(Jelly_Model $model, $attachment_mount, $must_have_all = false) {
		$attachment_mounts = Attachment::get_mounts($model);
		return self::_has_mount($attachment_mounts, $attachment_mount, $must_have_all);
	}

	public static function has_external_mount(Jelly_Model $model, $attachment_mount, $must_have_all = false) {
		$attachment_mounts = Attachment::get_external_mounts($model);
		return self::_has_mount($attachment_mounts, $attachment_mount, $must_have_all);
	}

	private static function _has_mount($attachment_mounts, $attachment_mount, $must_have_all = false) {
		if (is_array($attachment_mount)) {
			if ($must_have_all) {
				$count = count($attachment_mount);
				$match = 0;
				foreach ($attachment_mount as $mount) {
					if (array_key_exists($mount, $attachment_mounts)) {
						$match++;
					}
				}
				return ($count == $match);
			} else {
				foreach ($attachment_mount as $mount) {
					if (array_key_exists($mount, $attachment_mounts)) {
						return true;
					}
				}
			}
		} else {
			return array_key_exists($attachment_mount, $attachment_mounts);
		}
		return false;
	}

	/*
	 * INSTANCE INIT
	 */

	protected function init_mount() {
		$this->mounts_list = array();
		foreach (self::$mounts as $mount => $params) {
			$this->mounts_list[$mount] = $mount;
		}
	}

	/*
	 * INSTANCE ONLY
	 */

	/**
	 * @return array
	 */
	public function get_mount_list() {
		return $this->mounts_list;
	}

} // End Attachment_Mount
