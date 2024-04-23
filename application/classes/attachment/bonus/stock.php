<?php
/**
 * User: legion
 * Date: 04.01.2020
 * Time: 23:10
 */
trait Attachment_Bonus_Stock {

	public static function calculate_stock_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_STOCK,
		])) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		$stock = JA2_Item::factory();
		$stock->modify(JA2_Item::PercentReadyTimeAPReduction, null);

		if (array_key_exists(Attachment::BONUS_STOCK_ADJUSTABLE, $bonuses)) {
			$stock->STAND_MODIFIERS()
				->PercentHandling(-10);
		}

		if (array_key_exists(Attachment::BONUS_STOCK_ADJUSTABLE, $bonuses)) {
			$stock->set(JA2_Item::ItemSizeBonus, -1);
		}

		if (array_key_exists(Attachment::BONUS_STOCK_SHOOT_MINIMIZED, $bonuses)) {
			$stock
				->set(JA2_Item::ItemSizeBonus, -2)
				->modify(JA2_Item::ToHitBonus, -5)
				->modify(JA2_Item::PercentReadyTimeAPReduction, 30);

			$stock->STAND_MODIFIERS()
				->PercentMaxCounterForce(-30)
				->PercentCounterForceAccuracy(-20)
				->AimLevels(1);

			if (array_key_exists(Attachment::BONUS_STOCK_SOLID, $bonuses)) {
				$stock->STAND_MODIFIERS()->PercentMaxCounterForce(+10);
			} elseif (array_key_exists(Attachment::BONUS_STOCK_SIMPLE, $bonuses)) {
				$stock->STAND_MODIFIERS()->PercentMaxCounterForce(-10);
			}
		}

		if (array_key_exists(Attachment::BONUS_STOCK_LONG, $bonuses)) {
			$stock->set(JA2_Item::ItemSizeBonus, -2);
		} elseif (array_key_exists(Attachment::BONUS_STOCK_SHORT, $bonuses)) {
			$stock->set(JA2_Item::ItemSizeBonus, -1);
		}

		$stock->apply_data($model);

		return true;
	}

}