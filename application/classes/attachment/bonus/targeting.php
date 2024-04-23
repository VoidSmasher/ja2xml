<?php

/**
 * User: legion
 * Date: 04.01.2020
 * Time: 23:08
 */
trait Attachment_Bonus_Targeting {

	public static function calculate_targeting_bonuses(Model_Attachment_Data $model) {
		if (!Attachment::has_type($model, [
			Attachment::TYPE_SCOPE,
			Attachment::TYPE_SIGHT,
			Attachment::TYPE_LASER,
			Attachment::TYPE_FLASHLIGHT,
		])) {
			return false;
		}

		$bonuses = Attachment::get_bonuses($model);

		$item = JA2_Item::factory();
		$item
			->set(JA2_Item::VisionRangeBonus, 0)
			->set(JA2_Item::DayVisionRangeBonus, 0)
			->set(JA2_Item::BrightLightVisionRangeBonus, 0)
			->set(JA2_Item::NightVisionRangeBonus, 0)
			->set(JA2_Item::CaveVisionRangeBonus, 0)
			->set(JA2_Item::PercentAPReduction, 0)
			->set(JA2_Item::RangeBonus, 0)
			->set(JA2_Item::DamageBonus, 0)
			->set(JA2_Item::ToHitBonus, 0)
			->set(JA2_Item::AimBonus, 0)
			->set(JA2_Item::MinRangeForAimBonus, 0)
//			->set(JA2_Item::FlashLightRange, 0)
			->set(JA2_Item::CamoBonus, 0)
			->set(JA2_Item::DesertCamoBonus, 0)
			->set(JA2_Item::SnowCamoBonus, 0)
			->set(JA2_Item::UrbanCamoBonus, 0)
			->set(JA2_Item::StealthBonus, 0)
			->set(JA2_Item::BurstToHitBonus, 0)
			->set(JA2_Item::AutoFireToHitBonus, 0);

		/*
		 * SCOPE
		 */
		$ScopeMagFactor = $model->ScopeMagFactor;
		$DayVisionRangeBonus = 0;
		$NightVisionRangeBonus = 0;
		$CaveVisionRangeBonus = 0;
		$PercentAPReduction = 0;
		$PercentTunnelVision = 0;
		$AimLevels = 0;
		$ToHitBonus = 0;
		$ToHitBonusAuto = 0;

		if ($ScopeMagFactor > 0) {
			/*
			 * TUNNEL VISION negative to FIELD OF VIEW (FOV)
			 */
			$PercentTunnelVision = $ScopeMagFactor * 10 - 10;

			if (array_key_exists(Attachment::BONUS_SCOPE_TIGHT_FOV, $bonuses)) {
				$PercentTunnelVision += 10;
			} elseif (array_key_exists(Attachment::BONUS_SCOPE_WIDE_FOV, $bonuses)) {
				$PercentTunnelVision -= 10;
			}

			if (array_key_exists(Attachment::BONUS_SCOPE_SEE_THROUGH, $bonuses)) {
				$PercentTunnelVision += 5;
			}

			if ($ScopeMagFactor > 1) {
				if ($PercentTunnelVision < 5) {
					$PercentTunnelVision = 5;
				}
			} else {
				if ($PercentTunnelVision < 0) {
					$PercentTunnelVision = 0;
				}
			}

			$item->set(JA2_Item::MinRangeForAimBonus, $PercentTunnelVision / 1.3);

			/*
			 * SCOPE SEMI, BURST and AUTO PENALTY
			 */
			$ToHitBonus = ($PercentTunnelVision + 10) / 10;
			if (array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_SMALL, $bonuses)
				|| array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_LARGE, $bonuses)
				|| array_key_exists(Attachment::BONUS_SCOPE_GRID_DOUGHNUT, $bonuses)
			) {
				$ToHitBonus /= 2;
			}

			if (array_key_exists(Attachment::BONUS_SCOPE_SEE_THROUGH, $bonuses)) {
				$ToHitBonus /= 2;
			}

			$ToHitBonusAuto = $ToHitBonus * 5;

