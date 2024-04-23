<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_List
 * User: legion
 * Date: 15.08.14
 * Time: 12:09
 */
class Force_List extends Force_List_Columns {

	use Force_Control_Buttons;

	const ID_FIELD_FILE_LINE_NUMBER = '__FILE_LINE_NUMBER__';
	const ROW_NUMBER = '__ROW_NUMBER__';
	const FORMAT = '__FORMAT__';

//	const ARRAY_ROWS_WITH_COLS = 100;
//	const ARRAY_COLS_WITH_ROWS = 200;
//
//	protected $_array_data_organisation = self::ARRAY_ROWS_WITH_COLS;

	protected $_fields = null;
	protected $_rows = array();
	protected $_rows_before = array();
	protected $_rows_after = array();
	protected $_pagination = null;
	protected $_title = '';
	protected $_view = 'force/list/index';
	protected $_total_items_count = 0;
	protected $_items_per_page = 20;
	protected $_callback = null;
	protected $_callback_params = array();
	protected $_is_data_applied = false;
	protected $_is_button_add = false;
	protected $_array_of_rows = true;
	protected $_array_of_models = false;

	protected $_has_col_sorting_data = true;

	protected $_model = null;
	protected $_aliases = array();

	protected $_message_list_empty = null;

	/*
	 * DISPLAY SETUP
	 */
	protected $_display_title = false;
	protected $_display_count = false;
	protected $_display_pagination = false;
	protected $_display_sorting = false;
	protected $_display_headers = true;
	protected $_display_items_per_page_selector = false;
	protected $_display_more_rows_button = false;
	protected $_display_more_rows = false;

	/*
	 * BUTTON MORE
	 */
	protected $_button_more_url = NULL;
	protected $_button_more = NULL;

	public function __construct(array $columns = array()) {
		parent::__construct($columns);
		$this->attribute('class', 'table table-striped');
	}

	public static function factory(array $columns = array()) {
		return new self($columns);
	}

	public function __toString() {
		return $this->render();
	}

	/*
	 * SET
	 */

	public function __set($name, $value) {
		$this->_set_buttons($name, $value);
	}

	/*
	 * RENDER
	 */

	public function render($table_body = NULL) {
		$items_count = count($this->_rows);
		if ($items_count < $this->_total_items_count) {
			$showed_count = __('common.count_from_total', array(
				':count' => $items_count,
				':total' => $this->_total_items_count,
			));
		} else {
			$showed_count = $items_count;
		}

		if ($this->_display_title && empty($this->_title)) {
			$title = Request::current()->controller();
			$this->title(Force_Menu_Item::update_name($title));
		}

		$fields = $this->_get_fields();

		if ($this->_rows instanceof Jelly_Collection || $this->_array_of_models) {
			foreach ($this->_columns as $name => $column) {
				if ($column instanceof Force_List_Column) {
					$column_type = $column->get_type();
					if (empty($column_type) && array_key_exists($name, $fields)) {
						if ($fields[$name] instanceof Jelly_Field_Timestamp) {
							$column->col_date();
						} elseif ($fields[$name] instanceof Jelly_Field_Integer) {
							$column->col_number();
						} elseif ($fields[$name] instanceof Jelly_Field_Float) {
							$column->col_number();
						} elseif ($fields[$name] instanceof Jelly_Field_Boolean) {
							$column->col_control();
						}
					}
				}
			}
		}

		$pagination = '';
		if ($this->_display_pagination) {
			if ($this->_pagination instanceof Pagination) {
				$this->_pagination->display_items_per_page_selector = $this->_display_items_per_page_selector;
				$pagination = $this->_pagination->render();
			}
			if ($this->_display_items_per_page_selector && empty($pagination)) {
				$pagination = Helper_Pagination::get_items_per_page_select_box_for_admin(true);
			}
		}

		$buttons_body = array();
		foreach ($this->_buttons as $button) {
			if ($button instanceof Force_Button) {
				$buttons_body[] = $button->render();
			} else {
				$buttons_body[] = $button; // custom html
			}
		}
		$buttons_body = implode(' ', $buttons_body);

		$header_body = $this->_render_table_head();

		if (empty($table_body)) {
			$table_body = $this->render_table_rows();
		}

		if (!empty($table_body)) {
			$table_body = "<tbody>\n" . $table_body . "</tbody>\n";
		}

		if ($this->_display_title) {
			if (empty($this->_title)) {
				$this->_title = __('menu.' . Request::current()->controller());
			}

			$title = $this->_title;
			if ($this->_display_count) {
				$title .= ' (' . $showed_count . ')';
			}
		} else {
			$title = '';
		}

		if (empty($this->_message_list_empty)) {
			$this->_message_list_empty = __('common.search.result.empty');
		}

		if ($this->_has_col_sorting_data) {
			$this->attribute('class', 'table-sortable');

			Helper_Assets::add_js_vars('sortable_url', Force_URL::current_clean()
				->action('sort_fields')
				->data_json());

			Helper_Assets::add_scripts_in_footer([
				'/assets/common/js/jquery-ui-1.11.4/jquery-ui.min.js',
				'/assets/common/js/sortable.js',
			]);
		}

		if (empty($table_body)) {
			$this->attribute('style', 'display:none');
		}

		return View::factory($this->_view)->bind('total_items_count', $this->_total_items_count)
			->set('attributes', $this->get_attributes())
			->bind('showed_count', $showed_count)
			->bind('pagination', $pagination)
			->bind('buttons_body', $buttons_body)
			->bind('header_body', $header_body)
			->bind('table_body', $table_body)
			->bind('title', $title)
			->bind('message_list_empty', $this->_message_list_empty)
			->render();
	}

