<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Attachment_Data
 * User: legion
 * Date: 13.10.19
 * Time: 1:27
 * @property integer id
 * @property integer uiIndex
 * @property string szLongItemName
 * @property string szItemName
 * @property string szItemDesc
 * @property string szBRName
 * @property string szBRDesc
 * @property integer APCost
 * @property integer is_integrated
 * @property integer is_fixed
 * @property integer attachment_bonuses
 * @property integer attachment_mounts
 * @property integer attachment_mounts_external
 * @property string attachment_types
 * @property string default_attachments
 * @property string possible_attachments
 * @property integer depends_on_items
 * @property integer AttachmentClass
 * @property integer nasAttachmentClass
 * @property integer nasLayoutClass
 * @property integer ubWeight
 * @property integer ItemSize
 * @property integer ItemSizeBonus
 * @property integer ubCoolness
 * @property integer bReliability
 * @property integer bRepairEase
 * @property integer Damageable
 * @property integer Repairable
 * @property integer WaterDamages
 * @property integer Metal
 * @property integer Sinks
 * @property integer ShowStatus
 * @property integer HiddenAddon
 * @property integer Attachment
 * @property integer HiddenAttachment
 * @property integer BlockIronSight
 * @property integer Electronic
 * @property integer Inseparable
 * @property integer BR_NewInventory
 * @property integer BR_UsedInventory
 * @property integer PercentNoiseReduction
 * @property integer HideMuzzleFlash
 * @property integer Bipod
 * @property integer RangeBonus
 * @property integer PercentRangeBonus
 * @property integer ToHitBonus
 * @property integer BestLaserRange
 * @property integer FlashLightRange
 * @property integer AimBonus
 * @property integer MinRangeForAimBonus
 * @property integer MagSizeBonus
 * @property integer BurstSizeBonus
 * @property integer BurstToHitBonus
 * @property integer AutoFireToHitBonus
 * @property integer CamoBonus
 * @property integer UrbanCamoBonus
 * @property integer DesertCamoBonus
 * @property integer SnowCamoBonus
 * @property integer StealthBonus
 * @property integer PercentBurstFireAPReduction
 * @property integer PercentAutofireAPReduction
 * @property integer PercentReadyTimeAPReduction
 * @property integer PercentReloadTimeAPReduction
 * @property integer PercentAPReduction
 * @property integer DamageBonus
 * @property integer MeleeDamageBonus
 * @property integer VisionRangeBonus
 * @property integer NightVisionRangeBonus
 * @property integer DayVisionRangeBonus
 * @property integer CaveVisionRangeBonus
 * @property integer BrightLightVisionRangeBonus
 * @property integer PercentTunnelVision
 * @property float ScopeMagFactor
 * @property float ProjectionFactor
 * @property float RecoilModifierX
 * @property float RecoilModifierY
 * @property integer PercentRecoilModifier
 * @property integer PercentAccuracyModifier
 * @property integer AttachmentSystem
 * @property string STAND_MODIFIERS
 * @property string CROUCH_MODIFIERS
 * @property string PRONE_MODIFIERS
 */
