<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Application
 * User: legion
 * Date: 04.09.12
 * Time: 16:48
 */
class Model_Application extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('applications');

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'name' => new Jelly_Field_String(array(
				'label' => __('common.name'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							255,
						),
					),
				),
			)),
			'domain' => new Jelly_Field_String(array(
				'label' => __('common.domain'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							128,
						),
					),
				),
			)),
			'key' => new Jelly_Field_String(array(
				'label' => __('common.key'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							32,
						),
					),
					array(
						'min_length',
						array(
							':value',
							32,
						),
					),
				),
			)),
			'created_at' => new Jelly_Field_Timestamp(array(
				'label' => __('common.created_at'),
				'format' => Force_Date::FORMAT_SQL,
				'default' => time(),
			)),
			'updated_at' => new Jelly_Field_Timestamp(array(
				'label' => __('common.updated_at'),
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

} // End Model_Application