	public function render_table_rows() {
		$table_body = $this->_render_custom_rows($this->_rows_before);

		if ($this->_rows instanceof Jelly_Collection || $this->_array_of_models) {
			$table_body .= $this->_render_table_body_from_collection();
		} else if (is_array($this->_rows)) {
			$table_body .= $this->_render_table_body_from_array();
		}

		$table_body .= $this->_render_custom_rows($this->_rows_after);

		$table_body .= $this->_render_button_more();

		return $table_body;
	}

	protected function _render_table_head() {
		$fields = $this->_get_fields();

		$header_body = '';
		if ($this->_display_headers) {
			$header_body = "<thead>\n<tr>";
			foreach ($this->_columns as $name => $column) {
				if ($column instanceof Force_List_Column) {
					$_label = $column->get_label();
					if (is_null($_label) && array_key_exists($name, $fields)) {
						$_label = $fields[$name]->label;
					}
					$field_name = null;
					if ($this->_display_sorting && ($column->is_sortable() !== false)) {
						if (array_key_exists($name, $fields)) {
							$field_name = $name;
						} elseif (array_key_exists($name, $this->_aliases)) {
							$field_name = $name;
						}
						if ($column->is_sortable() === true) {
							$field_name = $name;
						}
					}
					$header_body .= $column->render_header($_label, $field_name);
				} else {
					$header_body .= Force_List_Column::render_header_tag($name);
				}
			}
			$header_body .= "</tr>\n</thead>\n";
		}
		return $header_body;
	}