class Model_Attachment_Data extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('data_attachments');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'uiIndex' => new Jelly_Field_Integer([
				'description' => 'The item index, number the items sequentially starting at 1 and ending at 5000. Don\'t skip numbers or the game will crash. Do not use items #0 and #70. Do not use the same number for two different items.',
			]),

			'szLongItemName' => new Jelly_Field_String([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The item\'s long name, as displayed in the item description box.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'szItemName' => new Jelly_Field_String([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The short name of the item.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'szItemDesc' => new Jelly_Field_String([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The item\'s description, as displayed in the item description box.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'szBRName' => new Jelly_Field_String([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The name of the item at Bobby Ray\'s (usually the same as the long name)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'szBRDesc' => new Jelly_Field_String([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The description of the item displayed at Bobby Ray\'s',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'APCost' => new Jelly_Field_Integer([
				'default' => 20,
			]),

			'is_integrated' => new Jelly_Field_Integer([
				'label' => 'Integrated Attachment',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'is_fixed' => new Jelly_Field_Integer([
				'label' => 'Fixed',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'attachment_bonuses' => new Jelly_Field_Text([
				'label' => 'Attachment Bonuses',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'attachment_mounts' => new Jelly_Field_Text([
				'label' => 'Attachment Mounts',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'attachment_mounts_external' => new Jelly_Field_Text([
				'label' => 'External Attachment Mounts',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'attachment_types' => new Jelly_Field_Text([
				'label' => 'Attachment Types',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'default_attachments' => new Jelly_Field_Text([
				'label' => 'Default Attachments',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'possible_attachments' => new Jelly_Field_Text([
				'label' => 'Possible Attachments',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),
			'depends_on_items' => new Jelly_Field_String([
				'label' => 'Depends on Items',
				'description' => 'Items uiIndex divided by comma',
				'allow_null' => true,
				'convert_empty' => true,
				'default' => NULL,
			]),

			'usItemClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'label' => 'us Item Class',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'AttachmentClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'label' => 'Attachment Class',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'nasAttachmentClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'label' => 'nas Attachment Class',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'nasLayoutClass' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'label' => 'nas Layout Class',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

//			'AttachmentPoint' => new Jelly_Field_Integer([
//				'label' => 'Attachment Point',
//				'allow_null' => true,
//				'default' => NULL,
//				'convert_empty' => true,
//			]),
//			'AttachToPointAPCost' => new Jelly_Field_Integer([
//				'label' => 'Attach To Point AP Cost',
//				'allow_null' => true,
//				'default' => NULL,
//				'convert_empty' => true,
//			]),



			'ubGraphicType' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'Images Pack number',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'ubGraphicNum' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'Number of image in the Pack',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'ubWeight' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'The weight of the item, in 100g units, so 77 = 7.7 kg.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'ItemSize' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'label' => 'Item Size',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'ItemSizeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_MERGE_MANUAL,
				'label' => 'Item Size Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'usPrice' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_NOT_EMPTY,
				'description' => 'Item price in dollars',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'ubCoolness' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'description' => '(1-10) How far along in the game you\'ll see the item. Zero means that it\'s not cool at all and won\'t be sold anywhere.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'bReliability' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'description' => 'How quickly the item loses status. Positive numbers reduce status loss, negative numbers increase it.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'bRepairEase' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'description' => 'How easy the item is to repair.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Damageable' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can the item be damaged?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Repairable' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Can the item be repaired?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'WaterDamages' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Water Damages',
				'description' => '(1/0) Is the item damaged when it is submerged?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Metal' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is the item made of metal? (used for metal detector and for reducing damage to the item)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Sinks' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Will the item sink under water to be lost forever?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'ShowStatus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Show Status',
				'description' => '(1/0) Is the item\'s status displayed?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'HiddenAddon' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Hidden Addon',
				'description' => '(1/0) Is the item a hidden attachment?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Attachment' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is this item an attachment? (if so, you\'ll need to open attachments.xml to set what it can be attached to)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'HiddenAttachment' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Hidden Attachment',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'BlockIronSight' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Block Iron Sight',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'Electronic' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is the item electronic? (used in conjunction with Electronics specialty for repairs and attaching, etc.)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Inseparable' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'description' => '(1/0) Is the item a permanent attachment?',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'BR_NewInventory' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				'label' => 'BR New Inventory',
				'description' => '(>=0) Bobby Ray\'s optimal inventory stock of this item, in 100% condition (the actual quantity will vary, depending mostly on the Bobby Ray mode selected when starting a new game)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'BR_UsedInventory' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'BR Used Inventory',
				'description' => '(1/0) Does Bobby Ray offer this item in less than 100% condition? (Used items list)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'PercentNoiseReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Noise Reduction',
				'description' => '(<=100) Percent noise reduction (silencer). Higher = More silent.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'HideMuzzleFlash' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE_BOOLEAN,
				'label' => 'Hide Muzzle Flash',
				'description' => '(1/0) suppresses muzzle flash.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'Bipod' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'description' => '(number) Prone to-hit bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'RangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Range Bonus',
				'description' => 'Weapon range bonus, in meters (10 = 1 tile).',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Range Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'ToHitBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'To Hit Bonus',
				'description' => 'Straight forward (flat) to-hit bonus (laser scope). Adds an exact amount of bonus once per shot, regardless of aiming, range, or the firing mode used.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'BestLaserRange' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Best Laser Range',
				'description' => 'The laser scope gives full bonus (ToHitBonus) within this range. Beyond this range, the bonus diminishes. Set to 0 if N/A.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'FlashLightRange' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Flash Light Range',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'AimBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Aim Bonus',
				'description' => 'Aimed shot bonus (sniper scope). Adds a bonus to Chance-To-Hit per every aiming AP.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'MinRangeForAimBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Min Range For Aim Bonus',
				'description' => 'Minimum range at which any aim bonus is received (multiple attachments with this limitation will use the lowest min. range)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'MagSizeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'label' => 'Mag Size Bonus',
				'description' => 'Bonus to the magazine capacity (ubMagSize stat) of a weapon. Note that this bonus when combined with the weapon\'s ubMagSize must equal to a valid clip size, or else weird shit will happen. Ie: MP-5N (30 rds) + C-Mag adapter (70 rd bonus) = 100 = 100 rd 9mm C-Mag. OK. Glock 18 (15 rds) + C-Mag adapter (70 rd) = 85 rds... no corresponding clip size. NOT OK.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'BurstSizeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'label' => 'Burst Size Bonus',
				'description' => 'Bonus to the number of bullets fired in set-burst mode.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'BurstToHitBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Burst To Hit Bonus',
				'description' => 'Reduces or increases the ubBurstPenalty stat of the weapon.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'AutoFireToHitBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Auto Fire To Hit Bonus',
				'description' => 'Bonus to a gun\'s bAutofireShotsPerFiveAP stat.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'CamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Camo Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'UrbanCamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Urban Camo Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'DesertCamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Desert Camo Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'SnowCamoBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Snow Camo Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'StealthBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Stealth Bonus',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'PercentBurstFireAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Burst Fire AP Reduction',
				'description' => 'Percentile bonus towards reducing base burst fire aps for a weapon.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentAutofireAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Auto Fire AP Reduction',
				'description' => 'Percentile bonus towards reducing base auto fire aps for a weapon.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentReadyTimeAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Ready Time AP Reduction',
				'description' => 'Percentile bonus towards reducing aps required to ready a weapon.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentReloadTimeAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Reload Time AP Reduction',
				'description' => 'Percentile bonus towards reducing required time to reload a weapon.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentAPReduction' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent AP Reduction',
				'description' => 'Percentile bonus towards reducing required aps to use weapon item. This affects the weapon\'s ShotsPer4Turns.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'DamageBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Damage Bonus',
				'description' => 'Damage bonus. Applies only to guns.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'MeleeDamageBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Melee Damage Bonus',
				'description' => 'Hand to hand damage bonus. For now just applies to brass knuckles and knives',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'VisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Vision Range Bonus',
				'description' => 'Bonus to visible range (General. Always applied)',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'NightVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Night Vision Range Bonus',
				'description' => 'Bonus to visible range at night',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'DayVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Day Vision Range Bonus',
				'description' => 'Bonus to visible range during the day',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'CaveVisionRangeBonus' => new Jelly_Field_Float([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Cave Vision Range Bonus',
				'description' => 'Bonus to visible range underground',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'BrightLightVisionRangeBonus' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Bright Light Vision Range Bonus',
				'description' => 'Bonus to visible range in bright light (more than regular Daylight intensity). Cumulative with DayVisionRangeBonus.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentTunnelVision' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Tunnel Vision',
				'description' => '(0-100) % of peripheral vision lost. Higher = narrower line of sight.',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'ScopeMagFactor' => new Jelly_Field_Float([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'label' => 'Scope Mag Factor',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'ProjectionFactor' => new Jelly_Field_Float([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'label' => 'Projection Factor',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'RecoilModifierX' => new Jelly_Field_Float([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Recoil Modifier X',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'RecoilModifierY' => new Jelly_Field_Float([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Recoil Modifier Y',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentRecoilModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Recoil Modifier',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'PercentAccuracyModifier' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_INCREMENT,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_INCREMENT,
				'label' => 'Percent Accuracy Modifier',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),
			'AttachmentSystem' => new Jelly_Field_Integer([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_OVERWRITE,
				'label' => 'Attachment System',
				'allow_null' => true,
				'default' => NULL,
				'convert_empty' => true,
			]),

			'STAND_MODIFIERS' => new Jelly_Field_Text([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_STANCE,
			]),
			'CROUCH_MODIFIERS' => new Jelly_Field_Text([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_STANCE,
			]),
			'PRONE_MODIFIERS' => new Jelly_Field_Text([
				Core_Item::PARAM_SAVE => Core_Item::FIELD_OVERWRITE,
				Core_Item::PARAM_MERGE => Core_Item::FIELD_STANCE,
			]),
		));
	}

} // End Model_Attachment_Data