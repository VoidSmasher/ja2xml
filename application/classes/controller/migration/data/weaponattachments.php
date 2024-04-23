<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Data_WeaponAttachments
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Data_WeaponAttachments extends Controller_Migration_Template {

	protected $migration_title = 'Создание набора Attachments Data';
	protected $migration_description = [];

	public function get_count() {
		return Core_Weapon_Data::factory()->get_builder()
			->where('possible_attachments', 'IS', null)
			->count();
	}

	public function action_index() {
		parent::action_index();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$weapon = Core_Weapon_Data::factory()->get_builder()
			->where('possible_attachments', 'IS', null)
			->limit(1)
			->select();

		if (!$weapon->loaded()) {
			$this->migration->send_error('Weapon not loaded');
		}

		$attachments = Core_Attachment_Mod::factory()->get_builder()
			->where('itemIndex', '=', $weapon->uiIndex)
			->select_all();

		$changes = 0;

		$possible_attachments = array();

		foreach ($attachments as $attachment) {
			$possible_attachments[(int)$attachment->attachmentIndex] = (int)$attachment->APCost;
		}
//		$possible_attachments = $attachments->as_array(NULL, 'attachmentIndex');

		$weapon->possible_attachments = json_encode($possible_attachments);

		try {
			$weapon->save();
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		if ($weapon->saved()) {
			$changes++;
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Data_WeaponAttachments