	protected function _render_table_body_from_collection() {
		$fields = $this->_get_fields();

		$row_number = 0;
		$table_body = '';
		foreach ($this->_rows as $_key => $_model) {
			$current_row = null;
			$row_number++;
			$_model->{self::ROW_NUMBER} = $row_number;
			if (is_callable($this->_callback) && is_array($this->_callback_params)) {
				$current_row = call_user_func_array($this->_callback, array_merge(array(&$_model), $this->_callback_params));
			}
			if (!($current_row instanceof Force_List_Row)) {
				$current_row = Force_List_Row::factory();
			}
			$row = '';
			foreach ($this->_columns as $name => $column) {
				$formatted_value = true;
				$value = is_string($name) ? $_model->{$name . self::FORMAT} : null;
				if (is_null($value)) {
					$formatted_value = false;
					$value = is_string($name) ? $_model->{$name} : null;
				}
				if ($column instanceof Force_List_Column) {
					/*
					 * SORTING DATA
					 */
					if ($column->is_sorting_data()) {
						$this->_has_col_sorting_data = true;
						$current_row->attribute('data-id', $_model->id);
						$value = Helper_Admin::label_sortable($value);
						$column->attribute('class', 'col-sortable');
					}
					if (array_key_exists($name, $fields) && !$formatted_value) {
						if ($fields[$name] instanceof Jelly_Field_Timestamp) {
							if (!empty($value)) {
								$date_format = $column->get_date_format();
								if (!empty($date_format)) {
									$value = Force_Date::factory($value)->format($date_format);
								} else {
									$value = Force_Date::factory($value)
										->show_date($column->is_show_date())
										->show_time($column->is_show_time())
										->show_year($column->is_show_year())
										->show_seconds($column->is_show_seconds())
										->humanize();
								}
							}
						}
					} elseif (!empty($value) && !$formatted_value) {
						$date_format = $column->get_date_format();
						if (!empty($date_format)) {
							$column->col_date();
							$value = Force_Date::factory($value)->format($date_format);
						}
					}
					$id_field = $column->get_id_field();
					if (is_null($id_field)) {
						$id = $_key;
					} else {
						$id = $_model->{$id_field};
					}
					$overwrite = null;
					$attributes = array();
					if ($current_row instanceof Force_List_Row) {
						$overwrite = $current_row->get_cell_overwrite($name);
						$attributes = $current_row->get_cell_attributes($name);
					}
					$row .= $column->render($value, $id, $attributes, $overwrite);
				} else {
					$row .= $current_row->render_cell($name, $value);
				}
			}
			$table_body .= $current_row->render_open() . $row . $current_row->render_close();
		}
		return $table_body;
	}

	protected function _render_table_body_from_array() {
		$row_number = 0;
		$table_body = '';
		foreach ($this->_rows as $_key => $_row) {
			if (!is_array($_row)) {
				continue;
			}
			$current_row = null;
			$row_number++;
			$_row[self::ROW_NUMBER] = $row_number;
			if (is_callable($this->_callback) && is_array($this->_callback_params)) {
				$current_row = call_user_func_array($this->_callback, array_merge(array(&$_row), $this->_callback_params));
			}
			unset($_row[self::ROW_NUMBER]);
			if ($current_row instanceof Force_List_Row) {
				$row = $current_row->render_open();
			} else {
				$row = Force_List_Row::render_open_tag();
			}

			$is_assoc = Arr::is_assoc($_row);

			$col_number = 0;
			foreach ($this->_columns as $name => $column) {
				$value = null;

				if ($is_assoc) {
					$value = Arr::get($_row, $name);
				} else {
					$value = Arr::get($_row, $col_number);
				}

				if ($column instanceof Force_List_Column) {
					/*
					 * Проверяем значение поля
					 * - если это элемент формы - заставляем его срендерится
					 * - если это число, спрашиваем у колонки не был ли ей передан формат даты,
					 * и если был то конвертируем число в дату в заданном формате.
					 */
					if (Force_Form_Controls::check_control($value)) {
						$value = $value->simple()->render();
					} elseif (!empty($value) && is_numeric($value)) {
						$date_format = $column->get_date_format();
						if (!empty($date_format)) {
							$value = Force_Date::factory($value)->format($date_format);
						}
					}

					$id_field = $column->get_id_field();
					if (is_null($id_field)) {
						$id = $_key;
					} else {
						$id = (array_key_exists($id_field, $_row)) ? $_row[$id_field] : null;
					}
					$overwrite = null;
					$attributes = array();
					if ($current_row instanceof Force_List_Row) {
						$overwrite = $current_row->get_cell_overwrite($name);
						$attributes = $current_row->get_cell_attributes($name);
					}
					$row .= $column->render($value, $id, $attributes, $overwrite);
				} else {
					if ($current_row instanceof Force_List_Row) {
						$row .= $current_row->render_cell($name, $value);
					} else {
						$row .= Force_List_Row::render_cell_tag($value);
					}
				}
				$col_number++;
			}
			if ($current_row instanceof Force_List_Row) {
				$row .= $current_row->render_close();
			} else {
				$row .= Force_List_Row::render_close_tag();
			}
			$table_body .= $row;
		}
		return $table_body;
	}

