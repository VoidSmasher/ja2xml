<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Weapon_Mod
 * User: legion
 * Date: 05.05.18
 * Time: 5:05
 */
class Model_Weapon_Mod extends Model_Weapon_Group {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('weapons_mod');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer(),
			'szWeaponName' => new Jelly_Field_String([
				'description' => 'The weapon name, 20 characters maximum',
			]),
			'ubWeaponClass' => new Jelly_Field_Integer([
				'description' => "The weapon class.\n0 - NOGUNCLASS\n1 - HANDGUNCLASS\n2 - SMGCLASS\n3 - RIFLECLASS\n4 - MGCLASS\n5 - SHOTGUNCLASS\n6 - KNIFECLASS\n7 - MONSTERCLASS",
			]),
			'ubWeaponType' => new Jelly_Field_Integer([
				'description' => "Exact weapon type.\n0 - NOT_GUN\n1 - GUN_PISTOL\n2 - GUN_M_PISTOL\n3 - GUN_SMG\n4 - GUN_RIFLE\n5 - GUN_SN_RIFLE\n6 - GUN_AS_RIFLE\n7 - GUN_LMG\n8 - GUN_SHOTGUN",
			]),
			'ubCalibre' => new Jelly_Field_Integer([
				'description' => 'What calibre of ammo the the gun uses, look below for a listing (also dictates the burst sound).',
			]),
			'ubReadyTime' => new Jelly_Field_Integer([
				'label' => 'APs to Ready',
				'description' => "How many APs are spend to get this weapon into the \"ready\" state.\nLower is better.",
			]),

			'ubShotsPer4Turns' => new Jelly_Field_Float([
				'label' => 'Shots Per 4 Turns',
				'description' => 'The weapon\'s rate of fire for single shots.',
			]),
			'ubShotsPerBurst' => new Jelly_Field_Integer([
				'label' => 'Shots Per Burst',
				'description' => 'The number of bullets this weapon shoots per burst.',
			]),
			'ubBurstPenalty' => new Jelly_Field_Integer([
				'description' => 'The % chance to hit that is deducted for each shot of the burst (later shots in the burst are lass accurate).',
			]),
			'ubBulletSpeed' => new Jelly_Field_Integer([
				'description' => 'How fast bullets fly out of the gun (aesthetic consideration only).',
			]),

			'ubImpact' => new Jelly_Field_Integer([
				'label' => 'Damage',
				'description' => 'This has an effect on how much damage the bullets do.',
			]),
			'ubDeadliness' => new Jelly_Field_Integer([
				'description' => 'This is the "scare" value of the gun. Mercs complain if their guns are too low in this.',
			]),
			'bAccuracy' => new Jelly_Field_Integer([
				'label' => 'Bonus Accuracy',
				'description' => 'Straight to-hit bonus added to the gun.',
			]),
			'ubMagSize' => new Jelly_Field_Integer([
				'label' => 'Mag. Size',
				'description' => 'Number of bullets that can be loaded into the gun.',
			]),
			'usRange' => new Jelly_Field_Integer([
				'label' => 'Range (m)',
				'description' => 'Weapon\'s maximum range (in meters, 1 tile = 10 meters).',
			]),

			'usReloadDelay' => new Jelly_Field_Integer([
				'label' => 'Reload Delay',
				'description' => 'Not used anywhere right now, but could be used to specify the reload speed in RT.',
			]),
			'BurstAniDelay' => new Jelly_Field_Integer([
				'label' => 'Burst Animation Delay',
			]),

			'ubAttackVolume' => new Jelly_Field_Integer([
				'label' => 'Attack Volume',
				'description' => 'How much "noise" the weapon makes (in tiles).',
			]),
			'ubHitVolume' => new Jelly_Field_Integer([
				'label' => 'Hit Volume',
				'description' => 'How much "noise" the bullet makes when it hits something (in tiles).',
			]),

