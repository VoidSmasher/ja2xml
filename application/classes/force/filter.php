<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Filter
 * User: legion
 * Date: 15.07.14
 * Time: 0:49
 */
class Force_Filter extends Force_Filter_Controls {

	protected $_view = 'force/filter/default';
	protected $_fields = null;
	protected $_hidden = array();
	protected $_cancel_uri = null;
	protected $_form_method = 'get';
	protected $_form_action = null;
	protected $_always_visible = false;

	const VISIBILITY_VAR = 'force.filter.is_visible';

	const ORDER_BY_PARAM = 'ob';
	const ORDER_DIRECTION_PARAM = 'od';

	public function __construct(array $controls = array()) {
		$this->add_controls($controls);
	}

	public static function factory(array $controls = array()) {
		return new self($controls);
	}

	public function __toString() {
		return $this->render();
	}

	public function render() {
		if (!empty($this->_cancel_uri)) {
			$cancel_uri = $this->_cancel_uri;
		} else {
			$cancel_uri = Force_URL::current()->clean_query()->get_url();
		}

		Helper_Assets::add_scripts(array(
			'assets/common/js/list.filter.js',
		));
		Helper_Assets::add_js_vars(array(
			'filter' => array(
				'url' => Force_URL::current_clean()
					->action('filter_status')
					->data_json()
					->get_url(),
				'title_show' => __('admin.filter.show'),
				'title_hide' => __('admin.filter.hide'),
			),
		));

		$filter_conditions = array();
		$search_values = array();
		$is_searching = false;

		foreach ($this->_controls as $control) {
			if (!($control instanceof Force_Filter_Control)) {
				continue;
			}

			$this->_update_control_form_method($control);

			$label = $control->get_label();
			$name = $control->get_name();
			$value = $control->get_value();

			if (is_array($this->_fields)) {
				if (empty($label) || ($label == $name)) {
					if (array_key_exists($name, $this->_fields)) {
						$label = $this->_fields[$name]->label;
					}
					$control->label($label);
				}
			}

			$empty = false;

			if (is_null($value)) {
				$empty = true;
			} elseif (is_string($value)) {
				$value = trim($value);
				$empty = !(strlen($value));
			} elseif (is_array($value)) {
				$empty = empty($value);
			}

			if ($empty) {
				$search_values[$name] = NULL;
				continue;
			} else {
				$is_searching = true;
				$search_values[$name] = $value;
			}

			if ($control instanceof Force_Filter_Select) {
				if (is_array($value)) {
					foreach ($value as $_key => $_value) {
						$value[$_key] = self::_replace_value($_value, $control->get_value_rules());
						if (!self::_check_value_rules($value[$_key], $control->get_value_rules())) {
							unset($value[$_key]);
						}
					}
					if (empty($value)) {
						continue;
					}
				} else {
					$value = self::_replace_value($value, $control->get_value_rules());
					if (!self::_check_value_rules($value, $control->get_value_rules())) {
						continue;
					}
				}
			}
			$value = $search_values[$name];

			/*
			 * Всё верно, проверка только на пустую строку,
			 * и именно точная
			 * проверку на empty() здесь использовать нельзя
			 */
			if (is_array($value)) {
				foreach ($value as $_key => $_value) {
					if ($_value === '') {
						unset($value[$_key]);
					}
				}
				if (empty($value)) {
					continue;
				}
			} else {
				if ($value === '') {
					continue;
				}
			}

			/*
			 * Выстраиваем строку поиска,
			 * и да, как ни прискорбно, проверка только на пустую строку,
			 * !empty() здесь использовать нельзя
			 */
			if ($control instanceof Force_Filter_Select) {
				$condition_value = $control->get_value_label($value);
			} else {
				if (is_array($value)) {
					$condition_value = implode(', ', $value);
				} else {
					$condition_value = $value;
				}
			}
			if (!empty($label)) {
				$filter_conditions[$label] = $condition_value;
			}
		}

		$hidden_body = '';
		foreach ($this->_hidden as $hidden_key => $hidden_value) {
			if (is_array($hidden_value)) {
				foreach ($hidden_value as $_value) {
					$hidden_body .= Form::hidden($hidden_key . '[]', $_value);
				}
			} else {
				$hidden_body .= Form::hidden($hidden_key, $hidden_value);
			}
		}

		$filter_body = '';
		foreach ($this->_controls as $control) {
			if ($control instanceof Force_Filter_Control) {
				$filter_body .= $control->render();
			}
		}

		$filter_is_visible = (boolean)Session::instance()->get(Force_Filter::VISIBILITY_VAR, true);

		if (is_null($this->_form_method)) {
			$form_method = 'get';
		} else {
			$form_method = $this->_form_method;
		}

		$form_action = $this->_form_action;

		if (empty($form_action)) {
			$form_action = Force_URL::current()
				->route_param('page', 0)
				->get_url();
		}

		$this->attribute('method', $form_method);
		$this->attribute('action', $form_action);
		$this->attribute('class', 'well form-search form-filter');

		if (!empty($filter_body)) {
			return View::factory($this->_view)
				->set('filter_conditions_divider', '; ')
				->set('key_value_divider', ': ')
				->bind('filter_conditions', $filter_conditions)
				->bind('filter_is_visible', $filter_is_visible)
				->bind('filter_body', $filter_body)
				->bind('hidden_body', $hidden_body)
				->bind('cancel_uri', $cancel_uri)
				->bind('search_values', $search_values)
				->bind('form_action', $form_action)
				->set('attributes', $this->get_attributes())
				->bind('always_visible', $this->_always_visible)
				->render();
		} else {
			return '';
		}
	}

