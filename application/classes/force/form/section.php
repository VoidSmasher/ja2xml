<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Section
 * User: legion
 * Date: 24.07.14
 * Time: 11:40
 */
class Force_Form_Section extends Force_Form_Container {

	public function __construct($label = null, array $controls = array()) {
		if (is_null($label)) {
			$label = __('form.section.default');
		}
		$this->label($label);
		$this->name(Helper_String::translate_to_alias($label));
		$this->add_controls($controls);

		$this->attribute('class', 'fc-section');
	}

	public static function factory($label = null, array $controls = array()) {
		return new self($label, $controls);
	}

	public function render($container_body = null) {
		$container_body = (string)$container_body;

		return View::factory(FORCE_VIEW . 'form/containers/section')
			->set('attributes', $this->get_attributes())
			->set('label', $this->get_label())
			->set('show_label', $this->is_show_label())
			->bind('container_body', $container_body)
			->render();
	}

	public function preset_for_admin() {
		$section_id = $this->get_name();
		if (!empty($section_id)) {
			$this->attribute('id', $section_id, false);
		}
		return $this;
	}

} // End Force_Form_Section
