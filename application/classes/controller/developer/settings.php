<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Settings
 * User: legion
 * Date: 15.05.14
 * Time: 10:41
 */
class Controller_Developer_Settings extends Controller_Developer_Template {

	public function action_index() {
		function get_control($name, $label, $description = '') {
			$control = Force_Form_Input::factory($name, $label, Force_Config::instance()->get_param($name));
			if (!empty($description)) {
				$control->description($description);
			}
			return $control;
		}

		$config = Force_Config::instance();

		$form = Force_Form::factory()->preset_for_admin(false, false);

		$form->control = Force_Form_Section::factory(__('settings.counter_yandex'), array(
			get_control('yandex_code', __('settings.counter_yandex.code'), __('settings.counter_yandex.code.description')),
			get_control('yandex_id', __('settings.counter_yandex.id'), __('settings.counter_yandex.id.description')),
			Force_Form_Show_Image::factory()->value('/assets/admin/images/counter-yandex-metrika.png'),
		));

		$form->control = Force_Form_Section::factory(__('settings.counter_google'), array(
			get_control('google_code', __('settings.counter_google.code'), __('settings.counter_google.code.description')),
			get_control('google_id', __('settings.counter_google.id'), __('settings.counter_google.id.description')),
			Force_Form_Show_Image::factory()->value('/assets/admin/images/counter-google-analytics.png'),
		));

		if ($form->is_post()) {
			$config
				->set_params($form->get_values())
				->save();

			$form->redirect();
		}

		$this->template->data_spy_target = '.docs-sidebar';
		$this->template->content[] = $form->render();
	}

} // End Controller_Developer_Settings