	protected function _render_custom_rows(&$rows) {
		$table_body = '';
		foreach ($rows as $_row) {
			if ($_row instanceof Force_List_Row_Custom) {
				$row_data = $_row->get_data();
				$current_row = $_row->get_params();
				if (!empty($row_data)) {
					if ($current_row instanceof Force_List_Row) {
						$row = $current_row->render_open();
					} else {
						$row = Force_List_Row::render_open_tag();
					}
					foreach ($this->_columns as $name => $column) {
						$value = array_key_exists($name, $row_data) ? $row_data[$name] : null;

						if ($column instanceof Force_List_Column) {
							/*
							 * Проверяем значение поля
							 * - если это элемент формы - заставляем его срендерится
							 * - если это число, спрашиваем у колонки не был ли ей передан формат даты,
							 * и если был то конвертируем число в дату в заданном формате.
							 */
							if (Force_Form_Controls::check_control($value)) {
								$value = $value->simple()->render();
							} elseif (!empty($value) && is_numeric($value)) {
								$date_format = $column->get_date_format();
								if (!empty($date_format)) {
									$value = Force_Date::factory($value)->format($date_format);
								}
							}

							$overwrite = null;
							$attributes = array();
							if ($current_row instanceof Force_List_Row) {
								$overwrite = $current_row->get_cell_overwrite($name);
								$attributes = $current_row->get_cell_attributes($name);
							}
							$row .= $column->render_custom($value, $attributes, $overwrite);
						} else {
							if ($current_row instanceof Force_List_Row) {
								$row .= $current_row->render_cell($name, $value);
							} else {
								$row .= Force_List_Row::render_cell_tag($value);
							}
						}
					}
					if ($current_row instanceof Force_List_Row) {
						$row .= $current_row->render_close();
					} else {
						$row .= Force_List_Row::render_close_tag();
					}
					$table_body .= $row;
				}
			}
		}
		return $table_body;
	}

	protected function _render_button_more() {
		if (!$this->_display_more_rows_button) {
			return '';
		}

		$id = $this->get_attribute('id');

		if (empty($id)) {
			$id = uniqid();
			$this->attribute('id', $id);
		}

		$params = array(
			'id' => $id,
			'params' => array(
				'url_more' => (string)$this->_button_more_url,
			),
		);

		Helper_Assets::js_vars_push_array('force_list', $params);
		Helper_Assets::add_scripts('assets/common/js/list.js');

		$button = $this->_button_more;

		if (!($button instanceof Force_Button)) {
			$button = Force_Button::factory(__('common.show_more'))
				->attribute('class', 'table-button-more');
		}

		$col_span = $this->get_columns_count();

		$table_body = '<tr class="table-row-more"><td class="table-col-middle" colspan="' . $col_span . '">' . $button->render() . '</td></tr>';
		return $table_body;
	}

	/*
	 * HELPERS
	 */

	protected function _apply_pagination(Jelly_Builder $builder, Pagination $pagination) {
		$page = (int)Request::current()->post('page');

		if ($page < 1) {
			$page = 1;
		}

		$page += $pagination->current_page - 1;

		if ($page < 1) {
			$page = 1;
		}

		$offset = (int)(($page - 1) * $pagination->items_per_page);

		$builder->limit($pagination->items_per_page);
		$builder->offset($offset);
	}