			/*
			 * AIM BONUS
			 */
			if ($ScopeMagFactor > 1) {
				$AimBonus = pow($ScopeMagFactor, 0.8) * 2.4;

				if (array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_SMALL, $bonuses)
					|| array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_LARGE, $bonuses)
					|| array_key_exists(Attachment::BONUS_SCOPE_SEE_THROUGH, $bonuses)
				) {
					$AimBonus /= 2;
				}

				$AimBonus = round($AimBonus);

				$item->set(JA2_Item::AimBonus, $AimBonus);
			}

			/*
			 * AIM LEVELS
			 */
			if ($ScopeMagFactor > 1) {
				$bonus = floor($ScopeMagFactor / 5);

				$AimLevels += $bonus + 1;
			}

			/*
			 * DAY VISION
			 */
			$DayVisionRangeBonus += ($ScopeMagFactor - 1) * 10;

			$item->set(JA2_Item::PercentTunnelVision, $PercentTunnelVision);
		}

		/*
		 * SIGHT
		 */
		if (Attachment::has_type($model, Attachment::TYPE_SIGHT)) {
			if (array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_LARGE, $bonuses)) {
				$PercentAPReduction += 20;
			} elseif (array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_SMALL, $bonuses)) {
				$PercentAPReduction += 15;
			}

			if (array_key_exists(Attachment::BONUS_SIGHT_MATCH, $bonuses)) {
				$range_bonus = 20;
				$item->set(JA2_Item::RangeBonus, $range_bonus);
				$item->set(JA2_Item::ToHitBonus, $range_bonus / 2.8);
			}
		}

		/*
		 * SCOPES and SIGHTS
		 */
		if (Attachment::has_type($model, Attachment::TYPE_SCOPE) || Attachment::has_type($model, Attachment::TYPE_SIGHT)) {
			/*
			 * MOUNT POSITION
			 */
			if (array_key_exists(Attachment::BONUS_SCOPE_HIGH_PROFILE, $bonuses)) {
				$item->modify(JA2_Item::AimBonus, -1);
			}
			if (array_key_exists(Attachment::BONUS_SCOPE_LOW_PROFILE, $bonuses)) {
				$item->modify(JA2_Item::AimBonus, 1);
			}
			if (array_key_exists(Attachment::BONUS_SCOPE_OFFSET_AXIS, $bonuses)) {
				$item->modify(JA2_Item::AimBonus, -1);
			}
			if (array_key_exists(Attachment::BONUS_SCOPE_GRID_DOUGHNUT, $bonuses)) {
				$item->modify(JA2_Item::AimBonus, -1);
				$AimLevels -= 1;
				$PercentAPReduction += 10;
			}

			/*
			 * PERCENT AP REDUCTION
			 */
//			$PercentAPReduction -= floor($PercentTunnelVision / 40) * 5;
			$PercentAPReduction -= round($PercentTunnelVision / 10);

			if (array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_SMALL, $bonuses)
				|| array_key_exists(Attachment::BONUS_SIGHT_COLLIMATOR_LARGE, $bonuses)) {
				if ($ScopeMagFactor > 1) {
					$PercentAPReduction -= ($ScopeMagFactor - 1) * 2;
				}
				$AimLevels -= 1;

				$NightVisionRangeBonus += 5;
			} else {

//				if ($ScopeMagFactor > 1) {
//					$NightVisionRangeBonus += Helper::round_to_five($ScopeMagFactor);
//				}

				/*
				 * Scopes NIGHT vision
				 */
				if (array_key_exists(Attachment::BONUS_SCOPE_IR_VISION, $bonuses)
					|| array_key_exists(Attachment::BONUS_SIGHT_IR_VISION, $bonuses)) {
					$NightVisionRangeBonus += ($DayVisionRangeBonus / 2);
				} elseif (array_key_exists(Attachment::BONUS_SCOPE_NIGHT_VISION, $bonuses)
					|| array_key_exists(Attachment::BONUS_SIGHT_NIGHT_VISION, $bonuses)) {
					$NightVisionRangeBonus += ($DayVisionRangeBonus / 4);
				}

				if (array_key_exists(Attachment::BONUS_SCOPE_GRID_GLOW, $bonuses) && $ScopeMagFactor > 1) {
					$NightVisionRangeBonus += 5;
				}

				// Dot Glow = faster and easier targeting, easy to target at night
				if (array_key_exists(Attachment::BONUS_SCOPE_DOT_GLOW, $bonuses)
					|| array_key_exists(Attachment::BONUS_SIGHT_DOT_GLOW, $bonuses)) {
					$PercentAPReduction += 5;
					$NightVisionRangeBonus += 5;
					$ToHitBonusAuto /= 1.2;
				}

				if (array_key_exists(Attachment::BONUS_SCOPE_SEE_THROUGH, $bonuses)) {
					if ($AimLevels > 0) {
						$AimLevels -= 1;
					}
				}
			}

			/*
			 * CAVE vision
			 */
			if (array_key_exists(Attachment::BONUS_SCOPE_IR_VISION, $bonuses)
				|| array_key_exists(Attachment::BONUS_SIGHT_IR_VISION, $bonuses)) {
				$CaveVisionRangeBonus = $NightVisionRangeBonus;
			} elseif ($NightVisionRangeBonus > 5) {
				$CaveVisionRangeBonus = $NightVisionRangeBonus / 3;
			} else {
				$CaveVisionRangeBonus = 0;
			}