	public function apply(Jelly_Builder &$builder) {

		if (empty($this->_controls)) {
			return $this;
		}

		$this->_fields = $builder->meta()->fields();

		foreach ($this->_controls as $control) {
			if (!($control instanceof Force_Filter_Control)) {
				continue;
			}

			$this->_update_control_form_method($control);

			$conditions = $control->get_conditions();
			$value = $control->get_value();

			if (empty($conditions)) {
				continue;
			}

			if (is_array($value) && count($value) == 1) {
				$value = array_shift($value);
			}

			if (is_null($value)) {
				continue;
			} else {
				if (is_array($value)) {
					foreach ($value as $_key => $_value) {
						$value[$_key] = Helper_Html::prepare_value_for_form($_value);
					}
				} else {
					$value = Helper_Html::prepare_value_for_form($value);
				}
			}

			if ($control instanceof Force_Filter_Select) {
				if (is_array($value)) {
					foreach ($value as $_key => $_value) {
						$value[$_key] = self::_replace_value($_value, $control->get_value_rules());
						if (!self::_check_value_rules($value[$_key], $control->get_value_rules())) {
							unset($value[$_key]);
						}
					}
					if (empty($value)) {
						continue;
					}
				} else {
					$value = self::_replace_value($value, $control->get_value_rules());
					if (!self::_check_value_rules($value, $control->get_value_rules())) {
						continue;
					}
				}
			}

			if ($control instanceof Force_Filter_Date_Range && !is_array($value) && !empty($value)) {
				$value = explode('-', $value);
				foreach ($value as $_key => $_value) {
					$_value = trim($_value);
					if (empty($_value) || $_key > 1) {
						unset($value[$_key]);
					} else {
						$value[$_key] = Force_Date::factory($_value)->format_sql();
					}
				}
				if (count($value) != 2) {
					$value = array_shift($value);
				}
			} else {
				/*
				 * Всё верно, проверка только на пустую строку,
				 * и именно точная
				 * проверку на empty() здесь использовать нельзя
				 */
				if (is_array($value)) {
					foreach ($value as $_key => $_value) {
						if ($_value === '') {
							unset($value[$_key]);
						}
					}
					if (empty($value)) {
						continue;
					}
				} else {
					if ($value === '') {
						continue;
					}
				}
			}

			if ($control instanceof Force_Filter_Date && !is_array($value)) {
				$value = Force_Date::factory($value)->format_sql();
			}

			foreach ($conditions as $condition) {
				if (array_key_exists('type', $condition)) {
					$sql_op = $condition['type'];
				} else {
					continue;
				}
				if (array_key_exists('value', $condition)) {
					$select_value = $condition['value'];
				} else {
					$select_value = $value;
				}
				switch ($sql_op) {
					case 'where':
					case 'or_where':
					case 'and_where':
						if ($control instanceof Force_Filter_Date_Range && is_array($select_value) && (count($select_value) == 2)) {
							foreach ($select_value as $_key => $_value) {
								if (array_key_exists('db_expr', $condition) && $condition['db_expr'] instanceof Database_Expression) {
									$select_value[$_key] = $condition['db_expr']->param(':value', $_value)->compile();
								}
							}
							switch ($condition['op']) {
								case '>':
								case '>=':
									$builder->{$sql_op . '_open'}();
									$builder->where($condition['column'], $condition['op'], $select_value[0]);
									break;
								case '<':
								case '<=':
									$builder->where($condition['column'], $condition['op'], $select_value[1]);
									$builder->{$sql_op . '_close'}();
									break;
								default:
									$builder->{$sql_op . '_open'}();
									$builder->where($condition['column'], '>=', $select_value[0]);
									$builder->where($condition['column'], '<=', $select_value[1]);
									$builder->{$sql_op . '_close'}();
									break;
							}
						} else {
							if (is_array($select_value)) {
								$builder->{$sql_op . '_open'}();
								foreach ($select_value as $_value) {
									if (array_key_exists('db_expr', $condition) && $condition['db_expr'] instanceof Database_Expression) {
										$_value = $condition['db_expr']->param(':value', $_value)->compile();
									}
									if (strtolower($condition['op']) == 'like') {
										$_value = Helper_Html::prepare_value_for_sql($_value);
									}
									$builder->or_where($condition['column'], $condition['op'], $_value);
								}
								$builder->{$sql_op . '_close'}();
							} else {
								if (array_key_exists('db_expr', $condition) && $condition['db_expr'] instanceof Database_Expression) {
									$select_value = $condition['db_expr']->param(':value', $select_value)->compile();
								}
								if (strtolower($condition['op']) == 'like') {
									$select_value = Helper_Html::prepare_value_for_sql($select_value);
								}
								$builder->{$sql_op}($condition['column'], $condition['op'], $select_value);
							}
						}
						break;
					case 'where_open':
					case 'and_where_open':
						$builder->and_where_open();
						break;
					case 'where_close':
					case 'and_where_close':
						$builder->and_where_close();
						break;
					case 'or_where_open':
						$builder->or_where_open();
						break;
					case 'or_where_close':
						$builder->or_where_close();
						break;
					case 'join':
						if (array_key_exists('join_type', $condition)) {
							$join_type = $condition['join_type'];
						} else {
							$join_type = null;
						}
						$builder->join($condition['table'], $join_type);
						if (array_key_exists('on', $condition)) {
							$builder->on($condition['on']['c1'], $condition['on']['op'], $condition['on']['c2']);
						}
						break;
					case 'group_by':
						$builder->group_by($condition['columns']);
						break;
					case 'where_numeric_array':
						$this->_where_array($builder, true, $condition['column'], $condition['op'], $select_value);
						break;
					case 'where_array':
						$this->_where_array($builder, false, $condition['column'], $condition['op'], $select_value);
						break;
				}
			}
		}

		$this->_use_order_by();

		return $this;
	}

