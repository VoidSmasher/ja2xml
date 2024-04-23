<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Class_Attachment
 * User: legion
 * Date: 28.07.2020
 * Time: 14:40
 */
class Model_Class_Attachment extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('classes_attachment');

		$meta->sorting(array(
			'AttachmentClass' => 'asc',
		));

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'AttachmentClass' => new Jelly_Field_Integer([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'AttachmentClassName' => new Jelly_Field_String([
				'rules' => array(
					array('not_empty'),
				),
			]),
			'type' => new Jelly_Field_Integer([
				'default' => 0,
			]),
		));
	}

} // End Model_Class_Attachment