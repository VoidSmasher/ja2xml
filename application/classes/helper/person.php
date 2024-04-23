<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Person
 * User: legion
 * Date: 14.03.14
 * Time: 6:55
 */
class Helper_Person {

	const GENDER_MALE = 'male';
	const GENDER_FEMALE = 'female';
	const GENDER_UNISEX = 'unisex';

	/*
	 * GENDER
	 * Пол необходимо указывать в виде ENUM('male','female')
	 * Если по умолчанию пол неизвестен, то поле ставить в NULL
	 */

	public static function get_gender_options($include_unknown_gender = false) {
		$gender = array(
			self::GENDER_MALE => __('gender.' . self::GENDER_MALE),
			self::GENDER_FEMALE => __('gender.' . self::GENDER_FEMALE),
		);
		if ($include_unknown_gender) {
			$gender[self::GENDER_UNISEX] = __('gender.' . self::GENDER_UNISEX);
		}
		return $gender;
	}

	public static function get_gender_label($gender) {
		$labels = array(
			self::GENDER_MALE => Helper_Bootstrap::LABEL_BLUE,
			self::GENDER_FEMALE => Helper_Bootstrap::LABEL_RED,
		);
		return (array_key_exists($gender, $labels)) ? $labels[$gender] : Helper_Bootstrap::LABEL_DEFAULT;
	}

	public static function get_gender_value($gender) {
		if (in_array($gender, array(
			self::GENDER_MALE,
			self::GENDER_FEMALE,
		))) {
			return $gender;
		} else {
			return null;
		}
	}

	public static function humanize_gender($gender) {
		if (in_array($gender, array(
			self::GENDER_MALE,
			self::GENDER_FEMALE,
		))) {
			return __('gender.' . $gender);
		} else {
			return __('gender.' . self::GENDER_UNISEX);
		}
	}

	public static function humanize_gender_short($gender) {
		if (in_array($gender, array(
			self::GENDER_MALE,
			self::GENDER_FEMALE,
		))) {
			return __('gender.short.' . $gender);
		} else {
			return __('gender.short.' . self::GENDER_UNISEX);
		}
	}

} // End Helper_Person
 