<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Weapon
 * User: legion
 * Date: 05.05.18
 * Time: 5:05
 */
class Model_Weapon extends Model_Weapon_Group {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('weapons');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer(),
			'szWeaponName' => new Jelly_Field_String(),
			'ubWeaponClass' => new Jelly_Field_Integer(),
			'ubWeaponType' => new Jelly_Field_Integer(),
			'ubCalibre' => new Jelly_Field_Integer(),
			'ubReadyTime' => new Jelly_Field_Float(),

			'ubShotsPer4Turns' => new Jelly_Field_Integer(),
			'ubShotsPerBurst' => new Jelly_Field_Integer(),
			'ubBurstPenalty' => new Jelly_Field_Integer(),
			'ubBulletSpeed' => new Jelly_Field_Integer(),

			'ubImpact' => new Jelly_Field_Integer(),
			'ubDeadliness' => new Jelly_Field_Integer(),
			'bAccuracy' => new Jelly_Field_Integer(),
			'ubMagSize' => new Jelly_Field_Integer(),
			'usRange' => new Jelly_Field_Integer(),

			'usReloadDelay' => new Jelly_Field_Integer(),
			'BurstAniDelay' => new Jelly_Field_Integer(),

			'ubAttackVolume' => new Jelly_Field_Integer(),
			'ubHitVolume' => new Jelly_Field_Integer(),

			'sSound' => new Jelly_Field_Integer(),
			'sBurstSound' => new Jelly_Field_Integer(),
			'sSilencedBurstSound' => new Jelly_Field_Integer(),
			'sReloadSound' => new Jelly_Field_Integer(),
			'sLocknLoadSound' => new Jelly_Field_Integer(),
			'SilencedSound' => new Jelly_Field_Integer(),

			'bBurstAP' => new Jelly_Field_Integer(),
			'bAutofireShotsPerFiveAP' => new Jelly_Field_Integer(),
			'APsToReload' => new Jelly_Field_Integer([
				'default' => Core_Weapon::DEFAULT_APS_TO_RELOAD,
			]),
			'SwapClips' => new Jelly_Field_Integer(),

			'MaxDistForMessyDeath' => new Jelly_Field_Integer(),
			'AutoPenalty' => new Jelly_Field_Integer(),

			'NoSemiAuto' => new Jelly_Field_Integer(),
			'EasyUnjam' => new Jelly_Field_Integer(),
			'APsToReloadManually' => new Jelly_Field_Integer(),
			'ManualReloadSound' => new Jelly_Field_Integer(),

			'nAccuracy' => new Jelly_Field_Integer(),
			'bRecoilX' => new Jelly_Field_Integer(),
			'bRecoilY' => new Jelly_Field_Integer(),
			'ubAimLevels' => new Jelly_Field_Integer(),
			'ubRecoilDelay' => new Jelly_Field_Integer(),
			'Handling' => new Jelly_Field_Integer(),
			'usOverheatingJamThreshold' => new Jelly_Field_Integer(),
			'usOverheatingDamageThreshold' => new Jelly_Field_Integer(),
			'usOverheatingSingleShotTemperature' => new Jelly_Field_Integer(),
			'HeavyGun' => new Jelly_Field_Integer(),
		));
	}

} // End Model_Weapon