<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Incompatible_Mod
 * User: legion
 * Date: 19.10.19
 * Time: 8:40
 *
 * @property integer $id
 * @property integer $itemIndex
 * @property integer $incompatibleattachmentIndex
 */
class Model_Incompatible_Mod extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('incompatible_mod');
		$meta->sorting(array(
			'itemIndex' => 'asc',
			'incompatibleattachmentIndex' => 'asc',
		));

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'itemIndex' => new Jelly_Field_Integer(),
			'incompatibleattachmentIndex' => new Jelly_Field_Integer(),
		));
	}

} // End Model_Incompatible_Mod