<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Admin_Actions
 * User: legion
 * Date: 14.08.15
 * Time: 3:52
 */
trait Controller_Common_Admin_Actions {

	protected function _back_to_index($index_path = null, $object_id = null) {
		Force_URL::back_to_index($index_path, $object_id);
	}

	public function action_items_per_page() {
		$value = $this->request->param('id');
		Helper_Pagination::set_items_per_page($value);
		$this->_back_to_index();
	}

	public function action_json_filter_status() {
		$result = Force_Filter::save_visible_status();
		echo json_encode($result);
	}

	public function action_json_suggest_user() {
		$term = Arr::get($_GET, 'term');
		$limit = Arr::get($_GET, 'limit', 10);
		echo Core_User::factory()->get_list_for_suggest($term, $limit);
	}

	public function action_json_sort_fields() {
//		if (!isset($this->sort_table)) {
//			Log::error_class(__CLASS__, __FUNCTION__, "_sort_table is not defined");
//			API::factory()->send();
//			return false;
//		}

		$sort_list = Arr::get($_POST, 'sort_list', []);

		Log::error_class(__CLASS__, __FUNCTION__, var_export($sort_list, true));

		if (!empty($sort_list) && is_array($sort_list)) {
			foreach ($sort_list as $id => $sort_field) {
				try {
					DB::update($this->sort_table)
						->set(['sort_field' => $sort_field])
						->where('id', '=', $id)
						->execute();
				} catch (Exception $e) {
					Log::exception($e, __CLASS__, __FUNCTION__);
				}
			}
		}

		API::factory()->send();
	}

} // End Controller_Common_Admin_Actions