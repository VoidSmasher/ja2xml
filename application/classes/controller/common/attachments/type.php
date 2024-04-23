<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Common_Attachments_Type
 * User: legion
 * Date: 08.05.21
 * Time: 16:50
 */
trait Controller_Common_Attachments_Type {

	/**
	 * @param Jelly_Model $model
	 * @param string $caption
	 * @return Force_Button
	 */
	protected static function get_button_attachment_type(Jelly_Model $model, $caption = '+') {
		$uiIndex = $model->uiIndex;

		$button = Force_Button::factory($caption)
			->modal('attachment_type_modal')
			->attribute('data-id', $uiIndex);

		$button
			->link('#')
			->btn_sm();

		return $button;
	}

	public function action_attachment_type() {
		$uiIndex = $this->request->param('id');

		/** @var Model_Attachment_Data $model */
		$model = Core_Attachment_Data::get_attachments_builder()
			->where('uiIndex', '=', $uiIndex)
			->limit(1)
			->select();

		if (!$model->loaded()) {
			echo 'Failed to load model';
			exit;
		}

		$form = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'attachment_type'),
			Force_Form_Hidden::factory('id', $model->uiIndex),
		])->button_submit()
			->no_cache()
			->button(Force_Button::factory('Отмена')->modal_close());

		$types = Attachment::instance()->get_type_list();
		$type_values = Attachment::get_types($model);

		$form->control = Helper::form_checkboxes($types, $type_values);
		$form->control = Force_Form_HTML::factory()->attribute('class', 'clearfix');

		echo $form->render();
		exit;
	}

	protected function _attachment_type() {
		$this->_attachment_type_apply();
		Helper_Assets::add_scripts('/assets/ja2/js/modal_ajax_html.js');

		$action = Force_URL::current()
			->clean_query()
			->action('attachment_type')
			->get_url();

		$this->template->modal[] = Force_Modal::factory('attachment_type_modal')
			->attribute('data-action', $action)
			->label('Attachment Types')
			->modal_lg()
			->hide_buttons()
			->render();
	}

	/**
	 * @return bool
	 * @throws HTTP_Exception_404
	 * @throws Kohana_Exception
	 */
	protected function _attachment_type_apply() {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'attachment_type') {
			return false;
		}

		$uiIndex = $this->request->post('id');

		/** @var Model_Attachment_Data $model */
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

		$types = Attachment::instance()->get_type_list();
		$type_values = array();
		foreach ($types as $type) {
			$value = $this->request->post($type);
			if (!is_null($value)) {
				$type_values[] = $type;
			}
		}
		if (!empty($type_values)) {
			$model->attachment_types = json_encode($type_values);
		} else {
			$model->attachment_types = NULL;
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

} // End Controller_Common_Attachments_Type