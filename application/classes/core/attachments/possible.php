<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Attachments_Possible
 * User: legion
 * Date: 19.07.2020
 * Time: 7:58
 */
trait Core_Attachments_Possible {

	/**
	 * @param Jelly_Model $model
	 * @return array
	 */
	public static function get_possible_attachments(Jelly_Model $model) {
		$possible_attachments = json_decode($model->possible_attachments, true);

		if (!is_array($possible_attachments)) {
			$possible_attachments = array();
		}

		return $possible_attachments;
	}

	public static function set_possible_attachments(Jelly_Model $model, array $possible_attachments) {
		foreach ($possible_attachments as $attach_index => $ap_cost) {
			if (!is_numeric($attach_index)) {
				unset($possible_attachments[$attach_index]);
			}
		}

		ksort($possible_attachments);

		$model->possible_attachments = json_encode($possible_attachments);
	}

	public static function set_possible_attachment(Jelly_Model $model, $attachment_index, $ap_cost = Core_Attachment_Data::DEFAULT_AP_COST) {
		$possible_attachments = self::get_possible_attachments($model);

		$possible_attachments[$attachment_index] = $ap_cost;

		self::set_possible_attachments($model, $possible_attachments);
	}

	public static function remove_possible_attachment(Jelly_Model $model, $attachment_index) {
		$possible_attachments = self::get_possible_attachments($model);

		if (array_key_exists($attachment_index, $possible_attachments)) {
			unset($possible_attachments[$attachment_index]);
		}

		foreach ($possible_attachments as $attach_index => $ap_cost) {
			if (!is_numeric($attach_index)) {
				unset($possible_attachments[$attach_index]);
			}
		}

		if (empty($possible_attachments)) {
			$model->possible_attachments = NULL;
		} else {
			$model->possible_attachments = json_encode($possible_attachments);
		}
	}

	public static function popover_possible_attachments(array $attachments, Jelly_Model $model, Force_List_Row $row, $column = 'integrated_attachments') {
		if (empty($model->popover_possible_attachments)) {
			$possible_attachments = Core_Weapon_Data::get_possible_attachments($model);
			$possible_attachments_array = array();
			foreach ($possible_attachments as $attach_index => $attach_ap_cost) {
				$possible_attachments_array[$attach_index] = $attach_index . ': ' . Arr::get($attachments, $attach_index, 'Unknown');
			}
			$model->popover_possible_attachments = Helper_String::to_string($possible_attachments_array, '<br/>');
		}
		$row->cell($column)->popover('Possible Attachments', $model->popover_possible_attachments, 'top');
	}

} // End Core_Attachments_Possible