//			if (array_key_exists(Attachment::BONUS_SCOPE_ADJUSTABLE_WINDAGE, $bonuses)
//				&& array_key_exists(Attachment::BONUS_SCOPE_ADJUSTABLE_ELEVATION, $bonuses)
//			) {
//				$item->modify(JA2_Item::AimBonus, +3);
//				if (array_key_exists(Attachment::BONUS_SCOPE_FAST_TARGETING, $bonuses)) {
//					$PercentAPReduction -= 1;
//				} else {
//					$PercentAPReduction -= 3;
//				}
//			} else {
				if (array_key_exists(Attachment::BONUS_SCOPE_ADJUSTABLE_WINDAGE, $bonuses)) {
					$item->modify(JA2_Item::AimBonus, +1);
					if (array_key_exists(Attachment::BONUS_SCOPE_FAST_TARGETING, $bonuses)) {
						$PercentAPReduction -= 1;
					} else {
						$PercentAPReduction -= 2;
					}
				}
				if (array_key_exists(Attachment::BONUS_SCOPE_ADJUSTABLE_ELEVATION, $bonuses)) {
					$item->modify(JA2_Item::AimBonus, +1);
					if (array_key_exists(Attachment::BONUS_SCOPE_FAST_TARGETING, $bonuses)) {
						$PercentAPReduction -= 1;
					} else {
						$PercentAPReduction -= 2;
					}
				}
//			}
			if (array_key_exists(Attachment::BONUS_SCOPE_GRID_RANGEFINDER_ADVANCED, $bonuses)) {
				$item->modify(JA2_Item::AimBonus, +3);
				if (array_key_exists(Attachment::BONUS_SCOPE_FAST_TARGETING, $bonuses)) {
					$PercentAPReduction -= 2;
				} else {
					$PercentAPReduction -= 3;
				}
			} elseif (array_key_exists(Attachment::BONUS_SCOPE_GRID_RANGEFINDER_SIMPLE, $bonuses)) {
				$item->modify(JA2_Item::AimBonus, +1);
				$PercentAPReduction -= 1;
			}
		}

		$item->set(JA2_Item::DayVisionRangeBonus, $DayVisionRangeBonus);
		$item->set(JA2_Item::NightVisionRangeBonus, $NightVisionRangeBonus);
		$item->set(JA2_Item::CaveVisionRangeBonus, $CaveVisionRangeBonus);
		$item->set(JA2_Item::BrightLightVisionRangeBonus, $DayVisionRangeBonus);

		$camo_penalty = 0;

		/*
		 * LASER
		 */
		if (Attachment::has_type($model, Attachment::TYPE_LASER) &&
			Attachment::has_bonus($model, Attachment::BONUS_LASER_ON)) {
			$item->set(JA2_Item::ToHitBonus, 20);
			if (!Attachment::has_bonus($model, Attachment::BONUS_LASER_IR)) {
				$camo_penalty = $model->ProjectionFactor * 2;
				$camo_penalty = Helper::round_to_five($camo_penalty);
			}
		}

		/*
		 * FLASHLIGHT
		 */
		if (Attachment::has_type($model, Attachment::TYPE_FLASHLIGHT) &&
			Attachment::has_bonus($model, Attachment::BONUS_FLASHLIGHT_ON)) {
			if (Attachment::has_type($model, Attachment::TYPE_FLASHLIGHT_PROJECTOR)) {
				$item->set(JA2_Item::ToHitBonus, 25);
				$item->set_max(JA2_Item::PercentAPReduction, 20);
				$item->modify(JA2_Item::PercentTunnelVision, 20);
				$night_vision = $model->BestLaserRange / 10;
				$camo_penalty *= 2;
			} elseif (Attachment::has_bonus($model, Attachment::BONUS_FLASHLIGHT_IR)) {
				$camo_penalty = $night_vision = 0;
			} else {
				$camo_penalty = $night_vision = $model->FlashLightRange;
			}
			// Night vision bonus
			if ($night_vision > 0) {
				$item->set_max(JA2_Item::NightVisionRangeBonus, $night_vision);
				if (Attachment::has_type($model, Attachment::TYPE_FLASHLIGHT_PROJECTOR)) {
					$item->set_max(JA2_Item::CaveVisionRangeBonus, $night_vision + 5);
				} else {
					$item->set_max(JA2_Item::CaveVisionRangeBonus, $night_vision);
				}
			}
		}

		if ($ScopeMagFactor > 1) {
			$item->set(JA2_Item::ToHitBonus, -$ToHitBonus);
			$item->set(JA2_Item::BurstToHitBonus, -$ToHitBonusAuto / 2);
			$item->set(JA2_Item::AutoFireToHitBonus, -$ToHitBonusAuto);
		}

//		$item->set(JA2_Item::PercentAPReduction, $PercentAPReduction);
		$item->set(JA2_Item::PercentAPReduction, Helper::round_to_five($PercentAPReduction, false));

		if ($AimLevels > 0) {
			$item->set(JA2_Item::DamageBonus, $AimLevels - 1);
		}

		// Camo penalty
		if ($camo_penalty) {
			$item->modify(JA2_Item::CamoBonus, -$camo_penalty);
			$item->modify(JA2_Item::DesertCamoBonus, -$camo_penalty);
			$item->modify(JA2_Item::SnowCamoBonus, -$camo_penalty);
			$item->modify(JA2_Item::UrbanCamoBonus, -$camo_penalty);
			$item->modify(JA2_Item::StealthBonus, -$camo_penalty);
		}

		if ($AimLevels != 0) {
			$item->STAND_MODIFIERS()
				->AimLevels($AimLevels);
		}

		$item->apply_data($model);

		return true;
	}

}