	protected function _where_array(Jelly_Builder &$builder, $is_numeric, $column, $op, $value) {
		$filtered = false;
		if (strpos($value, ',') !== false) {
			$values = explode(',', $value);
			$list = array();
			foreach ($values as $_value) {
				$_value = trim($_value);
				if ($is_numeric) {
					if (is_numeric($_value)) {
						$list[] = $_value;
					}
				} else {
					$list[] = $_value;
				}
			}
			if (!empty($list)) {
				switch ($op) {
					case '!=':
					case '<>':
					case 'not in':
						$list_op = 'not in';
						break;
					default:
						$list_op = 'in';
				}
				$builder->where($column, $list_op, $list);
				$filtered = true;
			}
		}
		if (!$filtered) {
			switch ($op) {
				case '!=':
				case '<>':
				case 'not in':
					$single_op = '!=';
					break;
				default:
					$single_op = '=';
			}
			$builder->where($column, $single_op, $value);
		}
		return $this;
	}

	protected function _use_order_by() {
		/*
		 * Здесь нет никаких изменений в builder, они вносятся в Force_list apply_jelly_builder()
		 * Задача фильтра лишь сохранить параметры сортировки.
		 */
		$order_by = Arr::get($_REQUEST, self::ORDER_BY_PARAM);
		$order_direction = (boolean)Arr::get($_REQUEST, self::ORDER_DIRECTION_PARAM, true);

		if (empty($order_by)) {
			return $this;
		}

		$this->add_hidden(self::ORDER_BY_PARAM, $order_by);
		$this->add_hidden(self::ORDER_DIRECTION_PARAM, (integer)$order_direction);

		$this->_cancel_uri = Force_URL::current_clean()
			->query_param(Force_Filter::ORDER_BY_PARAM, $order_by)
			->query_param(Force_Filter::ORDER_DIRECTION_PARAM, (integer)$order_direction)
			->get_url();

		return $this;
	}

