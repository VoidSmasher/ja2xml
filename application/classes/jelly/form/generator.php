<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Jelly_Form_Generator
 * User: legion
 * Date: 30.07.14
 * Time: 21:05
 */
class Jelly_Form_Generator {

	protected $_model = null;
	protected $_controls = array();
	protected $_exclude_fields = array();
	protected $_include_fields = array();
	protected $_include_fields_only = true;
	protected $_is_preset_for_admin = false;

	protected $_fields_not_editable_by_default = array(
		'created_at' => 'created_at',
		'updated_at' => 'updated_at',
		'deleted_at' => 'deleted_at',
	);

	function __construct(&$model, array $include_fields = array()) {
		$error_message = 'Object type of Jelly_Model required';
		if (!is_object($model)) {
			throw new Exception($error_message);
		}
		if (!is_a($model, 'Jelly_Model')) {
			throw new Exception($error_message);
		}
		$this->_model = &$model;
		$this->include_fields($include_fields);
	}

	public static function factory(&$model, array $include_fields = array()) {
		return new self($model, $include_fields);
	}

	/*
	 * INCLUDE FIELDS
	 *
	 * В include_fields можно указать не только поле, но и сразу передать объект, который нужен для этого поля,
	 * объект будет дополнен данными из модели.
	 */

	public function include_field($field, $include_fields_only = true) {
		if (Force_Form_Controls::check_control($field)) {
			$this->_include_fields[$field->get_name()] = $field;
		} elseif (is_string($field)) {
			$this->_include_fields[$field] = null;
		}
		$this->_include_fields_only = $include_fields_only;
		return $this;
	}

	public function include_fields(array $fields, $include_fields_only = true) {
		foreach ($fields as $field) {
			$this->include_field($field);
		}
		$this->_include_fields_only = $include_fields_only;
		return $this;
	}

	public function include_fields_only($boolean_value) {
		$this->_include_fields_only = (boolean)$boolean_value;
		return $this;
	}

	/*
	 * EXCLUDE FIELDS
	 */

	public function exclude_field($field) {
		$this->_exclude_fields[(string)$field] = null;
		return $this;
	}

	public function exclude_fields(array $fields) {
		foreach ($fields as $field) {
			$this->exclude_field($field);
		}
		return $this;
	}

	/*
	 * SET
	 */

	public function preset_for_admin($boolean_value = true) {
		$this->_is_preset_for_admin = (boolean)$boolean_value;
		return $this;
	}

	/*
	 * GENERATE
	 */

	protected function _check_editable_fields($field) {
		if ($field instanceof Jelly_Field) {
			if (array_key_exists($field->name, $this->_fields_not_editable_by_default) && is_null($field->editable)) {
				return false;
			}
		}
		return true;
	}

	protected function _is_read_only($field) {
		if ($field instanceof Jelly_Field && $field->editable === false) {
			$value = $this->_model->{$field->name};
			if ($field instanceof Jelly_Field_Boolean) {
				if ($value) {
					$value = __('common.yes');
				} else {
					$value = __('common.no');
				}
			}
			$control = Force_Form_Show_Value::factory($field->name, $field->label, $value);
			return $control;
		} else {
			return false;
		}
	}

