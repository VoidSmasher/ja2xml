<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Tag
 * User: legion
 * Date: 20.01.14
 * Time: 18:27
 */
class Model_Tag extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('tags');
		$meta->sorting(array(
			'title' => 'asc',
		));

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'title' => new Jelly_Field_String(array(
				'label' => __('common.name'),
				'rules' => array(
					array(
						'max_length',
						array(
							':value',
							25,
						),
					),
				),
				'unique' => true,
			)),
		));
	}

} // End Model_Tag