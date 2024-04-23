<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Settings
 * User: legion
 * Date: 15.05.14
 * Time: 10:41
 */
class Controller_Admin_Settings extends Controller_Admin_Template {

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

		$form->control = Force_Form_Section::factory(__('form.tab.common'), array(
			get_control('title', __('settings.title')),
			get_control('domain', __('settings.domain')),
		));

		$form->control = Force_Form_Section::factory(__('form.tab.contacts'), array(
			get_control('phone', __('contacts.phone')),
			get_control('address', __('contacts.address')),
			get_control('email', __('contacts.email')),
		));

		$socials = Force_Social::instance();
		$form->control = Force_Form_Section::factory(__('settings.social_networks'), array(
			Force_Form_Note::factory(__('common.note'), __('settings.social_networks.description'))
				->alert_info(),
			$socials->get_control('vk'),
			$socials->get_control('facebook'),
			$socials->get_control('instagram'),
			$socials->get_control('twitter'),
		));

		$form->control = Force_Form_Section::factory(__('common.copyright.label'), array(
			get_control('company', __('settings.company')),
			get_control('start_year', __('settings.start_year')),
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

} // End Controller_Admin_Settings