			'sSound' => new Jelly_Field_Integer([
				'label' => 'Sound',
				'description' => 'The sound that\'s used for single shot (and burst if the burst sound is missing).',
			]),
			'sBurstSound' => new Jelly_Field_Integer([
				'label' => 'Burst Sound',
				'description' => 'The "default" burst sound. This is not used anymore. However, set it to something other than 0 if you want to hear bursts.',
			]),
			'sSilencedBurstSound' => new Jelly_Field_Integer([
				'label' => 'Silenced Burst Sound',
			]),
			'sReloadSound' => new Jelly_Field_Integer([
				'label' => 'Reload Sound',
				'description' => 'The sound of reloading.',
			]),
			'sLocknLoadSound' => new Jelly_Field_Integer([
				'label' => 'Lock&Load Sound',
				'description' => 'This sound is used in AutoResolve and sometimes for rifles in tactical.',
			]),
			'SilencedSound' => new Jelly_Field_Integer([
				'label' => 'Silenced Sound',
			]),

			'bBurstAP' => new Jelly_Field_Integer([
				'label' => 'Burst AP',
				'description' => 'The AP cost of burst.',
			]),
			'bAutofireShotsPerFiveAP' => new Jelly_Field_Integer([
				'label' => 'Auto Fire Shorts Per 5 AP',
				'description' => 'This is the number of shots you can fire using this weapon using 5 APs (ie. 25 = 5 shots per AP).',
			]),
			'APsToReload' => new Jelly_Field_Integer([
				'label' => 'APs To Reload',
				'description' => 'The AP cost to replace magazine.',
				'default' => Core_Weapon_Mod::DEFAULT_APS_TO_RELOAD,
			]),
			'SwapClips' => new Jelly_Field_Integer([
				'label' => 'Swap Clips',
				'description' => '1 = normal behaviour, 0 = when you reload in turn based combat, you will add to the rounds in the gun instead of replacing the magazine (ie: shotguns).',
			]),

			'MaxDistForMessyDeath' => new Jelly_Field_Integer([
				'Maximum Range for Critical Kill',
				'description' => 'This is the maximum range in tiles to which a critical hit from the weapon can instantly kill an enemy (accompanied by "messy" animation).',
			]),
			'AutoPenalty' => new Jelly_Field_Integer([
				'label' => 'Auto Penalty',
				'description' => 'The % chance to hit that is deducted for each shot of auto fire.',
			]),

			'NoSemiAuto' => new Jelly_Field_Integer([
				'label' => 'No Semi Auto',
				'description' => 'The gun has full auto only.',
			]),
			'EasyUnjam' => new Jelly_Field_Integer([
				'label' => 'Easy Unjam',
			]),
			'APsToReloadManually' => new Jelly_Field_Integer([
				'label' => 'APs To Reload Manually',
				'description' => 'How many APs are spend for manual reload, if > 0 than gun will change the gun to non self loading.',
			]),
			'ManualReloadSound' => new Jelly_Field_Integer([
				'label' => 'Manual Reload Sound',
				'description' => 'Sound index for manual reload.',
			]),

			'nAccuracy' => new Jelly_Field_Integer([
				'label' => 'Accuracy',
				'description' => 'nCTH accuracy.'
			]),
			'bRecoilX' => new Jelly_Field_Integer([
				'label' => 'Horizontal Recoil',
				'description' => "Positive values means bullet diversion to right side.\nNegative values - to left.",
			]),
			'bRecoilY' => new Jelly_Field_Integer([
				'label' => 'Vertical Recoil',
				'description' => "Positive values means bullet diversion to top.\nNegative values - to bottom.",
			]),
			'ubAimLevels' => new Jelly_Field_Integer(),
			'ubRecoilDelay' => new Jelly_Field_Integer([
				'label' => 'Recoil Delay',
				'description' => "Render recoil after number of shots fired. Used for HK G11 and AN-94 Abakan.",
			]),
			'Handling' => new Jelly_Field_Integer([
				'label' => 'Handling difficulty',
				'description' => "Initially equal to ubReadyTime.\nMinimum value is 1.\nLower is better.",
			]),

			'usOverheatingJamThreshold' => new Jelly_Field_Integer(),
			'usOverheatingDamageThreshold' => new Jelly_Field_Integer(),
			'usOverheatingSingleShotTemperature' => new Jelly_Field_Integer(),
			'HeavyGun' => new Jelly_Field_Integer(),
		));
	}

} // End Model_Weapon_Mod