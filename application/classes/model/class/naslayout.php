<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Class_NasLayout
 * User: legion
 * Date: 28.07.2020
 * Time: 14:40
 */
class Model_Class_NasLayout extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('classes_nas_layout');

		$meta->sorting(array(
			'nasLayoutClass' => 'asc',
		));

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'nasLayoutClass' => new Jelly_Field_Integer([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'nasLayoutClassName' => new Jelly_Field_String([
				'rules' => array(
					array('not_empty'),
				),
			]),
		));
	}

} // End Model_Class_NasLayout