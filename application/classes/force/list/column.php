<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_List_Column
 * User: legion
 * Date: 15.08.14
 * Time: 19:45
 */
class Force_List_Column extends Force_Control {

	use Force_Attributes_Header;

	const BUTTON_EDIT = 'edit';
	const BUTTON_DELETE = 'delete';

	const TYPE_CONTROL = 'control';
	const TYPE_STRING = 'string';
	const TYPE_NUMBER = 'number';
	const TYPE_DATE = 'date';

	// показатель выравнивания
	protected $_replaced_by_alignment = array(
		'table-col-left',
		'table-col-middle',
		'table-col-right',
	);

	// показатель типа
	protected $_replaced_by_type = array(
		'table-col-control',
		'table-col-date',
		'table-col-number',
		'table-col-string',
	);

	// показатель ширины
	protected $_replaced_by_main = array(
		'table-col-control',
	);

	// control одновременно является и показателем типа и показателем ширины
	protected $_replaced_by_control = array(
		'table-col-main',
		'table-col-date',
		'table-col-number',
		'table-col-string',
	);

	protected $_id_field = 'id';
	protected $_link = null;
	protected $_back_url = null;
	protected $_is_button = false;
	protected $_in_db = true;
	protected $_get_label_from_model = false;

	protected $_date_format = null;

	protected $_show_date = true;
	protected $_show_year = true;

	protected $_show_time = true;
	protected $_show_seconds = false;

	protected $_type = null;

	protected $_is_sorting_data = false;
	protected $_is_sortable = null; // null = auto by default

	public function __construct($name = null) {
		if (!is_null($name) && !is_numeric($name)) {
			$this->_name = (string)$name;
		} else {
			$this->_name = $name;
		}
	}

	public static function factory($name = null) {
		return new self($name);
	}

	/*
	 * RENDER HEADER
	 */

	public function render_header($label = null, $field_name = null) {
		if ($this->is_show_label()) {

			if (is_null($label)) {
				$label = (!is_null($this->_label)) ? $this->_label : $this->_name;
			} else {
				$label = (string)$label;
			}

			if (mb_strtolower($label) == 'id') {
				$label = 'ID';
				$this->col_control();
			}

			if (!empty($label) && !$this->is_button() && $this->_in_db && !empty($field_name)) {
				$by = Arr::get($_GET, Force_Filter::ORDER_BY_PARAM);
				$direction = 0;
				$icon = '';
				if (!empty($by) && ($by == $field_name)) {
					$direction = (integer)(boolean)Arr::get($_GET, Force_Filter::ORDER_DIRECTION_PARAM, true);
					if ($direction == 1) {
						$icon = Helper_Bootstrap::get_icon('fa-caret-up');
					} else {
						$icon = Helper_Bootstrap::get_icon('fa-caret-down');
					}
				}
				$uri = Force_URL::current()
					->query_param(Force_Filter::ORDER_BY_PARAM, $field_name)
					->query_param(Force_Filter::ORDER_DIRECTION_PARAM, (integer)!$direction)
					->get_url();
				$label = HTML::anchor($uri, $label . $icon);
			}
		} else {
			$label = null;
		}

		return self::render_header_tag($label, $this->render_header_attributes());
	}

	public static function render_header_tag($label = null, $attributes = null) {
		if (is_array($attributes)) {
			$attributes = HTML::attributes($attributes);
		}
		return '<th' . $attributes . '>' . $label . '</th>';
	}

	/*
	 * RENDER CELL
	 */

	public function render($value = null, $id = null, array $attributes = array(), $overwrite = null) {
		if (is_null($value)) {
			if ($this->_is_button) {
				if (!($this->_value instanceof Force_Button)) {
					$caption = (is_string($this->_value)) ? $this->_value : '';
					$this->_value = Force_Button::factory($caption);
				}

				if (!is_null($id)) {
					$link = rtrim($this->_link, ' /') . '/' . trim($id, ' /');
					$this->_value->link($link, $this->_back_url);
				}
			}
			$value = $this->_value;
		}

		if ($value instanceof Force_Button) {
			$value = $value->btn_xs()->render();
		}

		return Force_List_Row::render_cell_tag($value, $this->get_attributes_merge($attributes, $overwrite));
	}