	public function update_control($control) {
		if (is_string($control) && !empty($control)) {
			return $this->generate_control($control);
		} elseif ($control instanceof Force_Form_Control) {
			$field_name = $control->get_name();
			$field = $this->_model->meta()->field($field_name);
			if (empty($field)) {
				return $control;
			}

			$label = $control->get_label();
			if (empty($label)) {
				$control->label($field->label);
			}

			$value = $control->get_value();

			if ($field instanceof Jelly_Field_Boolean) {
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else if ($field instanceof Jelly_Field_Text) {
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else if ($field instanceof Jelly_Field_ManyToMany) {

			} else if ($field instanceof Jelly_Field_HasMany) {

			} else if ($field instanceof Jelly_Field_HasOne) {

			} else if ($field instanceof Jelly_Field_Email) {
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else if ($field instanceof Jelly_Field_Password) {
				// password must be empty by default
			} else if ($field instanceof Jelly_Field_Enum) {
				if ($control instanceof Force_Form_Select) {
					$options = $control->get_options();
					if (empty($options)) {
						$control->add_options($field->choices);
					}
				}
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else if ($field instanceof Jelly_Field_Slug) {
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else if ($field instanceof Jelly_Field_Image) {

			} else if ($field instanceof Jelly_Field_File) {

			} else if ($field instanceof Jelly_Field_Serialized) {

			} else if ($field instanceof Jelly_Field_Timestamp) {
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else if (self::_is_field_in($field, array(
				'Jelly_Field_Integer',
				'Jelly_Field_Float',
			))
			) {
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			} else { // Jelly_Field_String
				if (is_null($value)) {
					$control->value($this->_model->{$field->name});
				}
			}

			/*
			 * задание image_type для Force_Form_Image
			 * @todo разобрать работу Jelly_Field_Image
			 */
			if ($control instanceof Force_Form_Image) {
				$image_type = $control->get_image_type();
				if (empty($image_type)) {
					$control->image_type($this->_model->meta()->model() . '_' . $field_name);
				}
			}

			if ($this->_is_read_only($field)) {
				$control->value($this->_model->{$field->name});
				return $control;
			}

			/*
			 * description и required не для read_only
			 */

			$description = $control->get_description();
			if (empty($description) && !empty($field->description)) {
				$control->description($field->description);
			}

			if (Helper_Jelly::get_rule_value_from_jelly_model($this->_model, $field_name, 'not_empty', false)) {
				$control->required();
			}

		}

		return $control;
	}

	public function generate_control($field_name) {
		if (is_string($field_name) && !empty($field_name)) {
			$control = $field_name;
			$field = $this->_model->meta()->field($field_name);
			if (empty($field)) {
				return $control;
			}
		} else {
			$field = $field_name;
			$control = $field->name;
		}

		if (!$this->_check_editable_fields($field)) {
			return false;
		}

		if ($read_only = $this->_is_read_only($field)) {
			return $read_only;
		}

		if ($field instanceof Jelly_Field_Boolean) {
			$control = Force_Form_Checkbox::factory($field->name, $field->label, $this->_model->{$field->name});
		} else if ($field instanceof Jelly_Field_Text) {
			$control = Force_Form_Textarea::factory($field->name, $field->label, $this->_model->{$field->name});
		} else if ($field instanceof Jelly_Field_ManyToMany) {
			$_selected = $this->_model->{$field->name};
			$_available = array();
			if ($_selected instanceof Jelly_Collection) {
				$_model_name = $_selected->meta()->model();
				$_core = 'Core_' . ucfirst($_model_name);
				if (class_exists($_core) && ($_core instanceof Force_Core_Common)) {
					$_core = $_core::factory();
				} else {
					$_core = null;
				}
				$_primary_key = $_selected->meta()->primary_key();
				$_name_field = $_selected->meta()->field('name');
				if (empty($_name_field)) {
					$_name_field = $_selected->meta()->field('title');
				}
				if (empty($_name_field)) {
					$_name_field = $_primary_key;
				} else {
					$_name_field = $_name_field->name;
				}
				if (is_object($_core) && $_core instanceof Core_Common) {
					if ($this->_is_preset_for_admin) {
						$_core->preset_for_admin();
					}
					$_available = $_core->get_list()->as_array($_primary_key, (string)$_name_field);
				} else {
					$_available = Jelly::query($_model_name)->select_all()
						->as_array($_primary_key, (string)$_name_field);
				}
				$_selected = $_selected->as_array($_primary_key, (string)$_name_field);
			} else {
				$_selected = array();
			}
			$control = Force_Form_ManyToMany::factory($field->name, $field->label, $_available, $_selected);
		} else if ($field instanceof Jelly_Field_HasMany) {
//			@TODO available? selected?
//			$control = Force_Form_Select::factory($field->name, $field->label);
		} else if ($field instanceof Jelly_Field_HasOne) {

		} else if ($field instanceof Jelly_Field_Email) {
			$control = Force_Form_Input::factory($field->name, $field->label, $this->_model->{$field->name});
		} else if ($field instanceof Jelly_Field_Password) {
			$control = Force_Form_Input::factory($field->name, $field->label)->password();
		} else if ($field instanceof Jelly_Field_Enum) {
			$control = Force_Form_Select::factory($field->name, $field->label, $field->choices, $this->_model->{$field->name});
		} else if ($field instanceof Jelly_Field_Slug) {
			$control = Force_Form_Alias::factory($field->name, $field->label, $this->_model->{$field->name});
		} else if ($field instanceof Jelly_Field_Image) {

		} else if ($field instanceof Jelly_Field_File) {

		} else if ($field instanceof Jelly_Field_Serialized) {

		} else if ($field instanceof Jelly_Field_Timestamp) {
			$control = Force_Form_Date::factory($field->name, $field->label, $this->_model->{$field->name})
				->pick_seconds();
		} else if ($field instanceof Jelly_Field_Float) {
			$control = Force_Form_Float::factory($field->name, $field->label, $this->_model->{$field->name});
		} else if ($field instanceof Jelly_Field_Integer) {
			$control = Force_Form_Input::factory($field->name, $field->label, $this->_model->{$field->name});
		} else { // Jelly_Field_String
			$control = Force_Form_Input::factory($field->name, $field->label, $this->_model->{$field->name});
		}

		if (Helper_Jelly::get_rule_value_from_jelly_model($this->_model, $field->name, 'not_empty', false)) {
			$control->required();
		}

		if (!empty($field->description)) {
			$control->description($field->description);
		}

		return $control;
	}

	public function generate_controls() {
		$fields = $this->_model->meta()->fields();

		/*
		 * Убираем вычтеные поля из include_fields.
		 */
		foreach ($this->_exclude_fields as $field => $value) {
			if (array_key_exists($field, $this->_include_fields)) {
				unset($this->_include_fields[$field]);
			}
		}
		$include_fields_only = ($this->_include_fields_only && !empty($this->_include_fields));

		foreach ($fields as $field) {
			if ($include_fields_only && !array_key_exists($field->name, $this->_include_fields)) {
				continue;
			}
			/*
			 * Пропускаем вычтенные поля
			 */
			if (array_key_exists($field->name, $this->_exclude_fields)) {
				continue;
			}
			/*
			 * Пропускаем поле ID, за исключением случаев, когда оно явно было указано, как необходимое.
			 */
			if (!$include_fields_only) {
				if ($field instanceof Jelly_Field_Primary && !array_key_exists($field->name, $this->_include_fields)) {
					continue;
				}
			}
			/*
			 * Автогенерация полей
			 */
			$control = $this->generate_control($field);
			if (is_object($control)) {
				$this->_controls[] = $control;
			}
		}
		return $this->_controls;
	}

	protected static function _is_field_in(&$field, array $class_names) {
		if (!is_object($field)) {
			return false;
		}
		foreach ($class_names as $_class_name) {
			if (is_a($field, $_class_name)) {
				return true;
			}
		}
		return false;
	}

} // End Jelly_Form_Generator
