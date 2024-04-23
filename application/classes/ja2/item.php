<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: JA2_Item
 * User: legion
 * Date: 07.11.19
 * Time: 18:18
 */
class JA2_Item extends JA2_Item_Stances {

	const ItemSizeBonus = 'ItemSizeBonus';
	const PercentNoiseReduction = 'PercentNoiseReduction';
	const HideMuzzleFlash = 'HideMuzzleFlash';
	const Bipod = 'Bipod';
	const RangeBonus = 'RangeBonus';
	const PercentRangeBonus = 'PercentRangeBonus';

	const ToHitBonus = 'ToHitBonus';
	const BestLaserRange = 'BestLaserRange';
	const FlashLightRange = 'FlashLightRange';
	const AimBonus = 'AimBonus';
	const MinRangeForAimBonus = 'MinRangeForAimBonus';
	const MagSizeBonus = 'MagSizeBonus';
	const BurstSizeBonus = 'BurstSizeBonus';
	const BurstToHitBonus = 'BurstToHitBonus';
	const AutoFireToHitBonus = 'AutoFireToHitBonus';
	const CamoBonus = 'CamoBonus';
	const UrbanCamoBonus = 'UrbanCamoBonus';
	const DesertCamoBonus = 'DesertCamoBonus';
	const SnowCamoBonus = 'SnowCamoBonus';
	const StealthBonus = 'StealthBonus';

	const PercentBurstFireAPReduction = 'PercentBurstFireAPReduction';
	const PercentAutofireAPReduction = 'PercentAutofireAPReduction';
	const PercentReadyTimeAPReduction = 'PercentReadyTimeAPReduction';
	const PercentReloadTimeAPReduction = 'PercentReloadTimeAPReduction';
	const PercentAPReduction = 'PercentAPReduction';

	const DamageBonus = 'DamageBonus';
	const MeleeDamageBonus = 'MeleeDamageBonus';

	const VisionRangeBonus = 'VisionRangeBonus';
	const BrightLightVisionRangeBonus = 'BrightLightVisionRangeBonus';
	const DayVisionRangeBonus = 'DayVisionRangeBonus';
	const NightVisionRangeBonus = 'NightVisionRangeBonus';
	const CaveVisionRangeBonus = 'CaveVisionRangeBonus';

	const PercentTunnelVision = 'PercentTunnelVision';
	const ScopeMagFactor = 'ScopeMagFactor';
	const ProjectionFactor = 'ProjectionFactor';

	const RecoilModifierX = 'RecoilModifierX';
	const RecoilModifierY = 'RecoilModifierY';
	const PercentRecoilModifier = 'PercentRecoilModifier';
	const PercentAccuracyModifier = 'PercentAccuracyModifier';

	const DefaultAttachment = 'DefaultAttachment';

	const STAND_MODIFIERS = 'STAND_MODIFIERS';
	const CROUCH_MODIFIERS = 'CROUCH_MODIFIERS';
	const PRONE_MODIFIERS = 'PRONE_MODIFIERS';

	protected $data = array();
	protected static $fields = array();

	public function __construct() {
		if (empty(self::$fields)) {
			self::$fields = Core_Item_Mod::factory()->create()->meta()->fields();
		}
	}

	/**
	 * @return JA2_Item
	 */
	public static function factory() {
		return new self();
	}

	public function get_data() {
		return $this->data;
	}

	public function get_value($field) {
		if (array_key_exists($field, $this->data)) {
			return $this->data[$field];
		} else {
			return NULL;
		}
	}

	protected function round_value($field, $value) {
		if (!array_key_exists($field, self::$fields)) {
			return $value;
		}
		$round = Core_Item::get_field_round_status(self::$fields[$field]);

		switch ($round) {
			case Core_Item::FIELD_ROUND_TO_FIVE:
				$value = Helper::round_to_five($value);
				break;
		}

		return $value;
	}

	/*
	 * STANCES
	 */

	public function STAND_MODIFIERS($json = null) {
		return $this->stance(Ja2_Item::STAND_MODIFIERS, $json);
	}

	public function CROUCH_MODIFIERS($json = null) {
		return $this->stance(Ja2_Item::CROUCH_MODIFIERS, $json);
	}

	public function PRONE_MODIFIERS($json = null) {
		return $this->stance(Ja2_Item::PRONE_MODIFIERS, $json);
	}

	/*
	 * FIELDS
	 */

	public function modify_by_percent($field, $percent) {
		switch ($field) {
			case Ja2_Item::STAND_MODIFIERS:
			case Ja2_Item::CROUCH_MODIFIERS:
			case Ja2_Item::PRONE_MODIFIERS:
			case JA2_Item::DefaultAttachment:
				break;
			default:
				if (array_key_exists($field, $this->data)) {
					$value = $this->data[$field];

					$percent = 100 + $percent;

					$value = $value * $percent / 100;

					$this->data[$field] = $this->round_value($field, $value);
				}
				break;
		}
		return $this;
	}

	public function modify($field, $value) {
		switch ($field) {
			case Ja2_Item::STAND_MODIFIERS:
			case Ja2_Item::CROUCH_MODIFIERS:
			case Ja2_Item::PRONE_MODIFIERS:
			case JA2_Item::DefaultAttachment:
				break;
			default:
				if (array_key_exists($field, $this->data)) {
					$current_value = $this->data[$field];
				} else {
					$current_value = 0;
				}
				$current_value += $value;
				$this->data[$field] = $this->round_value($field, $current_value);
				break;
		}
		return $this;
	}

