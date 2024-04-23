<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Model_Article
 * User: ener
 * Date: 29.07.12
 * Time: 3:03
 */
class Model_Article extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta) {
		$meta->table('articles');

		if (Helper_Auth::is_user_authorized()) {
			$author = Helper_Auth::get_user()->id;
		} else {
			$author = null;
		}

		$meta->fields(array(
			'id' => new Jelly_Field_Primary(),
			'author_id' => new Jelly_Field_Integer(array(
				'label' => __('common.author'),
				'default' => $author,
			)),
			'alias' => new Jelly_Field_String(array(
				'label' => __('common.alias'),
				'description' => __('article.alias.description'),
				'rules' => array(
//					array('not_empty'),
					array(
						'max_length',
						array(
							':value',
							128,
						),
					),
				),
			)),
			'image' => new Jelly_Field_String(array(
				'label' => __('common.image'),
			)),
			'title' => new Jelly_Field_String(array(
				'label' => __('common.title'),
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
			'description' => new Jelly_Field_Text(array(
				'label' => __('common.description'),
			)),
			'tags' => new Jelly_Field_ManyToMany(array(
				'label' => __('common.tags'),
			)),
			'tags_cache' => new Jelly_Field_Text(),
			'content' => new Jelly_Field_Text(array(
				'label' => __('common.content'),
//				'rules' => array(
//					array('not_empty'),
//				),
			)),
			'is_published' => new Jelly_Field_Boolean(array(
				'label' => __('common.is_published'),
				'default' => false,
			)),
			'sort_field' => new Jelly_Field_Integer(array(
				'label' => __('common.sort_field'),
				'default' => 0,
			)),
			'created_at' => new Jelly_Field_Timestamp(array(
				'editable' => true,
				'label' => __('common.created_at'),
				'format' => Force_Date::FORMAT_SQL,
				'default' => time(),
			)),
		));
	}

	public function get_image_large($as_html_tag = FALSE, $attributes = null) {
		if ($as_html_tag) {
			return Helper_Image::get_image($this->image, 'article_image_large', '', $attributes);
		} else {
			return Helper_Image::get_filename($this->image, 'article_image_large');
		}
	}

	public function get_image($as_html_tag = FALSE, $attributes = null) {
		if ($as_html_tag) {
			return Helper_Image::get_image($this->image, 'article_image', '', $attributes);
		} else {
			return Helper_Image::get_filename($this->image, 'article_image');
		}
	}

	public function get_image_small($as_html_tag = FALSE, $attributes = null) {
		if ($as_html_tag) {
			return Helper_Image::get_image($this->image, 'article_image_small', '', $attributes);
		} else {
			return Helper_Image::get_filename($this->image, 'article_image_small');
		}
	}

	public function remove_image($only_file = true) {
		$result = true;
		if (!empty($this->image)) {
			if (!$only_file && Helper_Image::remove_file($this->image, 'article_image_large')) {
				$this->image = NULL;
				try {
					$this->save();
				} catch (Jelly_Validation_Exception $e) {
					$result = !Helper_Error::add_from_jelly($this, $e->errors());
				}
			}
		}
		return $result;
	}

	/*
	 * PATH
	 */

	public function get_path($action = 'index') {
		switch ($action) {
			case 'show':
				$result = URL::site("article/{$this->alias}");
				break;
			default:
				$result = URL::site('articles');
				break;
		}
		return $result;
	}

	/*
	 * SAVE OVERRIDE
	 */

	public function save($validation = NULL) {
		if (empty($this->alias) && !empty($this->title)) {
			$this->alias = Helper_String::translate_to_alias($this->title);
		} else {
			$this->alias = Helper_String::clean_alias($this->alias);
		}

		if (array_key_exists('alias', $this->_changed) && $this->_original['alias'] == $this->_changed['alias']) {
			unset($this->_changed['alias']);
		}

		return parent::save($validation);
	}

} // End Model_Article
