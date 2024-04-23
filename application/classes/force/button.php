<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Button
 * User: legion
 * Date: 15.08.14
 * Time: 12:15
 */
class Force_Button extends Force_Control {

	use Force_Control_Icon;
	use Force_Control_Link;

	protected $_classes = array(
		'btn-default',
		'btn-primary',
		'btn-success',
		'btn-info',
		'btn-warning',
		'btn-danger',
		'btn-lg',
		'btn-sm',
		'btn-xs',
	);

	protected $_colors = array(
		'btn-default',
		'btn-primary',
		'btn-success',
		'btn-info',
		'btn-warning',
		'btn-danger',
	);

	protected $_sizes = array(
		'btn-lg',
		'btn-sm',
		'btn-xs',
	);

	protected $_confirmation = '';
	protected $_submit = false;
	protected $_color = 'btn-default';
	protected $_color_overwrite = true;
	protected $_size = '';
	protected $_size_overwrite = true;

	public function __construct($label = null, $name = null) {
		$this->label($label);
		if (!empty($name)) {
			$this->name($name);
		}
	}

	public static function factory($label = null, $name = null) {
		return new self($label, $name);
	}

	public function __toString() {
		return $this->render();
	}

	public function render() {
		// classes
		if (!$this->_simple) {
			$this->attribute('class', 'btn');
			$this->replace_attribute_class($this->_colors, $this->_color);
			$this->replace_attribute_class($this->_sizes, $this->_size);
		}

		if (!is_null($this->_name) && ($this->_name != '')) {
			$this->attribute('name', $this->_name);
		}
		if (!is_null($this->_value) && ($this->_value != '')) {
			$this->attribute('value', $this->_value);
		}

		// icon
		$icon = $this->get_icon();

		// as link
		if (!empty($this->_link)) {
			$this->attribute('href', $this->get_link());
			return '<a ' . $this->render_attributes() . '>' . $icon . $this->_label . '</a>';
		}

		// submit
		if ($this->_submit === true) {
			$this->attribute('type', 'submit');
			if (!empty($this->_confirmation)) {
				$this->attribute('onclick', 'return confirm(\'' . addslashes($this->_confirmation) . '\')');
			}
		} elseif (is_string($this->_submit) && !empty($this->_submit)) {
			$on_click = '$("' . $this->_submit . '").submit()';
			if (!empty($this->_confirmation)) {
				$on_click = 'if (confirm(\'' . addslashes($this->_confirmation) . '\')) {' . $on_click . '}';
			}
			$this->attribute('onclick', $on_click);
		}

		if (!$this->attribute_exists('type')) {
			$this->attribute('type', 'button');
		}

		// button

		return '<button ' . $this->render_attributes() . '>' . $icon . $this->_label . '</button>';
	}

	/*
	 * SET
	 */

	public function confirmation($message) {
		$this->_confirmation = (string)$message;
		return $this;
	}

	public function submit($form_selector = null) {
		$this->_submit = (is_string($form_selector) && !empty($form_selector)) ? $form_selector : true;
		return $this;
	}

	public function modal($modal_id) {
		$this->attribute('data-toggle', 'modal');
		$this->attribute('data-target', '#' . $modal_id);
		return $this;
	}

	public function modal_close() {
		$this->attribute('data-dismiss', 'modal');
		return $this;
	}

	/*
	 * SIZE
	 */

	public function btn_lg() {
		$this->_size = 'btn-lg';
		return $this;
	}

	public function btn_sm() {
		$this->_size = 'btn-sm';
		return $this;
	}

	public function btn_xs() {
		$this->_size = 'btn-xs';
		return $this;
	}

	/*
	 * PRESET STYLES
	 */

	public function btn_default() {
		$this->_color = 'btn-default';
		return $this;
	}

	public function btn_primary() {
		$this->_color = 'btn-primary';
		return $this;
	}

	public function btn_success() {
		$this->_color = 'btn-success';
		return $this;
	}

	public function btn_info() {
		$this->_color = 'btn-info';
		return $this;
	}

	public function btn_warning() {
		$this->_color = 'btn-warning';
		return $this;
	}

	public function btn_danger() {
		$this->_color = 'btn-danger';
		return $this;
	}

	public function btn_disabled() {
		$this->attribute('disabled');
		return $this;
	}

	public function btn_enabled() {
		$this->remove_attribute('disabled');
		return $this;
	}