	/*
	 * VALUE RULES
	 */

	protected static function _check_value_rules($value, array $value_rules) {
		$rule_check_result = true;
		foreach ($value_rules as $rule_key => $rule_value) {
			$rule_check_result = false;
			switch ($rule_key) {
				case '>':
					if ($value > $rule_value) {
						$rule_check_result = true;
					}
					break;
				case '<':
					if ($value < $rule_value) {
						$rule_check_result = true;
					}
					break;
				case '>=':
					if ($value >= $rule_value) {
						$rule_check_result = true;
					}
					break;
				case '<=':
					if ($value <= $rule_value) {
						$rule_check_result = true;
					}
					break;
				case '==':
					if (is_null($value)) {
						if (is_null($rule_value)) {
							$rule_check_result = true;
						}
					} else {
						if ((string)$value == (string)$rule_value) {
							$rule_check_result = true;
						}
					}
					break;
				case '!=':
					if (is_null($value)) {
						if (!is_null($rule_value)) {
							$rule_check_result = true;
						}
					} else {
						if ((string)$value != (string)$rule_value) {
							$rule_check_result = true;
						}
					}
					break;
				/*
				 * Проверки типа is_integer или is_string здесь не имеют смысла,
				 * так как $value всегда string и приводить его, например, к integer нельзя.
				 * Поэтому единственно верная проверка это is_numeric т.е.
				 * не является ли наша строка числом?
				 * Эта проверка хорошо подходит для списков, типа:
				 * 'default' => '---'
				 * '0' => 'элемент списка 0'
				 * '1' => 'элемент списка 1'
				 */
				case 'is_numeric':
					if ($rule_value) {
						if (is_numeric($value)) {
							$rule_check_result = true;
						}
					} else {
						if (!is_numeric($value)) {
							$rule_check_result = true;
						}
					}
					break;
				/*
				 * Не стоит добавлять сюда проверку типа in_array - результаты просто феерические,
				 * уже проверял, как так получается в душе незнаю.
				 */
				default:
					$rule_check_result = true;
			}
			if (!$rule_check_result) {
				break;
			}
		}
		return $rule_check_result;
	}

	protected static function _replace_value($value, array $value_rules) {
		if (!empty($value_rules) && array_key_exists('=', $value_rules)) {
			$replacement_rules = $value_rules['='];
			if (is_array($replacement_rules) && array_key_exists($value, $replacement_rules)) {
				$value = $replacement_rules[$value];
			}
		}
		return $value;
	}

	/*
	 * VISIBLE STATUS
	 */

	public static function save_visible_status() {
		if (Form::is_post()) {
			$is_visible = Arr::get($_POST, 'is_visible');
			Session::instance()->set(self::VISIBILITY_VAR, (boolean)json_decode($is_visible));
		}
		return true;
	}

	public function always_visible() {
		$this->_always_visible = true;
		return $this;
	}

	/*
	 * FORM METHOD
	 */

	public function form_method_post() {
		$this->_form_method = 'post';
		return $this;
	}

	public function form_method_get() {
		$this->_form_method = 'get';
		return $this;
	}

	protected function _get_data_array() {
		switch ($this->_form_method) {
			case 'post':
				return $_POST;
			case 'get':
				return $_GET;
			default:
				return $_REQUEST;
		}
	}

	protected function _update_control_form_method(&$control) {
		if ($control instanceof Force_Filter_Control) {
			switch ($this->_form_method) {
				case 'post':
					$control->form_method_post();
					break;
				case 'get':
				default:
					$control->form_method_get();
			}
		}
	}

	/*
	 * HIDDEN
	 */

	public function add_hidden($key, $value) {
		$this->_hidden[$key] = $value;
		return $this;
	}

	public function remove_hidden($key) {
		if (array_key_exists($key, $this->_hidden)) {
			unset($this->_hidden[$key]);
		}
		return $this;
	}

	/*
	 * VALUE for custom implementations
	 */

	public function get_value($name, $default = null) {
		return Arr::get($this->_get_data_array(), $name, $default);
	}

} // End Force_Filter
