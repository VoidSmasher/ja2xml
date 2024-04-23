<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Item_Mod
 * User: legion
 * Date: 08.05.18
 * Time: 21:13
 * @property integer uiIndex
 * @property string szItemName
 * @property string szLongItemName
 * @property string szItemDesc
 * @property string szBRName
 * @property string szBRDesc
 * @property integer usItemClass
 * @property integer AttachmentClass
 * @property integer nasAttachmentClass
 * @property integer nasLayoutClass

 * @property string DefaultAttachment

 * @property integer ubWeight
 * @property integer ubCoolness
 * @property integer ItemSize
 * @property integer PercentNoiseReduction
 */
class Model_Item_Mod extends Model_Weapon_Group {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('items_mod');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary([]),

			'uiIndex' => new Jelly_Field_Integer([
				'description' => 'The item index, number the items sequentially starting at 1 and ending at 5000. Don\'t skip numbers or the game will crash. Do not use items #0 and #70. Do not use the same number for two different items.',
			]),
			'szItemName' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The short name of the item.',
				'convert_empty' => true,
			]),
			'szLongItemName' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The item\'s long name, as displayed in the item description box.',
				'convert_empty' => true,
			]),
			'szItemDesc' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The item\'s description, as displayed in the item description box.',
			]),
			'szBRName' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The name of the item at Bobby Ray\'s (usually the same as the long name)',
				'convert_empty' => true,
			]),
			'szBRDesc' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The description of the item displayed at Bobby Ray\'s',
				'convert_empty' => true,
			]),

			'usItemClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'The type of the item (gun, blade, launcher, bomb, etc.) - this should never be zero! If the code sees a zero ItemClass, it will think the list of items has ended. If you need space between items, place Nothing types between them. The values you can use are listed at the bottom of this page.',
			]),
			'AttachmentClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'nasAttachmentClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'nasLayoutClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'AvailableAttachmentPoint' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'convert_empty' => true,
			]),
			'AttachmentPoint' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'AttachToPointAPCost' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'ubClassIndex' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'The index of the item in the other files (weapons, explosives, magazines, etc.) NB: the weapons.xml index should be the same as the items.xml uiIndex, the other\'s just need to point to the right uiIndex in the other file.',
			]),
			'ItemFlag' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'ubCursor' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'The cursor displayed when the item is used. See the bottom of this page for a list of cursors.',
			]),
			'bSoundType' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'Does nothing, not sure what this was used for.',
			]),
			'ubGraphicType' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'The TYPE of graphics file used for an item, ie: 0 = MDGUNS.sti, 1 = P1ITEMS.sti, 2 = P2ITEMS.sti. For new items, just use 0 for guns, and 1 for other items, otherwise they may not display correctly in-game.',
			]),
			'ubGraphicNum' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'The number of the graphic for the item, as indexed in the sti file you referenced with ubGraphicType.',
			]),

			'ubWeight' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => 'The weight of the item, in 100g units, so 77 = 7.7 kg.',
			]),

			'ubPerPocket' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => 'The quantity of this item that can fit in one large pocket. This is halved for small pockets, so a value of 1 makes sure you can only carry the item in a large pocket. Guns should always be zero.',
			]),
			'ItemSize' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),
			'ItemSizeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),

			'usPrice' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),

			'ubCoolness' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => '(1-10) How far along in the game you\'ll see the item. Zero means that it\'s not cool at all and won\'t be sold anywhere.',
			]),

			'bReliability' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => 'How quickly the item loses status. Positive numbers reduce status loss, negative numbers increase it.',
			]),
			'bRepairEase' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => 'How easy the item is to repair.',
			]),
			'Damageable' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can the item be damaged?',
			]),
			'Repairable' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can the item be repaired?',
			]),
			'WaterDamages' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Water Damages',
				'description' => '(1/0) Is the item damaged when it is submerged?',
			]),
			'Metal' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is the item made of metal? (used for metal detector and for reducing damage to the item)',
			]),
			'Sinks' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Will the item sink under water to be lost forever?',
			]),
			'ShowStatus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Show Status',
				'description' => '(1/0) is the item\'s status displayed? (Status - vertical bar showing item status)',
			]),
			'HiddenAddon' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Hidden Addon',
				'description' => '(1/0) Is the item a hidden attachment?',
			]),

			'TwoHanded' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Two Handed',
				'description' => '(1/0) Does the item require two hands to use? (Left hand slot will disappear when the item is placed in the right hand slot).',
			]),
			'NotBuyable' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Not Buyable',
				'description' => '(1/0) Is the item not purchasable?',
			]),
			'Attachment' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is this item an attachment? (if so, you\'ll need to open attachments.xml to set what it can be attached to)',
			]),
			'HiddenAttachment' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Hidden Attachment',
			]),
			'BlockIronSight' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Block Iron Sight',
			]),

			'BigGunList' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Big Gun List',
				'description' => '(1/0) not used anymore in 1.13',
			]),
			'SciFi' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Sci Fi',
			]),
			'NotInEditor' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Not In Editor',
				'description' => '(1/0) So far unused.',
			]),
			'DefaultUndroppable' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Default Undroppable',
				'description' => '(1/0) Is item not droppable by enemies? (Drop everything option overrides this)',
			]),
			'Unaerodynamic' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Unaero dynamic',
				'description' => '(1/0) Can the item only be thrown one-two squares?',
			]),
			'Electronic' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is the item electronic? (used in conjunction with Electronics specialty for repairs and attaching, etc.)',
			]),
			'Inseparable' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is the item a permanent attachment?',
			]),

			'BR_NewInventory' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'label' => 'BR New Inventory',
				'description' => '(>=0) Bobby Ray\'s optimal inventory stock of this item, in 100% condition (the actual quantity will vary, depending mostly on the Bobby Ray mode selected when starting a new game)',
			]),
			'BR_UsedInventory' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'BR Used Inventory',
				'description' => '(1/0) Does Bobby Ray offer this item in less than 100% condition? (Used items list)',
			]),
			'BR_ROF' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => '(>=0) Weapon\'s Rate of Fire as displayed on Bobby Rays (doesn\'t affect the gun\'s actual firing speed).',
			]),

			'PercentNoiseReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Noise Reduction',
				'description' => '(<=100) Percent noise reduction (silencer). Higher = More silent.',
			]),
			'HideMuzzleFlash' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Hide Muzzle Flash',
				'description' => '(1/0) suppresses muzzle flash.',
			]),
			'Bipod' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'description' => '(number) Prone to-hit bonus',
			]),

			'RangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Range Bonus',
				'description' => 'Weapon range bonus, in meters (10 = 1 tile).',
			]),
			'PercentRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Range Bonus',
			]),
			'ToHitBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'To Hit Bonus',
				'description' => 'Straight forward (flat) to-hit bonus (laser scope). Adds an exact amount of bonus once per shot, regardless of aiming, range, or the firing mode used.',
			]),
			'BestLaserRange' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Best Laser Range',
				'description' => 'The laser scope gives full bonus (ToHitBonus) within this range. Beyond this range, the bonus diminishes. Set to 0 if N/A.',
			]),
			'AimBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Aim Bonus',
				'description' => 'Aimed shot bonus (sniper scope). Adds a bonus to Chance-To-Hit per every aiming AP.',
			]),
			'MinRangeForAimBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_ROUND => Core_Item::FIELD_ROUND_TO_FIVE,
				'label' => 'Min Range For Aim Bonus',
				'description' => 'Minimum range at which any aim bonus is received (multiple attachments with this limitation will use the lowest min. range)',
			]),

			'MagSizeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Mag Size Bonus',
				'description' => 'Bonus to the magazine capacity (ubMagSize stat) of a weapon. Note that this bonus when combined with the weapon\'s ubMagSize must equal to a valid clip size, or else weird shit will happen. Ie: MP-5N (30 rds) + C-Mag adapter (70 rd bonus) = 100 = 100 rd 9mm C-Mag. OK. Glock 18 (15 rds) + C-Mag adapter (70 rd) = 85 rds... no corresponding clip size. NOT OK.',
			]),

			'RateOfFireBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Rate Of Fire Bonus',
				'description' => 'Bonus to the rate of fire of a weapon (Only on Bobby Ray\'s stats display? Can anyone clarify this?)',
			]),
			'BulletSpeedBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Bullet Speed Bonus',
				'description' => 'Bonus to the speed at which bullets are fired from a weapon. This is entirely cosmetic, and has no effect on gameplay.',
			]),

			'BurstSizeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'label' => 'Burst Size Bonus',
				'description' => 'Bonus to the number of bullets fired in set-burst mode.',
			]),
			'BurstToHitBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Burst To Hit Bonus',
				'description' => 'Reduces or increases the ubBurstPenalty stat of the weapon.',
			]),
			'AutoFireToHitBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Auto Fire To Hit Bonus',
				'description' => 'Bonus to a gun\'s bAutofireShotsPerFiveAP stat.',
			]),
			'APBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'AP Bonus',
			]),

			'PercentBurstFireAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Burst Fire AP Reduction',
				'description' => 'Percentile bonus towards reducing base burst fire aps for a weapon.',
			]),
			'PercentAutofireAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Auto Fire AP Reduction',
				'description' => 'Percentile bonus towards reducing base auto fire aps for a weapon.',
			]),
			'PercentReadyTimeAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Ready Time AP Reduction',
				'description' => 'Percentile bonus towards reducing aps required to ready a weapon.',
			]),
			'PercentReloadTimeAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Reload Time AP Reduction',
				'description' => 'Percentile bonus towards reducing required time to reload a weapon.',
			]),
			'PercentAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent AP Reduction',
				'description' => 'Percentile bonus towards reducing required aps to use weapon item. This affects the weapon\'s ShotsPer4Turns.',
			]),
			'PercentStatusDrainReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Status Drain Reduction',
				'description' => 'Affects how quickly an item degrades through use. Affects any items that degrade through use (including kits, weapons, etc).',
			]),

			'DamageBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Damage Bonus',
				'description' => 'Damage bonus. Applies only to guns.',
			]),
			'MeleeDamageBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Melee Damage Bonus',
				'description' => 'Hand to hand damage bonus. For now just applies to brass knuckles and knives',
			]),

			'GrenadeLauncher' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Grenade Launcher',
				'description' => '(1/0) launches grenades? Can be assigned to guns, launchers or attachments.',
			]),
			'Duckbill' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '1/0) acts as Duckbill? (Spreads buckshot ammo, and any associated range bonuses only apply to buckshot ammo)',
			]),
			'GLGrenade' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'GL Grenade',
				'description' => '(1/0) Can this item be launched in a grenade launcher? (must also be in launchables.xml for applicable launcher)',
			]),
			'Mine' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
			]),
			'Mortar' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
			]),
			'RocketLauncher' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Rocket Launcher',
			]),
			'SingleShotRocketLauncher' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Single Shot Rocket Launcher',
				'description' => '(1/0) is a single shot rocket launcher? (LAW) Must define a DiscardedLauncherItem',
			]),
			'DiscardedLauncherItem' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Discarded Launcher Item',
				'description' => 'uiIndex of discarded launcher for single shot launchers',
			]),
			'RocketRifle' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Rocket Rifle',
				'description' => '(1/0) does this item launch small missiles? (rocket rifle)',
			]),
			'Cannon' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this weapon a tank cannon?',
			]),

			'DefaultAttachment' => new Jelly_Field_Text([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_SUMMARY_JSON,
				'label' => 'Default Attachment',
				'description' => 'uiIndex of an attachment that comes with this item by default (when bought, dropped by enemies, etc.)',
				'convert_empty' => true,
			]),

			'BrassKnuckles' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this a punching weapon that\'s valid in the boxing ring?',
			]),
			'Crowbar' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this a crowbar-type item that can be used to force open locks and make melee attacks?',
			]),
			'BloodiedItem' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => 'Item number of thrown item after it hits its mark and becomes bloody (if zero, the item will never turn bloody)',
			]),
			'Rock' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this item a rock that can be used to distract enemies?',
			]),

			'CamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Camo Bonus',
				'description' => 'Percent camouflage bonus granted by the item when it is worn (or attached to a worn item...?)',
			]),
			'UrbanCamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Urban Camo Bonus',
				'description' => 'Percent camouflage bonus granted by the item when it is worn (or attached to a worn item...?)',
			]),
			'DesertCamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Desert Camo Bonus',
				'description' => 'Percent camouflage bonus granted by the item when it is worn (or attached to a worn item...?)',
			]),
			'SnowCamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Snow Camo Bonus',
				'description' => 'Percent camouflage bonus granted by the item when it is worn (or attached to a worn item...?)',
			]),
			'StealthBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Stealth Bonus',
			]),

			'FlakJacket' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this item a flak jacket? (greatly reduces damage from explosions).',
			]),
			'LeatherJacket' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this item a Leather Jacket? (meets Nails\' leather jacket fetish, won\'t be worn by enemy soldiers).',
			]),
			'Directional' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'RemoteTrigger' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) is this item a remote bomb trigger?',
			]),
			'LockBomb' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can this item blow up locks? (Shaped charge)',
			]),
			'Flare' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) break light?',
			]),
			'RobotRemoteControl' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) robot remote?',
			]),
			'Walkman' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Walkman effect? (Increases morale, but blocks all sounds...)',
			]),

			'HearingRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Hearing Range Bonus',
				'description' => 'Bonus to hearing granted by object. Reduced by object\'s status. (ext. ear)',
			]),
			'VisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_ROUND => Core_Item::FIELD_ROUND_TO_FIVE,
				'label' => 'Vision Range Bonus',
				'description' => 'Bonus to visible range (General. Always applied)',
			]),
			'NightVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_ROUND => Core_Item::FIELD_ROUND_TO_FIVE,
				'label' => 'Night Vision Range Bonus',
				'description' => 'Bonus to visible range at night',
			]),
			'DayVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_ROUND => Core_Item::FIELD_ROUND_TO_FIVE,
				'label' => 'Day Vision Range Bonus',
				'description' => 'Bonus to visible range during the day',
			]),
			'CaveVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_ROUND => Core_Item::FIELD_ROUND_TO_FIVE,
				'label' => 'Cave Vision Range Bonus',
				'description' => 'Bonus to visible range underground',
			]),
			'BrightLightVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_ROUND => Core_Item::FIELD_ROUND_TO_FIVE,
				'label' => 'Bright Light Vision Range Bonus',
				'description' => 'Bonus to visible range in bright light (more than regular Daylight intensity). Cumulative with DayVisionRangeBonus.',
			]),
			'PercentTunnelVision' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'label' => 'Percent Tunnel Vision',
				'description' => '(0-100) % of peripheral vision lost. Higher = narrower line of sight.',
			]),

			'ThermalOptics' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Thermal vision enabled? (see through walls)',
			]),
			'GasMask' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Gas protection?',
			]),
			'Alcohol' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Beer, wine, etc. Will cause drunkness when used.',
			]),
			'Hardware' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Flag for shopkeepers, incl. toolkits, etc.',
			]),
			'Medical' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Flag for shopkeepers, incl. med kits, etc.',
			]),
			'DrugType' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'CamouflageKit' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to apply camo?',
			]),
			'LocksmithKit' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to pick locks?',
			]),
			'Toolkit' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to repair items?',
			]),
			'FirstAidKit' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to repair people?',
			]),
			'MedicalKit' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) As first aid kit, plus can be used when assigning people to DOCTOR duty.',
			]),
			'WireCutters' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to cut through fences?',
			]),
			'Canteen' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to regain energy? (aahhh!)',
			]),
			'GasCan' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to refuel vehicles?',
			]),
			'Marbles' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can drop to make enemies slip?',
			]),
			'CanAndString' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use on a door as a noise-alarm?',
			]),
			'Jar' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can use to store liquids?',
			]),
			'XRay' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) X-ray device?',
			]),
			'Batteries' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Batteries?',
			]),
			'NeedsBatteries' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Needs Batteries?',
			]),
			'ContainsLiquid' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Contains Liquid? (for jar items that contain liquid...)',
			]),
			'MetalDetector' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can detect mines?',
			]),
			'FingerPrintID' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Finger Print ID',
				'description' => '(1/0) Requires a Finger Print ID before use is possible? (weapons only, I think)',
			]),

			'TripWireActivation' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'TripWire' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'NewInv' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'AttachmentSystem' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'ScopeMagFactor' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'label' => 'Scope Mag Factor',
			]),
			'ProjectionFactor' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'label' => 'Projection Factor',
			]),
			'RecoilModifierX' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
			]),
			'RecoilModifierY' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
			]),
			'PercentRecoilModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
			]),
			'PercentAccuracyModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
			]),

			'spreadPattern' => new Jelly_Field_String([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'label' => 'spread Pattern',
				'convert_empty' => true,
			]),

			'barrel' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'usOverheatingCooldownFactor' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
				'label' => 'Overheating Cooldown Factor',
				'default' => 100,
			]),
			'overheatTemperatureModificator' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),
			'overheatCooldownModificator' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),
			'overheatJamThresholdModificator' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),
			'overheatDamageThresholdModificator' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),

			'PoisonPercentage' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_MAX,
			]),
			'FoodType' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'LockPickModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'CrowbarModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'DisarmModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'RepairModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'DamageChance' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Damage Chance',
				'description' => 'Chance that damage to the status will also damage the repair threshold',
			]),
			'DirtIncreaseFactor' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Dirt Increase Factor',
			]),

			'clothestype' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'usActionItemFlag' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'randomitem' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'randomitemcoolnessmodificator' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'FlashLightRange' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
			]),

			'ItemChoiceTimeSetting' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'buddyitem' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'SleepModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'usSpotting' => new Jelly_Field_Integer([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'diseaseprotectionface' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),
			'diseaseprotectionhand' => new Jelly_Field_Float([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
			]),

			'STAND_MODIFIERS' => new Jelly_Field_Text([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT_JSON,
				'convert_empty' => true,
				'allow_null' => true,
			]),
			'CROUCH_MODIFIERS' => new Jelly_Field_Text([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT_JSON,
				'convert_empty' => true,
				'allow_null' => true,
			]),
			'PRONE_MODIFIERS' => new Jelly_Field_Text([
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT_JSON,
				'convert_empty' => true,
				'allow_null' => true,
			]),
		));
	}

} // End Model_Item_Mod