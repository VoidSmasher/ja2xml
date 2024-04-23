<?php
/**
 * User: legion
 * Date: 04.01.2020
 * Time: 23:16
 */
trait Attachment_Bonus_Mag {

	public static function calculate_mag_adapter_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_MAG_ADAPTER,
		])) {
			return false;
		}

		/*
		 * Встроенные адаптеры уже вписаны в оружие
		 */
		if ($model->is_integrated || $model->uiIndex < 0) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		$ubWeight = Core_Attachment_Data::get_weight($model);
		$ubWeight = Helper::round_to_five($ubWeight);

		$weight_bonus = $ubWeight * 2;

		$item = JA2_Item::factory()
			->set(JA2_Item::PercentReadyTimeAPReduction, -$weight_bonus)
			->set(JA2_Item::PercentReloadTimeAPReduction, -$weight_bonus * 3);

		$item->STAND_MODIFIERS()
			->PercentHandling($weight_bonus)
			->PercentMaxCounterForce($weight_bonus)
			->PercentCounterForceAccuracy($weight_bonus);

		$item->apply_data($model);

		return true;
	}

}