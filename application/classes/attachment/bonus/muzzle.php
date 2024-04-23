<?php
/**
 * User: legion
 * Date: 04.01.2020
 * Time: 23:14
 */
trait Attachment_Bonus_Muzzle {

	public static function calculate_muzzle_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_SUPPRESSOR,
			Attachment::TYPE_MUZZLE,
			Attachment::TYPE_CHOKE,
		])) {
			return false;
		}

		/*
		 * Встроенные глушители уже вписаны в оружие
		 */
		if ($model->is_integrated || $model->uiIndex < 0) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		$ubWeight = Core_Attachment_Data::get_weight($model);
		$ubWeight = Helper::round_to_five($ubWeight);

		$item = JA2_Item::factory()
			->set(JA2_Item::ItemSizeBonus, NULL);

		if (Attachment::has_type($model, Attachment::TYPE_SUPPRESSOR)) {
			$item->set(JA2_Item::ToHitBonus, NULL);
		}

		if (array_key_exists(Attachment::BONUS_MUZZLE_LONG, $bonuses)) {
			$item->set(JA2_Item::ItemSizeBonus, 1);
		}

		if (array_key_exists(Attachment::BONUS_MUZZLE_WEIGHT, $bonuses)) {
			$item->set(JA2_Item::PercentReadyTimeAPReduction, -$ubWeight);

			$item->STAND_MODIFIERS()
				->PercentHandling($ubWeight)
				->PercentMaxCounterForce($ubWeight)
				->PercentCounterForceAccuracy($ubWeight);
		}

		if (array_key_exists(Attachment::BONUS_MUZZLE_BARREL, $bonuses)) {
			$item->set(JA2_Item::PercentRecoilModifier, NULL);

			$item->modify(JA2_Item::PercentAccuracyModifier, 10);
			$item->modify(JA2_Item::RangeBonus, 100);
			$item->STAND_MODIFIERS()
				->PercentMaxCounterForce(-15)
				->PercentCounterForceAccuracy(-15);
		}

		$item->apply_data($model);

		return true;
	}

}