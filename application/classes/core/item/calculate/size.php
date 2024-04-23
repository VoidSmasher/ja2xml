<?php
/**
 * User: legion
 * Date: 14.03.2020
 * Time: 19:55
 */
trait Core_Item_Calculate_Size {

	protected static $sizes = array(
		0 => 186,
		1 => 239,
		2 => 351,
		3 => 401,
		4 => 551,
		5 => 605,
		6 => 826,
		7 => 1001,
		8 => 1199,
	);

	public static function calculate_size(Model_Weapon_Group $model) {
		if (empty($model->length_max)) {
			return $model->ItemSize;
		}

		$size = 9;

		foreach (self::$sizes as $_size => $length_max) {
			if ($model->length_max < $length_max) {
				$size = $_size;
				break;
			}
		}

		$size_bonus = $model->stock_ItemSizeBonus;

		if (!empty($size_bonus)) {
			$size = $size + $size_bonus;
		}

		if ($size < 0) {
			$size = 0;
		}

		if ($size == 0) {
			$size = NULL;
		}

		return $size;
	}

}