<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Attachment
 * User: legion
 * Date: 12.10.19
 * Time: 19:45
 */
class Model_Attachment extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('attachments');
		$meta->sorting(array(
			'attachmentIndex' => 'asc',
			'itemIndex' => 'asc',
		));

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'attachmentIndex' => new Jelly_Field_Integer(),
			'itemIndex' => new Jelly_Field_Integer(),
			'APCost' => new Jelly_Field_Integer([
				'default' => 20,
			]),
		));
	}

} // End Model_Attachment