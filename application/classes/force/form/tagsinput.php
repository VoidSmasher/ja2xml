<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_TagsInput
 * User: legion
 * Date: 08.12.19
 * Time: 8:49
 */
class Force_Form_TagsInput extends Force_Form_Input {

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	public function render() {



		Helper_Assets::add_styles('assets/tagsinput/bootstrap-tagsinput.css');

		Helper_Assets::add_scripts('assets/typeahead/typeahead.bundle.js');
		Helper_Assets::add_scripts('assets/tagsinput/bootstrap-tagsinput.min.js');

		Helper_Assets::add_scripts('assets/common/js/form.tagsinout.js');

		return parent::render();
	}

} // End Force_Form_TagsInput