	protected function _get_fields() {
		if ($this->_rows instanceof Jelly_Collection) {
			$fields = $this->_rows->meta()->fields();
		} else {
			/*
			 * Проверка на массив моделей
			 */
			foreach ($this->_rows as $model) {
				if ($model instanceof Jelly_Model) {
					$fields = $model->meta()->fields();
					$this->_array_of_models = true;
					return $fields;
				}
				break;
			}
			$fields = array();
		}

		return $fields;
	}

	/*
	 * APPLY DATA
	 */

	public function apply(&$data, $count_or_pagination = null, $apply_pagination = true) {
		if ($data instanceof Jelly_Builder) {
			$this->apply_jelly_builder($data, $count_or_pagination, $apply_pagination);
		}

		if ($data instanceof Jelly_Collection) {
			$this->apply_jelly_collection($data, $count_or_pagination, $apply_pagination);
		}

		if (is_array($data)) {
			$this->apply_array($data, $count_or_pagination, $apply_pagination);
		}

		return $this;
	}

	public function apply_jelly_builder(Jelly_Builder &$builder, $count_or_pagination = null, $apply_pagination = true) {
		if ($this->_is_data_applied) {
			return $this;
		}

		if ($this->_display_more_rows_button || $this->_display_more_rows) {
			$this->_display_pagination = false;

			/*
			 * Есть общий каунт - хорошо. Нету? Тоже хорошо.
			 * В этом вся и прелесть досылки строк постранично.
			 */
			if (!($count_or_pagination instanceof Pagination)) {
				$pagination = Helper_Pagination::get_admin_pagination($count_or_pagination);
			}

			$this->_total_items_count = $pagination->total_items;
			$this->_items_per_page = $pagination->items_per_page;

			$this->_pagination = $pagination;

			$this->_apply_pagination($builder, $this->_pagination);
		} else {
			/*
			 * Стандартная пагинация. Требует общего количества записей.
			 */
			if ($apply_pagination) {
				if ($count_or_pagination instanceof Pagination) {
					$pagination = $count_or_pagination;
				} else {
					if (!is_numeric($count_or_pagination)) {
						$count_or_pagination = $builder->count();
					}
					if ($count_or_pagination < 0) {
						$count_or_pagination = 0;
					}
					$pagination = Helper_Pagination::get_admin_pagination($count_or_pagination);
				}
				$this->_total_items_count = $pagination->total_items;
				$this->_items_per_page = $pagination->items_per_page;

				$this->_pagination = $pagination;

				$builder->apply_pagination($this->_pagination);
			} else {
				/*
				 * _display_pagination однозначно в этом методе определяется только здесь, потому что
				 * даже в случае $apply_pagination = true перед запуском apply может быть вызван метод
				 * скрывающий пагинацию. Таким образом она будет применена, но при этом скрыта.
				 */
				$this->_display_pagination = false;
			}
		}

		self::apply_sorting($builder);

		$this->aliases($builder->get_selected_aliases());

		$this->_rows = $builder->select_all();

		if (!$apply_pagination) {
			$this->_items_per_page = $this->_total_items_count = $this->_rows->count();
		}

		$this->_is_data_applied = true;
		return $this;
	}

	public function apply_jelly_collection(Jelly_Collection &$collection, $count_or_pagination = null, $apply_pagination = true) {
		if ($this->_is_data_applied) {
			return $this;
		}

		if ($apply_pagination) {
			if ($count_or_pagination instanceof Pagination) {
				$pagination = $count_or_pagination;
			} else {
				if (!is_numeric($count_or_pagination)) {
					$count_or_pagination = $collection->count();
				}
				if ($count_or_pagination < 0) {
					$count_or_pagination = 0;
				}
				$pagination = Helper_Pagination::get_admin_pagination($count_or_pagination);
			}
			$this->_total_items_count = $pagination->total_items;

			$this->_pagination = $pagination;
			$this->_items_per_page = $this->_pagination->items_per_page;
		} else {
			/*
			 * _display_pagination однозначно в этом методе определяется только здесь, потому что
			 * даже в случае $apply_pagination = true перед запуском apply может быть вызван метод
			 * скрывающий пагинацию. Таким образом она будет применена, но при этом скрыта.
			 */
			$this->_display_pagination = false;
			$this->_total_items_count = $collection->count();
			$this->_items_per_page = $this->_total_items_count;
		}

		$this->_rows = $collection;

		$this->_is_data_applied = true;
		return $this;
	}

