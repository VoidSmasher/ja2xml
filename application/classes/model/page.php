<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Page
 * User: legion
 * Date: 13.07.14
 * Time: 13:27
 */
class Model_Page extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('pages');
		$meta->sorting(array(
			'title' => 'asc',
		));

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'alias' => new Jelly_Field_String(array(
				'label' => 'ALIAS',
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							150,
						),
					),
				),
			)),
			'title' => new Jelly_Field_String(array(
				'label' => __('common.title'),
				'rules' => array(
					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							100,
						),
					),
				),
				'unique' => true,
			)),
			'content' => new Jelly_Field_Text(array(
				'label' => __('common.text'),
				'rules' => array(
					array('not_empty'),
				),
			)),
			'is_published' => new Jelly_Field_Boolean(array(
				'label' => __('common.is_published'),
				'default' => false,
			)),
			'created_at' => new Jelly_Field_Timestamp(array(
				'label' => __('common.created_at'),
				'format' => Force_Date::FORMAT_SQL,
				'default' => time(),
			)),
		));
	}

	/*
	 * SAVE OVERRIDE
	 */

	public function save($validation = NULL) {
		if (empty($this->alias) && !empty($this->title)) {
			$this->alias = URL::title($this->title);
		} else {
			$this->alias = URL::title($this->alias);
		}

		if ($this->loaded()) {
			$this->updated_at = time();
		}

		if (array_key_exists('alias', $this->_changed) && $this->_original['alias'] == $this->_changed['alias']) {
			unset($this->_changed['alias']);
		}

		return parent::save($validation);
	}

} // End Model_Page