<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Jelly_Form
 * User: legion
 * Date: 14.11.14
 * Time: 16:10
 */
class Jelly_Form extends Force_Form_Core {

	protected $_model = null;
	protected $_prevent_apply = array();
	protected $_prevent_apply_all = false;
	protected $_controls_updated = false;

	/*
	 * auto apply statuses
	 *
	 * Предназначены для того чтобы методы обработки могли быть выполнены только один раз за одну итерацию.
	 * К таким методам относятся:
	 * apply_before_save()
	 * save()
	 * apply_after_save()
	 * redirect()
	 * auto()
	 */
	protected $_auto_done = false;
	protected $_apply_before_save_done = false;
	protected $_save_done = false;
	protected $_apply_after_save_done = false;

	protected $_auto_redirect = true;

	public function __construct(Jelly_Model &$model, array $controls = array(), $form_action = null, $form_method = 'post') {
		parent::__construct($controls, $form_action, $form_method);
		$this->_model = &$model;
	}

	public static function factory(Jelly_Model &$model, array $controls = array(), $form_action = null, $form_method = 'post') {
		return new self($model, $controls, $form_action, $form_method);
	}

	/*
	 * RENDER
	 */

	public function render() {
		$this->_update_controls();
		$this->auto();
		return parent::render();
	}

	protected function _update_controls() {
		if ($this->_controls_updated) {
			return false;
		}
		$generator = Jelly_Form_Generator::factory($this->_model);
		if ($this->_is_preset_for_admin) {
			$generator->preset_for_admin();
		}
		$controls = $this->get_controls();
		if (empty($controls)) {
			$controls = $generator->generate_controls();
			$this->add_control(Force_Form_Section::factory(null, $controls));
		} else {
			$this->_update_container_controls($generator, $controls);
		}
		return true;
	}

	protected function _update_container_controls(Jelly_Form_Generator &$generator, &$controls, $container = null) {
		foreach ($controls as $control_id => $control) {
			if ($control instanceof Force_Form_Container) {
				$container_controls = $control->get_controls();
				$this->_update_container_controls($generator, $container_controls, $control);
			} else {
				$control_updated = $generator->update_control($control);
				if (is_object($control_updated) && $control_updated instanceof Force_Form_Control) {
					if ($container instanceof Force_Form_Container) {
						$container->add_control($control_updated, $control_id);
					} else {
						$this->add_control($control_updated, $control_id);
					}
				}
			}
		}
	}

	/*
	 * GET MODEL DATA
	 */

	public function model() {
		return $this->_model;
	}

	public function model_name() {
		if ($this->_model instanceof Jelly_Model) {
			return $this->_model
				->meta()
				->model();
		}
		return null;
	}

	/*
	 * APPLY
	 */
	public function no_auto() {
		$this->_auto_done = true;
		return $this;
	}

	public function no_auto_redirect() {
		$this->_auto_redirect = false;
		return $this;
	}

	public function auto($index_path = null, $edit_path = null) {
		if ($this->_auto_done) {
			return $this;
		}
		if ($this->is_ready_to_apply()) {
			$this->apply_before_save();
			$this->save();
			$this->apply_after_save();
			if ($this->_auto_redirect) {
				$this->redirect($index_path, $edit_path);
			}
		}
		$this->_auto_done = true;
		return $this;
	}

	public function prevent_apply($prevent_fields = TRUE) {
		if (is_bool($prevent_fields)) {
			$this->_prevent_apply_all = $prevent_fields;
		} elseif (is_array($prevent_fields)) {
			foreach ($prevent_fields as $field) {
				$this->prevent_apply((string)$field);
			}
		} else {
			$this->_prevent_apply[(string)$prevent_fields] = null;
		}
		return $this;
	}