	public function apply_array(array &$data, $count_or_pagination = null, $apply_pagination = false) {
		if ($this->_is_data_applied) {
			return $this;
		}

		if (!$this->_array_of_rows) {
			$_data = array();

			foreach ($data as $_col => $_rows) {
				if ($_rows instanceof Jelly_Model) {
					/*
					 * Проверка на массив моделей
					 */
					$this->_array_of_rows = true;
					$this->_array_of_models = true;
					break;
				}
				foreach ($_rows as $_row => $_value) {
					$_data[$_row][$_col] = $_value;
				}
			}

			$data = $_data;
			unset($_data);
		}

		if ($apply_pagination) {
			if ($count_or_pagination instanceof Pagination) {
				$pagination = $count_or_pagination;
			} else {
				if (!is_numeric($count_or_pagination)) {
					$count_or_pagination = count($data);
				}
				if ($count_or_pagination < 0) {
					$count_or_pagination = 0;
				}
				$pagination = Helper_Pagination::get_admin_pagination($count_or_pagination);
			}
			$this->_total_items_count = $pagination->total_items;

			$this->_pagination = $pagination;
			$this->_items_per_page = $this->_pagination->items_per_page;
			$data = array_slice($data, $this->_pagination->offset, $this->_pagination->items_per_page, true);
		} else {
			/*
			 * _display_pagination однозначно в этом методе определяется только здесь, потому что
			 * даже в случае $apply_pagination = true перед запуском apply может быть вызван метод
			 * скрывающий пагинацию. Таким образом она будет применена, но при этом скрыта.
			 */
			$this->_display_pagination = false;
			$this->_total_items_count = count($data);
			$this->_items_per_page = $this->_total_items_count;
		}

		$this->_rows = $data;

		$this->_is_data_applied = true;
		return $this;
	}

	public function apply_csv($filename, $separator = ';', $apply_pagination = true) {
		if ($this->_is_data_applied) {
			return $this;
		}

		$handle = fopen($filename, 'r');

		// Берём первую строку (заголовки колонок)
		$line = fgets($handle);
		$columns = explode($separator, $line);

		if (empty($this->_columns)) {
			foreach ($columns as $key => $column) {
				$this->add_column(Force_List_Column::factory($key)->label($column));
			}
		} else {
			foreach ($this->_columns as $column) {
				if ($column instanceof Force_List_Column && !$column->is_button()) {
					$_name = $column->get_name();
					$_label = $column->get_label();
					if (is_null($_label) && array_key_exists($_name, $columns)) {
						$column->label($columns[$_name]);
					}
				}
			}
		}

		/*
		 * Вычисляем количество строк простым перебором.
		 * Это и есть самое узкое место. Здесь хоть и построчно, но просматриваетя весь файл.
		 * Тем не менее, так как перебор идёт по одной строке, то потерь по памяти не будет,
		 * за-то будет проседание по времени.
		 * @TODO найти иной способ вычисления количества строк в файле, без перебора всех строк
		 */
		$count = 0;
		while (fgets($handle)) {
			$count++;
		}
		$this->_total_items_count = $count;
		$this->_pagination = Helper_Pagination::get_admin_pagination($count);
		$this->_items_per_page = $this->_pagination->items_per_page;

		// Возвращаемся в начало
		rewind($handle);
		// Пропускаем первую строку (заголовки колонок)
		fgets($handle);

		/*
		 * Ещё один узкий момент, так как перемещение курсора в файле выполняет только fgets(), то
		 * смещение до нужной строки также выполняется перебором файла. Например, если необходимо
		 * показать последнюю страницу, то мы уже дважды переберём весь файл построчно.
		 * @TODO найти иной способ установки указателя строки файла, без перебора всех строк до искомой
		 */
		if ($apply_pagination) {
			$row_number = 1;
			$offset = $this->_pagination->offset;
			if ($offset > 0) {
				for ($i = 0; $i < $offset; $i++) {
					fgets($handle);
					$row_number++;
				}
			}
		}

		$count = 1;
		while (!feof($handle)) {
			$line = fgets($handle);
			$cols = explode($separator, $line);
			$cols[self::ID_FIELD_FILE_LINE_NUMBER] = $row_number;
			$this->_rows[] = $cols;
			$count++;
			$row_number++;
			if ($count > $this->_items_per_page) {
				break;
			}
		}

		fclose($handle);

		$this->_is_data_applied = true;
		return $this;
	}

