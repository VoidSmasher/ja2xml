<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Core_Common
 * User: legion
 * Date: 06.06.16
 * Time: 22:12
 */
abstract class Force_Core_Common {

	const UNDEFINED = '__id_is_not_set__';

	protected $_on_error = null; //do nothing
	protected $_has_error = false;

	protected $model_name = null;
	protected $model = null;

	protected $field_deleted_at = 'deleted_at';
	protected $field_is_published = 'is_published';
	protected $field_alias = 'alias';

	protected $builder;
	protected $table;
	protected $exclude = null;

	private $_id = null;
	private $_id_set = false;
	private $_id_equal = true;

	private $_alias = null;
	private $_alias_set = false;
	private $_alias_equal = true;

	/*
	 * Настройки по умолчанию для public-страниц
	 * Здесь ничего не менять!
	 * 
	 * Указанные здесь настройки являются трёхпозиционными переключателями
	 * Пример для show_deleted
	 * true - показаать только удалённые
	 * false - все, кроме удалённых
	 * null - все записи
	 */
	private $_show_deleted = false;
	private $_show_published = true;

	/*
	 * Эти значения всегда должны быть здесь в false!
	 * Их нельзя менять ни в коем случае!
	 */
	protected $can_use_deleted_at = false;
	protected $can_use_is_published = false;
	protected $can_use_alias = false;

	public function __construct($id = self::UNDEFINED) {
		if (empty($this->model_name)) {
			throw new Exception('Core Error: model_name must be specified');
		}

		$this->builder = Jelly::query($this->model_name);

		if (!($this->builder->meta() instanceof Jelly_Meta)) {
			throw new Exception('Core Error: cannot load meta, incorrect model_name = ' . $this->model_name);
		}

		$this->table = $this->builder->meta()->table();

		if ($id instanceof Jelly_Model) {
			if ($id->meta()->model() == $this->model_name) {
				$this->model = &$id;
				$this->id($this->model->id);
			}
		} elseif ($id !== self::UNDEFINED) {
			$this->id($id);
		}

		$fields = $this->builder->meta()->fields();
		$this->can_use_deleted_at = (array_key_exists($this->field_deleted_at, $fields));
		$this->can_use_is_published = (array_key_exists($this->field_is_published, $fields));
		$this->can_use_alias = (array_key_exists($this->field_alias, $fields));

		$this->_parse_fields($fields);
	}

	abstract protected function _parse_fields($fields);

	/*
	 * ERROR
	 */

	final public function on_error_do_nothing() {
		$this->_on_error = null;
		return $this;
	}

	final public function on_error_throw_403() {
		$this->_on_error = 'HTTP_Exception_403';
		return $this;
	}

	final public function on_error_throw_404() {
		$this->_on_error = 'HTTP_Exception_404';
		return $this;
	}

	final public function on_error_throw_500() {
		$this->_on_error = 'HTTP_Exception_500';
		return $this;
	}

	final public function throw_error() {
		$this->_has_error = true;
		if (!empty($this->_on_error)) {
			throw new $this->_on_error;
		}
		return false;
	}

	/*
	 * SET
	 */

	final public function exclude($id_or_array_of_ids) {
		if (is_array($id_or_array_of_ids) && empty($id_or_array_of_ids)) {
			return $this;
		}
		/**
		 * Если значение будет NULL действие выполнено не будет
		 */
		$this->exclude = $id_or_array_of_ids;
		return $this;
	}

	final public function where_id($field, $value, $exclude = false) {
		if (is_array($value)) {
			$op = ($exclude) ? 'NOT IN' : 'IN';
		} else {
			$op = ($exclude) ? '!=' : '=';
		}
		$this->builder->where($field, $op, $value);
		return $this;
	}

	/*
	 * GET
	 */

	final public function create() {
		$this->_id = null;
		$this->_id_set = false;
		$this->_id_equal = true;
		$this->_alias = null;
		$this->_alias_set = false;
		$this->_alias_equal = true;
		return $this->model = Jelly::factory($this->model_name);
	}

	abstract protected function _before_get();

	final private function _apply_before_get($id_only = false, $alias_only = false) {
		$this->_before_get();

		$this->_check_is_deleted();
		$this->_check_is_published();

		if ($id_only) {
			$this->_set_id(true);
		} elseif ($alias_only) {
			$this->_set_alias(true);
		} else {
			$this->_set_id();
			$this->_set_alias();
		}

		Helper_Jelly::apply_exclude($this->builder, $this->exclude, $this->table);
	}

	/*
	 * GET ONE
	 */

