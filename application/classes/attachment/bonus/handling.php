<?php
/**
 * User: legion
 * Date: 04.01.2020
 * Time: 23:07
 */
trait Attachment_Bonus_Handling {

	/**
	 * @return JA2_Item
	 */
	public static function get_foregrip() {
		$foregrip = JA2_Item::factory()
			->modify(JA2_Item::BurstToHitBonus, 3)
			->modify(JA2_Item::AutoFireToHitBonus, 3)
			->set(JA2_Item::PercentReadyTimeAPReduction, 0);

		$foregrip->STAND_MODIFIERS()
			->PercentHandling(0)
			->PercentMaxCounterForce(25)
			->PercentCounterForceAccuracy(10);

		$foregrip->PRONE_MODIFIERS()
			->PercentHandling(0)
			->PercentMaxCounterForce(0)
			->PercentCounterForceAccuracy(0);

		return $foregrip;
	}

	public static function calculate_handling_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_FOREGRIP,
			Attachment::TYPE_BIPOD,
		])) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		/*
		 * FOREGRIP
		 */
		$foregrip = Attachment::get_foregrip();

		/*
		 * FOREGRIP BONUSES
		 * Все бонусы рассчитаны так, чтобы давать 20 очков.
		 * 3 бонуса в сумме с базовыми показателями дают 110 очков.
		 * Положительный Handling является отрицательным показателем.
		 */

		if (array_key_exists(Attachment::BONUS_FOREGRIP_HANDLING, $bonuses)) {
			$foregrip
				->modify(JA2_Item::BurstToHitBonus, 1)
				->modify(JA2_Item::AutoFireToHitBonus, 1)
				->modify(JA2_Item::PercentReadyTimeAPReduction, 10);

			$foregrip->STAND_MODIFIERS()
				->PercentHandling(-10)
				->PercentMaxCounterForce(5)
				->PercentCounterForceAccuracy(5);
		}

		if (array_key_exists(Attachment::BONUS_FOREGRIP_STABILITY, $bonuses)) {
			$foregrip
				->modify(JA2_Item::BurstToHitBonus, 1)
				->modify(JA2_Item::AutoFireToHitBonus, 1)
				->modify(JA2_Item::PercentReadyTimeAPReduction, 5);

			$foregrip->STAND_MODIFIERS()
				->PercentHandling(-5)
				->PercentMaxCounterForce(10)
				->PercentCounterForceAccuracy(5);
		}

		if (array_key_exists(Attachment::BONUS_FOREGRIP_ACCURACY, $bonuses)) {
			$foregrip
				->modify(JA2_Item::BurstToHitBonus, 2)
				->modify(JA2_Item::AutoFireToHitBonus, 2);

			$foregrip->STAND_MODIFIERS()
				->PercentCounterForceAccuracy(10);
		}

		if (array_key_exists(Attachment::BONUS_FOREGRIP_COUNTER_FORCE, $bonuses)) {
			$foregrip->STAND_MODIFIERS()
				->PercentMaxCounterForce(15);
		}

		if (array_key_exists(Attachment::BONUS_FOREGRIP_POSITIVE_ANGLED, $bonuses)) {
			/*
			 * Рукоять наклонена назад
			 */
			$foregrip
				->modify(JA2_Item::BurstToHitBonus, -2)
				->modify(JA2_Item::AutoFireToHitBonus, -2)
				->modify(JA2_Item::PercentReadyTimeAPReduction, -5);

			$foregrip->STAND_MODIFIERS()
				->PercentHandling(5)
				->PercentCounterForceAccuracy(10);
		} elseif (array_key_exists(Attachment::BONUS_FOREGRIP_NEGATIVE_ANGLED, $bonuses)) {
			/*
			 * Рукоять наклонена вперёд
			 */
			$foregrip
				->modify(JA2_Item::BurstToHitBonus, -2)
				->modify(JA2_Item::AutoFireToHitBonus, -2)
				->modify(JA2_Item::PercentReadyTimeAPReduction, -5);

			$foregrip->STAND_MODIFIERS()
				->PercentHandling(5)
				->PercentMaxCounterForce(15)
				->PercentCounterForceAccuracy(-5);
		}

		if (array_key_exists(Attachment::BONUS_FOREGRIP_MAG, $bonuses)) {
			$foregrip
				->modify(JA2_Item::PercentReadyTimeAPReduction, -5);

			$foregrip->STAND_MODIFIERS()
				->PercentHandling(5);
		}

		/*
		 * BIPOD
		 * Всего распределено 190 очков.
		 */
		$bipod = JA2_Item::factory()
			->set(JA2_Item::Bipod, 10);

		$bipod->STAND_MODIFIERS()
			->PercentHandling(20);

		$bipod->PRONE_MODIFIERS()
			->PercentHandling(-75)
			->PercentMaxCounterForce(100)
			->PercentCounterForceAccuracy(35);

		/*
		 * Лёгкие сошки оказывают меньшее влияние на вертикальные положения.
		 */
		if (array_key_exists(Attachment::BONUS_BIPOD_LIGHT, $bonuses)) {
			$bipod->STAND_MODIFIERS()
				->PercentHandling(-10);
			$bipod->PRONE_MODIFIERS()
				->PercentMaxCounterForce(-10);
		}

		/*
		 * Длинные сошки открывают больше пространства для контроля оружия, но снижают
		 * эффективность гашения отдачи.
		 */
		if (array_key_exists(Attachment::BONUS_BIPOD_LONG, $bonuses)) {
			$bipod->PRONE_MODIFIERS()
				->PercentHandling(-10)
				->PercentMaxCounterForce(-10);
		}

		/*
		 * GRIPPOD
		 * Рукоять удлиннена и заканчивается упором
		 * Как и все бонусы для рукояти, добавляет 20 очков в STAND_MODIFIERS
		 * Теряет 70 очков в PRONE_MODIFIERS
		 */
		if (array_key_exists(Attachment::BONUS_FOREGRIP_POD, $bonuses)) {
			$bipod->set(JA2_Item::Bipod, 7);

			$foregrip
				->modify(JA2_Item::PercentReadyTimeAPReduction, -5);

			$bipod->STAND_MODIFIERS()
				->PercentHandling(-15)
				->PercentMaxCounterForce(5);

			$bipod->PRONE_MODIFIERS()
				->PercentHandling(+40)
				->PercentMaxCounterForce(-30);

			$foregrip->merge_item($bipod);
		}

		if (Attachment::has_type($model, Attachment::TYPE_FOREGRIP)) {
			$foregrip->apply_data($model);
		} elseif (Attachment::has_type($model, Attachment::TYPE_BIPOD)) {
			$bipod->apply_data($model);
		}

		return true;
	}

}