	public function render_custom($value = null, array $attributes = array(), $overwrite = null) {
		if ($value instanceof Force_Button) {
			$value = $value->btn_xs()->render();
		}

		return Force_List_Row::render_cell_tag($value, $this->get_attributes_merge($attributes, $overwrite));
	}

	/*
	 * GET
	 */

	public function get_name() {
		if (is_null($this->_name)) {
			$this->_name = md5($this->_label . $this->_value);
		}
		return parent::get_name();
	}

	public function get_type() {
		return $this->_type;
	}

	public function get_id_field() {
		return $this->_id_field;
	}

	/*
	 * SET
	 */

	public function label($value) {
		$this->_label = (string)$value;
		return $this;
	}

	public function in_db($value = null) {
		if (is_null($value)) {
			return $this->_in_db;
		} else {
			$this->_in_db = boolval($value);
		}
		return $this;
	}

	/*
	 * BUTTON
	 */

	/**
	 * Указывает полю что далее ему будет присвоена кнопка.
	 * Кнопки по умолчанию не имеют заголовка поля и ужаты по своей ширине.
	 * @return $this
	 */
	public function button_place() {
		if (empty($this->_label)) {
			$this->label('');
		}
		$this->col_control();
		return $this;
	}

	/**
	 * Стандартная кнопка редактирования - в большинстве случаев не нуждается в заполнении каких либо полей,
	 * т.е. достаточно просто указать полю ->button_edit() чтобы кнопка заработала.
	 * Поля же указываются если требуется добиться результата отличного от стандартных установок.
	 * Переопределять кнопку в each() не требуется.
	 *
	 * @param null $link_without_id
	 * @param string $id_field
	 * @param null $back_url
	 *
	 * @return $this
	 */
	public function button_edit($link_without_id = null, $id_field = 'id', $back_url = null) {
		$this->button(Force_Button::preset_edit(), $link_without_id, $id_field, $back_url);
		if (empty($link_without_id)) {
			$this->_link = rtrim($this->_link, ' /') . '/edit';
		}
		return $this;
	}

	/**
	 * Стандартная кнопка удаления - в большинстве случаев не нуждается в заполнении каких либо полей,
	 * т.е. достаточно просто указать полю ->button_delete() чтобы кнопка заработала.
	 * Поля же указываются если требуется добиться результата отличного от стандартных установок.
	 * Переопределять кнопку в each() не требуется.
	 *
	 * @param null $link_without_id
	 * @param string $id_field
	 * @param null $back_url
	 *
	 * @return $this
	 */
	public function button_delete($link_without_id = null, $id_field = 'id', $back_url = null) {
		$this->button(Force_Button::preset_delete(), $link_without_id, $id_field, $back_url);
		if (empty($link_without_id)) {
			$this->_link = rtrim($this->_link, ' /') . '/delete';
		}
		return $this;
	}

	/**
	 * Кастомизированная кнопка - важной особенностью является дополнение ссылки
	 * $link_without_id данными из поля $id_field - таким образом данный метод создания кнопки
	 * работает ТОЛЬКО в условиях простых конкатенаций.
	 * Если же требуется задавать ссылку по каким-либо иным условиям - следует воспользоваться
	 * методом button_place() и полностью переопределить кнопку в each().
	 *
	 * @param Force_Button $button
	 * @param null $link_without_id
	 * @param string $id_field
	 * @param null $back_url
	 *
	 * @return $this
	 */
	public function button(Force_Button $button, $link_without_id = null, $id_field = 'id', $back_url = null) {
		$this->button_place();
		$this->_value = $button;
		$this->_is_button = true;
		if (!empty($link_without_id)) {
			$this->_link = URL::site($link_without_id);
		} else {
			$request = Request::current();
			$this->_link = URL::site($request->directory() . '/' . $request->controller());
		}
		if (is_null($id_field)) {
			$this->_id_field = null;
		} else {
			$this->_id_field = (string)$id_field;
		}

		$this->_back_url = $back_url;

		return $this;
	}

	/**
	 * Отвечает является ли поле полем с кнопкой.
	 * Параметр _is_button устанавливается только методом button()
	 * и используется в render() для принудительного создания кнопки.
	 *
	 * @return bool
	 */
	public function is_button() {
		return (boolean)$this->_is_button;
	}

