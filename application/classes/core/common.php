<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Common
 * User: legion
 * Date: 27.09.14
 * Time: 14:30
 */
abstract class Core_Common extends Force_Core_Common {

	/*
	 * Ниже следуют все доступные для переопределения методы
	 */

	/*
	 * READ ONLY ACCESS TO PARAMS
	 */

	public function __get($param) {
		$param = strtolower($param);
		switch ($param) {
			case 'builder':
				return null;
		}
		return $this->{$param};
	}

	/**
	 * Предоставляет возможность получить данные из
	 * ->meta()->fields()
	 * на момент __construct()
	 *
	 * @param $fields
	 */
	protected function _parse_fields($fields) { }

	/**
	 * Предоставляет возможность указать дополнительные проверки перед любым запросом к базе
	 */
	protected function _before_get() { }

	/**
	 * Предоставляет возможность произвести проверку на возможность удаления записи
	 *
	 * @param Jelly_Model $model
	 *
	 * @return bool
	 */
	protected function _can_be_deleted(Jelly_Model $model) {
		return true;
	}

	/**
	 * Предоставляет возможность выполнить действия перед удалением.
	 *
	 * @param Jelly_Model $model
	 */
	protected function _before_delete_model(Jelly_Model $model) { }

	/**
	 * Предоставляет возможность выполнить действия после удаления.
	 *
	 * @param Jelly_Model $model
	 */
	protected function _after_delete_model(Jelly_Model $model) { }

	/**
	 * Предоставляет возможность произвести проверку на возможность восстановления записи
	 *
	 * @param Jelly_Model $model
	 *
	 * @return bool
	 */
	protected function _can_be_restored(Jelly_Model $model) {
		return true;
	}

	/**
	 * Предоставляет возможность внести изменения в модель перед восстановлением.
	 * Актуально для удаления через deleted_at
	 *
	 * @param Jelly_Model $model
	 */
	protected function _before_restore_model(Jelly_Model $model) { }

	/**
	 * Предоставляет возможность внести изменения в модель после восстановления.
	 * Актуально для удаления через deleted_at
	 *
	 * @param Jelly_Model $model
	 */
	protected function _after_restore_model(Jelly_Model $model) { }

	/**
	 * @param string $id
	 * @param string $name
	 *
	 * @return array
	 */
	protected function _get_list_for_select_box($id = 'id', $name = 'name') {
		$this->builder
			->select_column("{$this->table}.{$id}")
			->select_column("{$this->table}.{$name}");
		return $this->get_list()->as_array($id, $name);
	}

	/*
	 * PREDEFINED SETUPS
	 */

	public function preset_for_admin() {
		$this->ignore_is_published();
		return $this;
	}

} // End Core_Common
