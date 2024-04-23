<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Attachment_Data
 * User: legion
 * Date: 19.07.2020
 * Time: 19:28
 */
class Attachment_Data {

	public $item_index;
	public $item_name;
	public $attach_index;
	public $attach_name;
	public $ap_cost;

	public $ap_cost_original;
	public $ap_cost_mod;

	public $is_default = false;
	public $is_restore = false;

	public $has_danger = false;
	public $has_warning = false;
	public $has_success = false;

	public $is_fixed = false;
	public $is_original = false;
	public $is_mod = false;

	public function __construct($item_index, $attach_index) {
		$this->item_index = $item_index;
		$this->attach_index = $attach_index;
	}

	/**
	 * @param array $data
	 * @param $item_index
	 * @param $attach_index
	 * @param bool $attach_index_as_key
	 * @return Attachment_Data
	 */
	public static function load(array &$data, $item_index, $attach_index, $attach_index_as_key = false) {
		$attachment = null;
		if ($attach_index_as_key) {
			if (isset($data[$attach_index])) {
				$attachment = $data[$attach_index];
			}
		} else {
			if (isset($data[$item_index][$attach_index])) {
				$attachment = $data[$item_index][$attach_index];
			}
		}
		if ($attachment instanceof Attachment_Data) {
			return $attachment;
		}
		return Attachment_Data::factory($item_index, $attach_index);
	}

	/**
	 * @param $item_index
	 * @param $attach_index
	 * @return Attachment_Data
	 */
	public static function factory($item_index, $attach_index) {
		return new self($item_index, $attach_index);
	}

	public function __toString() {
		return $this->render();
	}

	public function warning() {
		$this->has_warning = true;
	}

	public function success() {
		$this->has_success = true;
	}

	public function danger() {
		$this->has_danger = true;
	}

	public function render() {
		if ($this->is_fixed) {
			return Force_Button::factory($this->attach_name)
				->btn_disabled()
				->btn_sm()
				->render();
		}

		if ($this->is_restore) {
			if ($this->is_default) {
				$button = $this->get_button_restore_default(
					$this->item_index,
					$this->item_name,
					$this->attach_index,
					$this->attach_name
				);
			} else {
				$button = $this->get_button_restore(
					$this->item_index,
					$this->item_name,
					$this->attach_index,
					$this->attach_name,
					$this->ap_cost
				);
			}
		} else {
			if ($this->is_default) {
				$button = $this->get_button_remove_default(
					$this->item_index,
					$this->item_name,
					$this->attach_index,
					$this->attach_name
				);
			} else {
				$button = $this->get_button_remove(
					$this->item_index,
					$this->item_name,
					$this->attach_index,
					$this->attach_name,
					$this->ap_cost
				);
			}
		}

		if ($this->has_warning) {
			$button->btn_warning();
		} elseif ($this->has_danger) {
			$button->btn_danger();
		} elseif ($this->has_success) {
			$button->btn_success();
		}

		return $button->render();
	}

	protected function get_button_restore($item_index, $item_name, $attach_index, $attach_name, $ap_cost) {
		return Force_Button::factory($attach_name)
			->btn_danger()
			->modal('restore_modal')
			->attribute('data-item_index', $item_index)
			->attribute('data-item_name', $item_name)
			->attribute('data-attach_index', $attach_index)
			->attribute('data-attach_name', $attach_name)
			->attribute('data-ap_cost', $ap_cost)
			->btn_sm()
			->link('#');
	}

	protected function get_button_remove($item_index, $item_name, $attach_index, $attach_name, $ap_cost) {
		return Force_Button::factory($attach_name)
			->modal('remove_modal')
			->attribute('data-item_index', $item_index)
			->attribute('data-item_name', $item_name)
			->attribute('data-attach_index', $attach_index)
			->attribute('data-attach_name', $attach_name)
			->attribute('data-ap_cost', $ap_cost)
			->btn_sm()
			->link('#');
	}

	protected function get_button_restore_default($item_index, $item_name, $attach_index, $attach_name) {
		return Force_Button::factory($attach_name)
			->btn_danger()
			->modal('restore_default_modal')
			->attribute('data-item_index', $item_index)
			->attribute('data-item_name', $item_name)
			->attribute('data-attach_index', $attach_index)
			->attribute('data-attach_name', $attach_name)
			->btn_sm()
			->link('#');
	}

	protected function get_button_remove_default($item_index, $item_name, $attach_index, $attach_name) {
		$button = Force_Button::factory($attach_name);
		$fixed_attachments = Core_Attachment_Data::get_fixed_attachments();

		if (array_key_exists($attach_index, $fixed_attachments)) {
			$button
				->btn_disabled();
		} else {
			$button
				->modal('remove_default_modal')
				->attribute('data-item_index', $item_index)
				->attribute('data-item_name', $item_name)
				->attribute('data-attach_index', $attach_index)
				->attribute('data-attach_name', $attach_name)
				->link('#');
		}

		$button->btn_sm();

		return $button;
	}

} // End Attachment_Data
