<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Data_Attachments
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Data_Attachments extends Controller_Migration_Template {

	protected $migration_title = 'Создание набора Attachments Data';
	protected $migration_description = [];

	public function get_count() {
		return 1;
	}

	public function action_index() {
		parent::action_index();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$attachments = Core_Item::factory()->get_builder()
			->where('nasAttachmentClass', 'IN', Core_Item::get_default_nas_attachment_classes())
			->select_all();

		$changes = 0;

		$fields = Core_Attachment_Data::factory()->get_builder()->meta()->fields();
		$fields = array_keys($fields);

		foreach ($attachments as $attachment) {
			if ($attachment->uiIndex < 1) {
				continue;
			}
			// ignoring replacable barrels
			if (($attachment->nasAttachmentClass == Core_Item::NAS_ATTACHMENT_CLASS_INTERNAL) && !empty($attachment->nasLayoutClass)) {
				continue;
			}

			$model = Core_Attachment_Data::factory()->get_builder()
				->where('uiIndex', '=', $attachment->uiIndex)
				->limit(1)
				->select();

			if (!$model->loaded()) {
				continue;
			}

			foreach ($fields as $field) {
//				switch ($field) {
//					case 'id':
//					case 'APCost':
//						continue;
//				}
				if ($field != 'Inseparable') {
					continue;
				}

				if (is_null($model->{$field})) {
					$model->{$field} = $attachment->{$field};
				}
			}

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
				$this->migration->send_error($e->getMessage());
			}

			if ($model->saved()) {
				$changes++;
			}
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Data_Attachments