	/*
	 * PRESET STYLES CHECK
	 */

	public function is_btn_default() {
		return ($this->_color == 'btn-default');
	}

	public function is_btn_primary() {
		return ($this->_color == 'btn-primary');
	}

	public function is_btn_success() {
		return ($this->_color == 'btn-success');
	}

	public function is_btn_info() {
		return ($this->_color == 'btn-info');
	}

	public function is_btn_warning() {
		return ($this->_color == 'btn-warning');
	}

	public function is_btn_danger() {
		return ($this->_color == 'btn-danger');
	}

	public function is_btn_disabled() {
		return $this->attribute_exists('disabled');
	}

	public function is_btn_enabled() {
		return !$this->is_btn_disabled();
	}

	/*
	 * COLORS ALTERNATIVE
	 */

	public function color_gray() {
		$this->btn_default();
		return $this;
	}

	public function color_blue() {
		$this->btn_primary();
		return $this;
	}

	public function color_green() {
		$this->btn_success();
		return $this;
	}

	public function color_cyan() {
		$this->btn_info();
		return $this;
	}

	public function color_yellow() {
		$this->btn_warning();
		return $this;
	}

	public function color_red() {
		$this->btn_danger();
		return $this;
	}

	/*
	 * COLORS ALTERNATIVE CHECK
	 */

	public function has_color_gray() {
		return $this->is_btn_default();
	}

	public function has_color_blue() {
		return $this->is_btn_primary();
	}

	public function has_color_green() {
		return $this->is_btn_success();
	}

	public function has_color_cyan() {
		return $this->is_btn_info();
	}

	public function has_color_yellow() {
		return $this->is_btn_warning();
	}

	public function has_color_red() {
		return $this->is_btn_danger();
	}

	/*
	 * PREDEFINED SETUP
	 */

	/**
	 * @param null $link
	 * @param bool $link_auto
	 * @param null $back_url
	 *
	 * @return Force_Button
	 */
	public static function preset_add($link = null, $link_auto = false, $back_url = null) {
		$button = Force_Button::factory(__('common.add'));
		$button->icon('fa-plus');
		$button->color_green();
		if (empty($link) && $link_auto) {
			$link = Force_URL::current_clean()
				->action('add')
				->get_url();
		}
		if (!empty($link)) {
			$button->link($link, $back_url);
		}
		return $button;
	}

	/**
	 * @param null $link_or_id
	 * @param null $back_url
	 *
	 * @return Force_Button
	 */
	public static function preset_edit($link_or_id = null, $back_url = null) {
		$button = Force_Button::factory(__('common.edit'));
		$button->icon('fa-edit');
		$button->color_yellow();
		if (is_numeric($link_or_id) && ($link_or_id > 0)) {
			$link_or_id = Force_URL::current_clean()
				->action('edit')
				->route_param('id', $link_or_id)
				->get_url();
		}
		if (!empty($link_or_id)) {
			$button->link($link_or_id, $back_url);
		}
		return $button;
	}

	/**
	 * @param null $link_or_id
	 * @param null $back_url
	 *
	 * @return Force_Button
	 */
	public static function preset_delete($link_or_id = null, $back_url = null) {
		$button = Force_Button::factory(__('common.delete'));
		$button->icon('fa-close');
		$button->confirmation(__('common.delete.confirmation'));
		$button->color_red();
		if (is_numeric($link_or_id) && ($link_or_id > 0)) {
			$link_or_id = Force_URL::current_clean()
				->action('delete')
				->route_param('id', $link_or_id)
				->get_url();
		}
		if (!empty($link_or_id)) {
			$button->link($link_or_id, $back_url);
		}
		return $button;
	}

	/**
	 * @param null $link_or_id
	 * @param null $back_url
	 *
	 * @return Force_Button
	 */
	public static function preset_restore($link_or_id = null, $back_url = null) {
		$button = Force_Button::factory(__('common.restore'));
		$button->icon('fa-undo');
		$button->confirmation(__('common.restore.confirmation'));
		$button->color_blue();
		if (is_numeric($link_or_id) && ($link_or_id > 0)) {
			$link_or_id = Force_URL::current_clean()
				->action('restore')
				->route_param('id', $link_or_id)
				->get_url();
		}
		if (!empty($link_or_id)) {
			$button->link($link_or_id, $back_url);
		}
		return $button;
	}

} // End Force_Button
