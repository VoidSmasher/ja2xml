<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Markdown
 * User: ener
 * Date: 19.09.15
 * Time: 17:24
 */
class Force_Form_Markdown extends Force_Form_Textarea {

	protected $_view = 'markdown';

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
			'params' => [],
		];
	}

	public function render() {
		Helper_Assets::add_scripts([
			'assets/common/js/jquery.pagedown-bootstrap.combined.min.js',
			'assets/common/js/form.markdown.js',
		]);
		Helper_Assets::add_styles('assets/common/css/jquery.pagedown-bootstrap.css');

		Helper_Assets::js_vars_push_array('form_markdown', $this->get_js_params());

		return parent::render();
	}

	/*
	 * HTML
	 */

	public function transform_to_html($value = null, array $attributes = null) {
		if (is_null($value)) {
			$value = $this->get_value();
		}
		$value = Markdown::instance()->transform($value);
		$value = nl2br($value);
		$value = str_replace('</p><br />', '</p>', $value);
		return $value;
	}

} // End Force_Form_Markdown
