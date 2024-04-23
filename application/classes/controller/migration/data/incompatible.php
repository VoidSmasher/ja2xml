<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Data_Incompatible
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Data_Incompatible extends Controller_Migration_Template {

	protected $migration_title = 'Проверка и исправление IncompatibleAttachments';
	protected $migration_description = [];

	public function get_count() {
		return 1;
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$changes = 0;

		$collection = Core_Incompatible_Mod::factory()->get_list();

		$data = array();

		/*
		 * Массив связей
		 */
		foreach ($collection as $model) {
			$data[$model->itemIndex][$model->incompatibleattachmentIndex] = $model->incompatibleattachmentIndex;
		}

		/*
		 * Проверка на наличие пар
		 */
		foreach ($data as $item_index => $incompatible_attachments) {
			foreach ($incompatible_attachments as $attachment_index) {
				if (array_key_exists($attachment_index, $data) && array_key_exists($item_index, $data[$attachment_index])) {
					// ok
				} else {
					$model = Core_Incompatible_Mod::factory()->create();

					$model->itemIndex = $attachment_index;
					$model->incompatibleattachmentIndex = $item_index;

					try {
						$model->save();
					} catch (Exception $e) {
						Log::exception($e, __CLASS__, __FUNCTION__);
						$this->migration->send_error($e->getMessage());
					}

					if ($model->saved()) {
						$this->migration->message($attachment_index . ' => ' . $item_index);
						$changes++;
					}
				}
			}
		}

		if ($changes < 1) {
			$changes = 1;
			$this->migration->message_success('Таблица успешно прошла проверку');
		} else {
			$this->migration->message_success('Все несоответствия были выявлены и устранены');
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Data_Incompatible