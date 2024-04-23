<?php
/**
 * User: legion
 * Date: 04.01.2020
 * Time: 23:13
 */
trait Attachment_Bonus_Knife {

	public static function calculate_knife_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_KNIFE,
		])) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		$ubWeight = Core_Attachment_Data::get_weight($model);
		$ubWeight = Helper::round_to_five($ubWeight);

		$item = JA2_Item::factory()
			->set(JA2_Item::ToHitBonus, NULL);

		$item->set(JA2_Item::PercentReadyTimeAPReduction, -$ubWeight);

		$item->STAND_MODIFIERS()
			->PercentHandling($ubWeight)
			->PercentMaxCounterForce($ubWeight)
			->PercentCounterForceAccuracy($ubWeight);

		$item->apply_data($model);

		return true;
	}

}