	public function set($field, $value) {
		switch ($field) {
			case Ja2_Item::STAND_MODIFIERS:
			case Ja2_Item::CROUCH_MODIFIERS:
			case Ja2_Item::PRONE_MODIFIERS:
				$this->stance($field, $value);
				break;
			default:
				$this->data[$field] = $this->round_value($field, $value);
				break;
		}

		return $this;
	}

	public function set_max($field, $value) {
		if (array_key_exists($field, $this->data)) {
			if ($value > $this->data[$field]) {
				$this->set($field, $value);
			}
		} else {
			$this->set($field, $value);
		}

		return $this;
	}

	public function set_min($field, $value) {
		if (array_key_exists($field, $this->data)) {
			if ($value < $this->data[$field]) {
				$this->set($field, $value);
			}
		} else {
			$this->set($field, $value);
		}

		return $this;
	}

	/*
	 * MODEL
	 */

	public function load_data(Jelly_Model $model) {
		foreach ($model->meta()->fields() as $field_name => $field_data) {
			if (empty($model->{$field_name})) {
				continue;
			}

			$this->set($field_name, $model->{$field_name});
		}
	}

	public function apply_data(Jelly_Model $model) {
		foreach ($this->data as $field => $value) {
			if (empty($value)) {
				$value = NULL;
			}
			$model->{$field} = $value;
		}
		foreach ([
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
		] as $stance) {
			$model->{$stance} = $this->stance($stance)->render();
		}
	}

	/*
	 * MERGE WITH ANOTHER JA2_ITEM
	 */

	/*
	 * Используется для DefaultAttachment
	 */
	private function merge_json($field, $json) {
		$new_json = json_decode($json, true);
		if (!empty($new_json)) {
			$old_value = $this->get_value($field);
			$old_json = json_decode($old_value, true);
			if (!empty($old_json)) {
				$old_json = array_merge($old_json, $new_json);
				$this->set($field, json_encode($old_json));
			} else {
				$this->set($field, $json);
			}
		}
	}

	public function prepare_stances_to_merge() {
		foreach ([
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
		] as $stance) {
			$modifiers = $this->stance($stance);

			switch ($stance) {
				case Ja2_Item::CROUCH_MODIFIERS:
					$modifiers->update_MODIFIERS($this->stance(Ja2_Item::STAND_MODIFIERS), false);
					break;
				case Ja2_Item::PRONE_MODIFIERS:
					$modifiers->update_MODIFIERS($this->stance(Ja2_Item::CROUCH_MODIFIERS), false);
					break;
			}
		}
		return $this;
	}

	public function clean_stance_modifiers_from_duplicates() {
		foreach ([
			Ja2_Item::STAND_MODIFIERS,
			Ja2_Item::PRONE_MODIFIERS,
			Ja2_Item::CROUCH_MODIFIERS,
		] as $stance) {
			$modifiers = $this->stance($stance);

			switch ($stance) {
				case Ja2_Item::PRONE_MODIFIERS:
					$modifiers->clean_MODIFIERS($this->stance(Ja2_Item::CROUCH_MODIFIERS));
					break;
				case Ja2_Item::CROUCH_MODIFIERS:
					$modifiers->clean_MODIFIERS($this->stance(Ja2_Item::STAND_MODIFIERS));
					break;
			}
		}
		return $this;
	}

	public function merge_item(JA2_Item $item) {
		$model = Core_Item_Mod::factory()->create();

		$data = $item->get_data();
		foreach ($data as $field => $new_value) {
			$field_data = $model->meta()->field($field);
			if ($field_data instanceof Jelly_Field) {
				$status = Core_Item::get_field_merge_status($field_data);
				$old_value = $this->get_value($field);
				switch ($status) {
					case Core_Item::FIELD_OVERWRITE:
						$this->set($field, $new_value);
						break;
					case Core_Item::FIELD_OVERWRITE_NOT_EMPTY:
						if (!empty($new_value)) {
							$this->set($field, $new_value);
						}
						break;
					case Core_Item::FIELD_OVERWRITE_BOOLEAN:
						if ($new_value) {
							$this->set($field, $new_value ? 1 : NULL);
						}
						break;
					case Core_Item::FIELD_OVERWRITE_MIN:
						if (abs($new_value) < abs($old_value)) {
							$this->set($field, $new_value);
						}
						break;
					case Core_Item::FIELD_OVERWRITE_MAX:
						if (abs($new_value) > abs($old_value)) {
							$this->set($field, $new_value);
						}
						break;
					case Core_Item::FIELD_INCREMENT:
					case Core_Item::FIELD_SUMMARY_JSON:
						$this->modify($field, $new_value);
						break;
				}
			}
		}

		$this->prepare_stances_to_merge();
		$item->prepare_stances_to_merge();

		$item_stances = $item->get_stances();

		foreach ($item_stances as $stance => $item_modifiers) {
			$this->stance($stance)->update_MODIFIERS($item_modifiers, true);
		}

		$this->clean_stance_modifiers_from_duplicates();
		$item->clean_stance_modifiers_from_duplicates();

		return $this;
	}

} // End JA2_Item