	/*
	 * DATE AND TIME
	 */

	public function date_format($date_format, $allow_wrap = false) {
		$this->_date_format = (string)$date_format;
		if ($allow_wrap) {
			$this->attribute('class', 'table-col-normal');
		}
		return $this;
	}

	public function get_date_format() {
		return $this->_date_format;
	}

	public function date_setup($show_year = true, $show_time = true) {
		$this->_show_date = true;
		$this->_show_year = boolval($show_year);
		$this->_show_time = boolval($show_time);
		return $this;
	}

	public function time_setup($show_date = false, $show_seconds = false) {
		$this->_show_time = true;
		$this->_show_date = boolval($show_date);
		$this->_show_seconds = boolval($show_seconds);
		return $this;
	}

	public function is_show_date() {
		return $this->_show_date;
	}

	public function is_show_time() {
		return $this->_show_time;
	}

	public function is_show_year() {
		return $this->_show_year;
	}

	public function is_show_seconds() {
		return $this->_show_seconds;
	}

	/*
	 * SORTABLE
	 */

	public function sortable($value = true) {
		$this->_is_sortable = boolval($value);
		return $this;
	}

	public function is_sortable() {
		return $this->_is_sortable;
	}

	public function sorting_data() {
		$this->label('<i class="fa fa-sort"></i>');

		$this->header_attribute('class', 'col-sortable')
			->col_control()
			->col_left();

		$this->_is_sorting_data = true;
	}

	public function is_sorting_data() {
		return $this->_is_sorting_data;
	}

	/*
	 * HELPERS FOR COL CLASSES
	 */

	/*
	 * width
	 */

	public function col_main($clean_classes = false, $for_header = true, $for_data = true) {
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_main, 'table-col-main', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_main, 'table-col-main', $clean_classes);
		}
		return $this;
	}

	public function col_width($width, $for_header = true, $for_data = true) {
		if ($for_header) {
			$this->header_attribute('style', 'min-width:' . $width);
		}
		if ($for_data) {
			$this->attribute('style', 'min-width:' . $width);
		}
		return $this;
	}

	/*
	 * position
	 */

	public function col_middle($clean_classes = false, $for_header = true, $for_data = true) {
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_alignment, 'table-col-middle', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_alignment, 'table-col-middle', $clean_classes);
		}
		return $this;
	}

	public function col_left($clean_classes = false, $for_header = true, $for_data = true) {
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_alignment, 'table-col-left', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_alignment, 'table-col-left', $clean_classes);
		}
		return $this;
	}

	public function col_right($clean_classes = false, $for_header = true, $for_data = true) {
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_alignment, 'table-col-right', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_alignment, 'table-col-right', $clean_classes);
		}
		return $this;
	}

	/*
	 * type
	 */

	public function col_control($clean_classes = false, $for_header = true, $for_data = true) {
		$this->_type = self::TYPE_CONTROL;
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_control, 'table-col-control', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_control, 'table-col-control', $clean_classes);
		}
		return $this;
	}

	public function col_date($clean_classes = false, $for_header = true, $for_data = true) {
		$this->_type = self::TYPE_DATE;
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_type, 'table-col-date', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_type, 'table-col-date', $clean_classes);
		}
		return $this;
	}

	public function col_number($clean_classes = false, $for_header = true, $for_data = true) {
		$this->_type = self::TYPE_NUMBER;
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_type, 'table-col-number', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_type, 'table-col-number', $clean_classes);
		}
		return $this;
	}

	public function col_string($clean_classes = false, $for_header = true, $for_data = true) {
		$this->_type = self::TYPE_STRING;
		if ($for_header) {
			$this->replace_header_attribute_class($this->_replaced_by_type, 'table-col-string', $clean_classes);
		}
		if ($for_data) {
			$this->replace_attribute_class($this->_replaced_by_type, 'table-col-string', $clean_classes);
		}
		return $this;
	}

	/*
	 * specific
	 */

	public function col_no_wrap($clean_classes = false, $for_header = false, $for_data = true) {
		if ($for_header) {
			$this->header_attribute('class', 'table-col-no-wrap', $clean_classes);
		}
		if ($for_data) {
			$this->attribute('class', 'table-col-no-wrap', $clean_classes);
		}
		return $this;
	}

} // End Force_List_Column
