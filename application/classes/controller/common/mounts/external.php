<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Mounts_External
 * User: legion
 * Date: 01.07.18
 * Time: 4:06
 */
trait Controller_Common_Mounts_External {

	/**
	 * @param Jelly_Model $model
	 * @param string $caption
	 * @return Force_Button
	 */
	protected static function get_button_mounts_external(Jelly_Model $model, $caption = '+') {
		$uiIndex = $model->uiIndex;

		$button = Force_Button::factory($caption)
			->modal('mounts_external_modal')
			->attribute('data-id', $uiIndex);

		$button
			->link('#')
			->btn_sm();

		return $button;
	}

	public function action_mounts_external() {
		$uiIndex = $this->request->param('id');

		$model = Core_Attachment_Data::get_attachments_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!$model->loaded()) {
			echo 'Failed to load model';
			exit;
		}

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'mounts_external'),
			Force_Form_Hidden::factory('id', $model->uiIndex),
		])->button_submit()
			->no_cache()
			->button(Force_Button::factory('Отмена')->modal_close());

		$bonuses = Attachment::instance()->get_mount_list();
		$bonus_values = Attachment::get_external_mounts($model);

		$form->control = Helper::form_checkboxes($bonuses, $bonus_values);
		$form->control = Force_Form_HTML::factory()->attribute('class', 'clearfix');

		echo $form->render();
		exit;
	}

	protected function _mounts_external() {
		$this->_mounts_external_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/modal_ajax_html.js');

		$action = Force_URL::current()
			->clean_query()
			->action('mounts_external')
			->get_url();

		$this->template->modal[] = Force_Modal::factory('mounts_external_modal')
			->attribute('data-action', $action)
			->label('External Mounts')
			->modal_lg()
			->hide_buttons()
			->render();
	}

	/**
	 * @return bool
	 * @throws HTTP_Exception_404
	 * @throws Kohana_Exception
	 */
	protected function _mounts_external_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'mounts_external') {
			return false;
		}

		$uiIndex = $this->request->post('id');

		$model = Core_Attachment_Data::factory()->get_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!($model instanceof Jelly_Model)) {
			throw new HTTP_Exception_404();
		}

		if (!$model->loaded()) {
			throw new HTTP_Exception_404();
		}

		$attachment_mounts = Attachment::instance()->get_mount_list();
		$attachment_mount_values = array();
		foreach ($attachment_mounts as $attachment_mount) {
			$value = $this->request->post($attachment_mount);
			if (!is_null($value)) {
				$attachment_mount_values[] = $attachment_mount;
			}
		}
		if (!empty($attachment_mount_values)) {
			$model->attachment_mounts_external = json_encode($attachment_mount_values);
		} else {
			$model->attachment_mounts_external = NULL;
		}

		try {
			$model->save();
		} catch (Jelly_Validation_Exception $e) {
			Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Common_Mounts_External