	/**
	 * @return Jelly_Model
	 */
	final public function get_one() {
		return $this->_get_one();
	}

	/**
	 * @return Jelly_Model
	 */
	final public function get_one_by_id() {
		return $this->_get_one(true, false);
	}

	/**
	 * @return Jelly_Model
	 */
	final public function get_one_by_alias() {
		return $this->_get_one(false, true);
	}

	final private function _get_one($id_only = false, $alias_only = false) {
		$this->_apply_before_get($id_only, $alias_only);

		if ($this->_has_error) {
			return $this->create();
		}

		$this->builder->select_column($this->table . '.*');
		$model = $this->builder->limit(1)->select();

		/*
		 * Проверяем результат и обновляем локальные данные
		 */

		if (!$model->loaded()) {
			$this->throw_error();
		}
		return $model;
	}

	final public function get_list() {
		$this->_apply_before_get();
		$result = $this->builder->select_all();
		return $result;
	}

	/**
	 * @return Jelly_Builder
	 */
	final public function get_builder() {
		$this->_apply_before_get();
		return $this->builder;
	}

	final public function get_count() {
		$this->_apply_before_get();
		return $this->builder->count();
	}

	final public function is_exist() {
		if (!empty($this->_id)) {
			return (boolean)Jelly::query($this->model_name, $this->_id)->count();
		} else {
			$this->_apply_before_get();
			return (boolean)$this->builder->count();
		}
	}

	/*
	 * IS DELETED
	 */

	final private function _check_is_deleted() {
		if ($this->can_use_deleted_at && !is_null($this->_show_deleted)) {
			$this->builder->where($this->table . '.' . $this->field_deleted_at, $this->_show_deleted ? 'IS NOT' : 'IS', null);
		}
	}

	final public function is_deleted($value = true) {
		if (!is_null($value)) {
			$value = boolval($value);
		}
		$this->_show_deleted = $value;
		return $this;
	}

	final public function ignore_is_deleted() {
		$this->_show_deleted = null;
		return $this;
	}

	/*
	 * DELETE
	 */

	abstract protected function _can_be_deleted(Jelly_Model $model);

	final public function delete() {
		if ($this->model instanceof Jelly_Model) {
			$object = $this->model;
		} else {
			$object = $this->get_one();
		}

		return $this->_delete($object);
	}

	final private function _delete($object) {
		$result = false;
		if ($object instanceof Jelly_Model) {
			$result = $this->_delete_model($object);
		} elseif ($object instanceof Jelly_Collection) {
			$result = $this->_delete_collection($object);
		}
		return $result;
	}

	final private function _delete_collection(Jelly_Collection $collection) {
		$result = 0;
		foreach ($collection as $model) {
			if ($this->_delete_model($model)) {
				$result++;
			}
		}
		return (boolean)$result;
	}

	abstract protected function _before_delete_model(Jelly_Model $model);

	abstract protected function _after_delete_model(Jelly_Model $model);

	final private function _delete_model(Jelly_Model $model) {
		if (!$this->_can_be_deleted($model)) {
			return false;
		}

		if (!$model->loaded()) {
			return false;
		}

		$this->_before_delete_model($model);
		if ($this->can_use_deleted_at) {
			$after = $model;
		} else {
			$after = clone $model;
		}

		try {
			if (method_exists('remove_image', $model)) {
				$model->remove_image();
			}

			if ($this->can_use_deleted_at) {
				$model->{$this->field_deleted_at} = time();
				$model->save();
			} else {
				$model->delete();
			}

		} catch (Jelly_Validation_Exception $e) {
			Helper_Error::add_from_jelly($model, $e->errors());
		}

		$this->_after_delete_model($after);
		if (!$this->can_use_deleted_at) {
			unset($after);
		}

		if (Helper_Error::has_errors()) {
			return false;
		}

		if ($this->can_use_deleted_at) {
			$result = $model->saved();
		} else {
			$this->_show_deleted = false;
			$result = !$this->is_exist();
		}

		return $result;
	}

	/*
	 * RESTORE
	 */

	abstract protected function _can_be_restored(Jelly_Model $model);

	final public function restore() {
		if ($this->model instanceof Jelly_Model) {
			$model = &$this->model;
		} else {
			$model = $this->on_error_throw_404()->ignore_is_deleted()->get_one();
		}

		$result = $this->_can_be_restored($model);

		if ($result) {
			$result = $this->_restore_model($model);
		}

		return $result;
	}

	abstract protected function _before_restore_model(Jelly_Model $model);

	abstract protected function _after_restore_model(Jelly_Model $model);

