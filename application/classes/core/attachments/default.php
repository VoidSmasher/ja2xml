<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachments_Default
 * User: legion
 * Date: 19.07.2020
 * Time: 7:59
 */
trait Core_Attachments_Default {

	/**
	 * @param Jelly_Model $model
	 * @param bool $prepare_keys
	 * @return array
	 */
	public static function get_default_attachments(Jelly_Model $model) {
		$default_attachments = json_decode($model->default_attachments, true);

		if (!is_array($default_attachments)) {
			$default_attachments = array();
		}

		if (!empty($default_attachments)) {
			$default_attachments = array_flip($default_attachments);
			foreach ($default_attachments as $attach_index => $number) {
				$default_attachments[$attach_index] = $attach_index;
			}
		}

		return $default_attachments;
	}

	public static function set_default_attachment(Jelly_Model $model, $attachment_index) {
		$default_attachments = self::get_default_attachments($model);

		$default_attachments[$attachment_index] = $attachment_index;

		foreach ($default_attachments as $attach_index => $number) {
			if (!is_numeric($attach_index)) {
				unset($default_attachments[$attach_index]);
			}
		}

		$default_attachments = array_keys($default_attachments);

		sort($default_attachments);

		$model->default_attachments = json_encode($default_attachments);
	}

	public static function remove_default_attachment(Jelly_Model $model, $attachment_index) {
		$default_attachments = self::get_default_attachments($model);

		if (array_key_exists($attachment_index, $default_attachments)) {
			unset($default_attachments[$attachment_index]);
		}

		foreach ($default_attachments as $attach_index => $number) {
			if (!is_numeric($attach_index)) {
				unset($default_attachments[$attach_index]);
			}
		}

		if (empty($default_attachments)) {
			$model->default_attachments = NULL;
		} else {
			$default_attachments = array_keys($default_attachments);

			sort($default_attachments);

			$model->default_attachments = json_encode($default_attachments);
		}
	}

} // End Core_Attachments_Default
