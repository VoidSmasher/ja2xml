<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Deferred_Action
 * User: CRUD Generator
 * Date: 12.06.16
 * Time: 06:24
 */
class Model_Deferred_Action extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {

		$meta->table('deferred_actions');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(array(
				'label' => __('deferred_action.id'),
			)),
			'action' => new Jelly_Field_Integer(array(
				'label' => __('deferred_action.action'),
			)),
			'priority' => new Jelly_Field_Integer(array(
				'label' => __('deferred_action.priority'),
			)),
			'data' => new Jelly_Field_Text(array(
				'label' => __('deferred_action.data'),
			)),
			'tries' => new Jelly_Field_Integer(array(
				'label' => __('deferred_action.tries'),
				'default' => 0,
			)),
			'created_at' => new Jelly_Field_Timestamp(array(
				'label' => __('common.created_at'),
				'format' => Force_Date::FORMAT_SQL,
				'default' => date(Force_Date::FORMAT_SQL),
			)),
			'canceled_at' => new Jelly_Field_Timestamp(array(
				'label' => __('deferred_action.canceled_at'),
				'format' => Force_Date::FORMAT_SQL,
			)),
			'updated_at' => new Jelly_Field_Timestamp(array(
				'label' => __('common.updated_at'),
				'format' => Force_Date::FORMAT_SQL,
			)),
			'executed_at' => new Jelly_Field_Timestamp(array(
				'label' => __('deferred_action.executed_at'),
				'format' => Force_Date::FORMAT_SQL,
			)),
		));
	}

	/*
	 * SAVE OVERRIDE
	 */

	public function save($validation = NULL) {
		if ($this->loaded()) {
			$this->updated_at = time();
		}

		return parent::save($validation);
	}

} // End Model_Deferred_Action
