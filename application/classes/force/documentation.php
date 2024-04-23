<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Documentation
 * User: legion
 * Date: 22.08.14
 * Time: 18:06
 */
class Force_Documentation {

	use Force_Control_Menu;

	protected $_content = array();

	public function __construct() {
		$this->_menu = Force_Menu_Form::factory();
	}

	public static function factory() {
		return new self();
	}

	public function __toString() {
		return $this->render();
	}

	public function render() {
		$menu = '';

		if ($this->is_show_menu()) {
			$menu = $this->_menu->render();
		}

		return View::factory(FORCE_VIEW . 'documentation/default')
			->bind('menu', $menu)
			->bind('content', $this->_content)
			->render();
	}

	/*
	 * HEADING
	 */

	protected function _heading($number, $title, $slug, array $attributes = array()) {
		if (empty($slug)) {
			$slug = Helper_String::translate_to_alias($title);
			if ($number == 1) {
				$this->_menu->add_divider();
			}
			$item = $this->_menu->item($title)->link('#' . $slug);
			if ($number > 2) {
				$item->attribute('style', 'padding-left:20px;font-size:90%');
			}
			if ($number == 1) {
				$this->_menu->add_divider();
			}
		}
		$this->_content[] = '<div class="fc-section" id="' . $slug . '"><h' . $number . HTML::attributes($attributes) . '>' . $title . '</h' . $number . '></div>';
		return $this;
	}

	public function heading1($title, $slug = null) {
		return $this->_heading(1, $title, $slug, array(
			'class' => 'page-header',
		));
	}

	public function heading2($title, $slug = null) {
		return $this->_heading(2, $title, $slug);
	}

	public function heading3($title, $slug = null) {
		return $this->_heading(3, $title, $slug);
	}

	/*
	 * TEXT
	 */

	public function text($content) {
		$this->_content[] = '<p>' . Helper_String::to_string($content, '</p><p>') . '</p>';
		return $this;
	}

	/*
	 * CALL OUT
	 */

	protected function _callout($title, $content, $class) {
		$this->_content[] = '<div class="bs-callout bs-callout-' . $class . '">';
		$this->_content[] = '<h4>' . $title . '</h4>';
		$this->_content[] = Helper_String::to_string($content, '<br/>');
		$this->_content[] = '</div>';
		return $this;
	}

	public function callout_info($title, $content) {
		return $this->_callout($title, $content, 'info');
	}

	public function callout_warning($title, $content) {
		return $this->_callout($title, $content, 'warning');
	}

	public function callout_danger($title, $content) {
		return $this->_callout($title, $content, 'danger');
	}

	/*
	 * EXAMPLE
	 */

	public function example($code, $preview = null, $lang = null) {
		if (!empty($preview)) {
			$this->_content[] = '<div class="bs-example">' . Helper_String::to_string($preview, '<br/>') . '</div>';
		}
		if (is_array($code)) {
			$code = implode("\n", $code);
		}
//		if (empty($lang)) {
//			$lang = 'php';
//		}
		if (!empty($lang)) {
			$lang = ' class="' . $lang . '"';
		}
		$highlight = ' class="highlight"';
		$this->_content[] = '<div' . $highlight . '><pre><code' . $lang . '>' . $code . '</code></pre></div>';
		return $this;
	}

	/*
	 * LIST
	 */

	public function ul(array $ul) {
		$this->_content[] = '<ul>';
		foreach ($ul as $li) {
			$this->_content[] = '<li>' . $li . '</li>';
		}
		$this->_content[] = '</ul>';
		return $this;
	}

} // End Force_Documentation