	final private function _restore_model(Jelly_Model $model) {
		if (!$this->can_use_deleted_at) {
			return false;
		}

		$this->_before_restore_model($model);

		try {
			$model->{$this->field_deleted_at} = null;
			$model->save();
		} catch (Jelly_Validation_Exception $e) {
			Helper_Error::add_from_jelly($model, $e->errors());
		}

		$this->_after_restore_model($model);

		if (Helper_Error::has_errors()) {
			return false;
		}

		$result = $model->saved();

		return $result;
	}

	/*
	 * IS PUBLISHED
	 */

	final private function _check_is_published() {
		if ($this->can_use_is_published && !is_null($this->_show_published)) {
			$this->builder->where($this->table . '.' . $this->field_is_published, '=', $this->_show_published);
		}
	}

	final public function is_published($value = true) {
		if (!is_null($value)) {
			$value = boolval($value);
		}
		$this->_show_published = $value;
		return $this;
	}

	final public function ignore_is_published() {
		$this->_show_published = null;
		return $this;
	}

	final public function set_is_published($value = true) {
		if (!$this->can_use_is_published) {
			return false;
		}

		if ($this->model instanceof Jelly_Model) {
			$model = &$this->model;
		} else {
			$model = $this->is_published(!$value)->get_one();
		}

		if (!$model->loaded()) {
			return false;
		}

		try {
			$model->{$this->field_is_published} = boolval($value);
			$model->save();
		} catch (Jelly_Validation_Exception $e) {
			Helper_Error::add_from_jelly($model, $e->errors());
		}

		return $model->saved();
	}

	/*
	 * REQUEST
	 */

	final public function request_id($request_param = 'id', $default_value = null) {
		$param = Request::current()->param($request_param, $default_value);
		$this->id($param);

		return $this;
	}

	final public function request_alias($request_param = 'alias', $default_value = null) {
		$param = Request::current()->param($request_param, $default_value);
		$this->alias($param);

		return $this;
	}

	/*
	 * ID
	 */

	final public function id($value, $equal_to = true) {
		$this->_id = $value;
		$this->_id_set = true;
		$this->_id_equal = boolval($equal_to);
		return $this;
	}

	final public function get_id() {
		return $this->_id;
	}

	final private function _set_id($force = false) {
		if (!$this->_id_set && !$force) {
			return false;
		}

		if (is_array($this->_id)) {
			if (empty($this->_id)) {
				$this->throw_error();
			}
			$op = ($this->_id_equal) ? 'IN' : 'NOT IN';
		} else {
			if ($this->_id < 1) {
				$this->throw_error();
			}
			$op = ($this->_id_equal) ? '=' : '!=';
		}

		$this->builder->where("{$this->table}.id", $op, $this->_id);
		return true;
	}

	/*
	 * ALIAS
	 */

	final public function alias($value, $equal_to = true) {
		$this->_alias = $value;
		$this->_alias_set = true;
		$this->_alias_equal = boolval($equal_to);
		return $this;
	}

	final public function get_alias() {
		return $this->_alias;
	}

	final private function _set_alias($force = false) {
		if (!$this->_alias_set && !$force) {
			return false;
		}

		if (!$this->can_use_alias) {
			$this->throw_error();
			return false;
		}

		if (is_array($this->_alias)) {
			if (empty($this->_alias)) {
				$this->throw_error();
			}
			$op = ($this->_alias_equal) ? 'IN' : 'NOT IN';
		} else {
			$op = ($this->_alias_equal) ? '=' : '!=';
		}

		$this->builder->where("{$this->table}.{$this->field_alias}", $op, $this->_alias);
		return true;
	}

	/*
	 * JOIN
	 */

	final public function join_using_many_to_many($model_or_table_name, $through) {
		$foreign_table_name = inflector::plural($model_or_table_name);
		$field_current = inflector::singular($this->table) . '_id';
		$field_foreign = inflector::singular($foreign_table_name) . '_id';

		if (empty($through)) {
			// Find the join table based on the two model names pluralized,
			// sorted alphabetically and with an underscore separating them
			$through = array(
				$foreign_table_name,
				$this->table,
			);

			sort($through);
			$through = implode('_', $through);
		} else {
			$through = (string)$through;
		}

		$this->builder
			->join($through)->on("{$this->table}.id", '=', "{$through}.{$field_current}")
			->join($foreign_table_name)->on("{$foreign_table_name}.id", '=', "{$through}.{$field_foreign}");

		return $this;
	}

	/*
	 * ORDER BY
	 */

	public function order_by($column, $direction = NULL) {
		$this->builder->order_by($column, $direction);
		return $this;
	}

} // End Force_Core_Common