	public function update_model_data() {
		if (!$this->is_ready_to_apply()) {
			return $this;
		}
		$fields = $this->_model->meta()->fields();

		foreach ($fields as $field) {
			$name = $field->name;
			$value = $this->get_value($name);
			if (empty($value)) {
				continue;
			}
			if ($field instanceof Jelly_Field_Boolean) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Text) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_ManyToMany) {
				$this->_model->set(array($name => NULL));
				if (is_array($value) && !empty($value)) {
					$this->_model->set(array($name => $value));
				}
			} else if ($field instanceof Jelly_Field_HasMany) {
				$this->_model->set(array($name => NULL));
				if (is_array($value) && !empty($value)) {
					$this->_model->set(array($name => $value));
				}
			} else if ($field instanceof Jelly_Field_HasOne) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Email) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Password) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Enum) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Slug) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Image) {

			} else if ($field instanceof Jelly_Field_File) {

			} else if ($field instanceof Jelly_Field_Serialized) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Integer) {
				$this->_model->{$name} = (integer)$value;
			} else if ($field instanceof Jelly_Field_Float) {
				$this->_model->{$name} = $value;
			} else if ($field instanceof Jelly_Field_Timestamp) {
				$this->_model->{$name} = strtotime($value);
			} else { // Jelly_Field_String
				$this->_model->{$name} = $value;
			}
		}
		return $this;
	}

	/*
	 * APPLY BEFORE SAVE
	 */

	public function apply_before_save() {
		if ($this->_apply_before_save_done) {
			return $this;
		}

		$this->_update_controls();

		$this->_apply_before_save($this->get_controls());

		$this->_apply_before_save_done = true;
		return $this;
	}

	/**
	 * Рекурсивное выполнение apply_before_save() всех указанных компонентов
	 *
	 * @param $controls
	 */
	protected function _apply_before_save($controls) {
		if ($this->_prevent_apply_all) {
			return false;
		}
		foreach ($controls as $control_name => $control) {
			if ($control instanceof Force_Form_Container) {
				$this->_apply_before_save($control->get_controls());
				continue;
			}

			if ($control instanceof Force_Form_Control) {
				$name = $control->get_name();
			} else {
				continue;
			}

			if (empty($name)) {
				continue;
			}

			if (array_key_exists($name, $this->_prevent_apply)) {
				continue;
			}

			$control->apply_before_save($this, $this->_model);
		}
		return true;
	}

	/*
	 * SAVE
	 */
	public function no_auto_save() {
		$this->_save_done = true;
		return $this;
	}

	public function save() {
		if (!$this->_apply_before_save_done) {
			return $this;
		}
		if (!$this->_save_done) {
			$this->_save_done = $this->_model->saved();
		}
		if ($this->_save_done) {
			return $this;
		}
		if (Helper_Error::no_errors()) {
			try {
				$this->_model->save();
			} catch (Jelly_Validation_Exception $e) {
				Log::jelly_validation_exception($e, __CLASS__, __FUNCTION__);
				Helper_Error::add_from_jelly($this->_model, $e->errors());
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
				if (Kohana::$environment == Kohana::DEVELOPMENT) {
					Helper_Error::add($e->getMessage());
				} else {
					Helper_Error::add(__('db.error.save'));
				}
			}
		}
		$this->_save_done = true;
		return $this;
	}

	/*
	 * APPLY AFTER SAVE
	 */

	public function apply_after_save() {
		if (!$this->_apply_before_save_done) {
			return $this;
		}
		if ($this->_apply_after_save_done) {
			return $this;
		}
		if (!$this->_model->saved()) {
			return $this;
		}

		$this->_apply_after_save($this->get_controls());

		$this->_apply_after_save_done = true;
		return $this;
	}

	/**
	 * Рекурсивное выполнение apply_after_save() всех указанных компонентов
	 *
	 * @param $controls
	 */
	protected function _apply_after_save($controls) {
		if ($this->_prevent_apply_all) {
			return false;
		}

		$model_must_be_saved = false;

		foreach ($controls as $control_name => $control) {

			if ($control instanceof Force_Form_Container) {
				$this->_apply_after_save($control->get_controls());
				continue;
			}

			if ($control instanceof Force_Form_Control) {
				$name = $control->get_name();
			} else {
				continue;
			}

			if (empty($name)) {
				continue;
			}

			if (array_key_exists($name, $this->_prevent_apply)) {
				continue;
			}

			$result = $control->apply_after_save($this, $this->_model);

			if ($result) {
				$model_must_be_saved = true;
			}
		}

		if ($model_must_be_saved) {
			try {
				$this->_model->save();
			} catch (Jelly_Validation_Exception $e) {
				Helper_Error::add_from_jelly($this->_model, $e->errors());
			}
		}
		return true;
	}

	/*
	 * HELPERS
	 */

	public function redirect($index_path = null, $edit_path = null) {
		if (Helper_Error::has_errors()) {
			return false;
		}

		$save_and_continue = (boolean)$this->get_value('save_and_continue', false);

		if ($save_and_continue) {
			if (empty($edit_path)) {
				if (Request::current()->action() == 'add') {
					$edit_path = Force_URL::current()
						->action('edit')
						->route_param('id', $this->_model->id)
						->get_url();
				} else {
					$edit_path = Request::current()->uri();
				}
			}
			Request::current()
				->redirect($edit_path);
		} else {
			if (empty($index_path)) {
				$index_path = Force_URL::current_clean()
					->action('index')
					->get_url();
			}
			Force_URL::back_to_index($index_path, $this->_model->id);
		}
		return true;
	}

} // End Jelly_Form
