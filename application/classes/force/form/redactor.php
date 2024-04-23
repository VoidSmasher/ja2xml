<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Redactor
 * User: ener
 * Date: 19.09.15
 * Time: 17:24
 */
class Force_Form_Redactor extends Force_Form_Textarea {

	protected $_view = 'redactor';

	public function __construct($name = null, $label = null, $value = null) {
		parent::__construct($name, $label, $value);
		$this->attribute('rows', 10);
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	public function get_js_params() {
		return [
			'name' => $this->get_name(),
			'params' => [
				'buttons' => array('format', 'bold', 'italic', 'deleted', 'lists', 'image', 'file', 'link', 'horizontalrule'),
				'plugins' => array('source'),
			],
		];
	}

	public function render() {
		Helper_Assets::add_scripts([
			'assets/imperavi/redactor/redactor.min.js',
			'assets/imperavi/redactor/plugins/source.js',
			'assets/common/js/form.redactor.js',
		]);
		Helper_Assets::add_styles('assets/imperavi/redactor/redactor.min.css');

		Helper_Assets::js_vars_push_array('form_redactor', $this->get_js_params());

		return parent::render();
	}

	/*
	 * HTML
	 */

	public function transform_to_html($value = null, array $attributes = null) {
		if (is_null($value)) {
			$value = $this->get_value();
		}
		return '<div class="imperavi-redactor">' . $value . '</div>';
	}

} // End Force_Form_Redactor
