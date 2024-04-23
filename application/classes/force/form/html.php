<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_HTML
 * User: legion
 * Date: 25.09.14
 * Time: 17:21
 */
class Force_Form_HTML extends Force_Form_Control {

	protected $_allow_combine = false;
	protected $_html = null;

	public function __construct($html = null) {
		if ($html instanceof Force_List) {
			$html = $html->render();
		}
		if ($html instanceof Force_Form) {
			$html = $html->render();
		}
		if ($html instanceof Force_Filter) {
			$html = $html->render();
		}
		if ($html instanceof Force_Button) {
			$html = $html->render();
		}
		$this->_html = (string)$html;
		$this->_value = null;
	}

	public static function factory($html = null) {
		return new self($html);
	}

	protected function _render_simple() {
		return $this->_html;
	}

	public function render() {
		return '<div' . $this->render_attributes() . '>' . $this->_html . '</div>';
	}

} // End Force_Form_HTML
