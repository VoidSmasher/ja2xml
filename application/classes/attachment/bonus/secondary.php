<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Attachment_Bonus_Secondary
 * User: legion
 * Date: 04.01.2020
 * Time: 23:12
 */
trait Attachment_Bonus_Secondary {

	public static function calculate_secondary_weapon_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_UNDER_BARREL_WEAPON,
		])) {
			return false;
		}

		if ($model->is_fixed) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		$ubWeight = Core_Attachment_Data::get_weight($model);
		$ubWeight = Helper::round_to_five($ubWeight);

		$ubw = JA2_Item::factory();

		$ubw->modify(JA2_Item::PercentReadyTimeAPReduction, -$ubWeight);
		$ubw->set(JA2_Item::PercentRecoilModifier, NULL);
		$ubw->set(JA2_Item::RecoilModifierX, NULL);
		$ubw->set(JA2_Item::RecoilModifierY, NULL);

		$ubw->STAND_MODIFIERS()
			->PercentHandling($ubWeight)
			->PercentMaxCounterForce($ubWeight)
			->PercentCounterForceAccuracy($ubWeight);

		if (array_key_exists(Attachment::BONUS_UBW_SMALL_FOREGRIP, $bonuses)) {
			$foregrip = Attachment::get_foregrip();

			$ubw->merge_item($foregrip);
		}

		$ubw->apply_data($model);

		return true;
	}

} // End Attachment_Bonus_Secondary