	public function get_pagination() {
		return $this->_pagination;
	}

	public function array_of_columns() {
		$this->_array_of_rows = false;
		return $this;
	}

	public function array_of_rows() {
		$this->_array_of_rows = true;
		return $this;
	}

	/*
	 * BUTTONS
	 */

	public function button_add($link = null, array $attributes = array()) {
		$this->button(Force_Button::factory(__('common.add'))->preset_add($link, true), null, $attributes);
		$this->_is_button_add = true;
		return $this;
	}

	/*
	 * SET
	 */

	public function title($title) {
		$this->_title = (string)$title;
		return $this;
	}

	public function view($view) {
		$view = (string)$view;
		$this->_view = trim($view, ' /');
		return $this;
	}

	public function aliases(array $field_aliases) {
		$this->_aliases = $field_aliases;
		return $this;
	}

	/*
	 * GET
	 */

	public function get_items_count() {
		if ($this->_rows instanceof Jelly_Collection) {
			return $this->_rows->count();
		} elseif (is_array($this->_rows)) {
			return count($this->_rows);
		}
		return 0;
	}

	public function get_items_per_page() {
		return $this->_items_per_page;
	}

	public function get_total_items_count() {
		return $this->_total_items_count;
	}

	public function get_rows() {
		return $this->_rows;
	}

	public function as_array($key = NULL, $value = NULL) {
		if ($this->_rows instanceof Jelly_Collection) {
			return $this->_rows->as_array($key, $value);

		} elseif (is_array($this->_rows)) {
			$results = array();

			if ($key === NULL AND $value === NULL) {
				// Indexed rows

				foreach ($this->_rows as $row) {
					$results[] = $row;
				}
			} elseif ($key === NULL) {
				// Indexed columns

				foreach ($this->_rows as $row) {
					$results[] = $row[$value];
				}
			} elseif ($value === NULL) {
				// Associative rows

				foreach ($this->_rows as $row) {
					$results[$row[$key]] = $row;
				}
			} else {
				// Associative columns

				foreach ($this->_rows as $row) {
					$results[$row[$key]] = $row[$value];
				}
			}

			return $results;
		}
		return array();
	}

	/*
	 * HEADERS
	 */

	public function show_headers($value = true) {
		$this->_display_headers = boolval($value);
		return $this;
	}

	public function hide_headers() {
		$this->_display_headers = false;
		return $this;
	}

	/*
	 * SORTING
	 */

	public function show_sorting($value = true) {
		$this->_display_sorting = boolval($value);
		return $this;
	}

	public function hide_sorting() {
		$this->_display_sorting = false;
		return $this;
	}

	public static function apply_sorting(Jelly_Builder &$builder) {
		$order_by = Arr::get($_REQUEST, Force_Filter::ORDER_BY_PARAM);
		$order_direction = (boolean)Arr::get($_REQUEST, Force_Filter::ORDER_DIRECTION_PARAM, true);
		if (!empty($order_by)) {
			$builder->clear_order_by();

			if (array_key_exists($order_by, $builder->meta()->fields())
				|| array_key_exists($order_by, $builder->get_selected_aliases())
			) {
				$builder->order_by($order_by, ($order_direction ? 'ASC' : 'DESC'));
			}
		}
	}

	/*
	 * TITLE
	 */

	public function show_title($value = true) {
		$this->_display_title = boolval($value);
		return $this;
	}

	public function hide_title() {
		$this->_display_title = false;
		return $this;
	}

	/*
	 * COUNT
	 */

	public function show_count($value = true) {
		$this->_display_count = boolval($value);
		return $this;
	}

	public function hide_count() {
		$this->_display_count = false;
		return $this;
	}

	/*
	 * PAGINATION
	 */

	public function show_pagination($value = true) {
		$this->_display_pagination = boolval($value);
		return $this;
	}

	public function hide_pagination() {
		$this->_display_pagination = false;
		return $this;
	}

	public function show_items_per_page_selector($value = true) {
		$this->_display_items_per_page_selector = boolval($value);
		return $this;
	}

	public function hide_items_per_page_selector() {
		$this->_display_items_per_page_selector = false;
		return $this;
	}

	/*
	 * ROWS
	 */

	public function each($function_with_model_as_param) {
		$this->_callback = $function_with_model_as_param;
		$params = func_get_args();
		array_shift($params);
		$this->_callback_params = $params;
		return $this;
	}

	public function add_row_before(array $row_data, Force_List_Row $row_params = null) {
		$this->_rows_before = array_merge([
			Force_List_Row_Custom::factory($row_data, $row_params),
		], $this->_rows_before);
		return $this;
	}

	public function add_row_after(array $row_data, Force_List_Row $row_params = null) {
		$this->_rows_after[] = Force_List_Row_Custom::factory($row_data, $row_params);
		return $this;
	}

	/*
	 * MORE ROWS
	 */

	public function show_more_rows_button($url_more, Force_Button $button = NULL) {
		$this->_display_more_rows_button = true;
		$this->_button_more_url = $url_more;
		$this->_button_more = $button;
		return $this;
	}

	public function hide_more_rows_button() {
		$this->_display_more_rows_button = false;
		$this->_button_more_url = NULL;
		$this->_button_more = NULL;
		return $this;
	}

	public function show_more_rows($value = true) {
		$this->_display_more_rows = boolval($value);
		return $this;
	}

	public function hide_more_rows() {
		$this->_display_more_rows = false;
		return $this;
	}

	/*
	 * MESSAGE
	 */

	public function message_list_empty($text) {
		$this->_message_list_empty = (string)$text;
		return $this;
	}

	/*
	 * SORTING
	 */

	protected function enable_sorting_data() {
		$sortable_url = Force_URL::current_clean()
			->action('sort_fields')
			->data_json();

		Helper_Assets::add_js_vars('sortable_url', $sortable_url);

		Helper_Assets::add_scripts_in_footer([
			'/assets/jquery-ui-1.11.4/jquery-ui.min.js',
			'/assets/common/js/sortable.js',
		]);

		$this->attribute('class', 'table-sortable');
	}

	/*
	 * PREDEFINED SETUP
	 */

	/**
	 * @return Force_List
	 */
	public function preset_for_admin() {
		$this->show_items_per_page_selector();
		$this->show_title();
		$this->show_count();
		$this->show_pagination();
		$this->show_sorting();
		$this->show_headers();
		return $this;
	}

	/**
	 * @return Force_List
	 */
	public function preset_basic() {
		$this->hide_items_per_page_selector();
		$this->hide_title();
		$this->hide_count();

		$this->show_pagination();
		$this->show_sorting();
		$this->show_headers();
		return $this;
	}

	/**
	 * @return Force_List
	 */
	public function preset_simple() {
		$this->hide_items_per_page_selector();
		$this->hide_title();
		$this->hide_count();
		$this->hide_pagination();
		$this->hide_sorting();

		$this->show_headers();
		return $this;
	}

	/**
	 * @return Force_List
	 */
	public function preset_data_only() {
		$this->hide_items_per_page_selector();
		$this->hide_title();
		$this->hide_count();
		$this->hide_pagination();
		$this->hide_sorting();
		$this->hide_headers();
		return $this;
	}

} // End Force_List
