<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Tag
 * User: legion
 * Date: 15.06.12
 * Time: 16:06
 */
class Core_Tag extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Tag';
	protected $model_name = 'tag';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	/*
	 * SET
	 */

	public function article($id) {
		if (!is_numeric($id) || ($id < 1)) {
			$id = null;
		}
		$this->builder
			->join('articles_tags')->on('articles_tags.tag_id', '=', "{$this->table}.id")
			->where('articles_tags.article_id', '=', $id);
		return $this;
	}

	public function title($title) {
		$this->builder->where("{$this->table}.title", '=', $title);
		return $this;
	}

	/*
	 * GET
	 */

	public function get_list_as_array($key = 'id', $value = 'title') {
		return $this->get_list()->as_array($key, $value);
	}

	/*
	 * GET STRING
	 */

	public static function get_tag_string_from_tags_collection($tags_collection) {
		$tags = array();
		if (!is_null($tags_collection) && ($tags_collection instanceof Jelly_Collection) && ($tags_collection->count() > 0)) {
			foreach ($tags_collection as $tag) {
				$tags[] = $tag->title;
			}
		}
		return implode(', ', $tags);
	}

	/*
	 * ADD
	 */

	public static function add_tag($tag_name) {
		$tag_name = trim(mb_strtolower($tag_name));
		$name_max_length = Helper_Jelly::get_max_length('tag', 'title');
		$tag_name = mb_substr($tag_name, 0, $name_max_length, 'utf-8');

		$tag_in_db = Core_Tag::factory()->title($tag_name)->get_one();

		if (!$tag_in_db->loaded()) {
			$tag_in_db = Core_Tag::factory()->create();
			$tag_in_db->title = $tag_name;
			try {
				$tag_in_db->save();
			} catch (Jelly_Validation_Exception $e) {
				Helper_Error::add_from_jelly($tag_in_db, $e->errors());
			}
			if (!$tag_in_db->saved()) {
				$tag_in_db = false;
			}
		}
		return $tag_in_db;
	}

	public static function add_tags($tags) {
		$result_tags = array();
		if (!empty($tags)) {
			if (is_string($tags)) {
				$tags = explode(',', $tags);
			}
			foreach ($tags as $index => $tag_name) {
				$tags[$index] = trim(mb_strtolower($tag_name));
			}
			$tags = array_unique($tags);

//			Helper_Error::var_dump($tags, 'tags');

			$tags_loaded = Core_Tag::factory()->get_builder()
				->where('title', 'in', $tags)
				->select_all()
				->as_array(null, 'title');

//			Helper_Error::var_dump($tags_loaded, 'tags_loaded');

			$tags_not_in_db = array_diff($tags, $tags_loaded);

//			Helper_Error::var_dump($tags_not_in_db, 'tags_diff');

			if (!empty($tags_not_in_db)) {
				$builder = DB::insert('tags', array('title'));

				foreach($tags_not_in_db as $_tag) {
					$builder->values(array($_tag));
				}

				$builder->execute();
			}

			$result_tags = Core_Tag::factory()->get_builder()
				->where('title', 'in', $tags)
				->select_all()
				->as_array(null, 'id');
		}
		return $result_tags;
	}

	/*
	 * ACTUALIZATION
	 */

	public function actualize_with_articles() {
		$this->builder
			->join('articles_tags')->on('articles_tags.tag_id', '=', "{$this->table}.id")
			->join('articles')->on('articles.id', '=', 'articles_tags.article_id')
			->where('articles.is_published', '=', true);
		return $this;
	}

	/*
	 * PREDEFINED SETUPS
	 */

	public function preset_for_admin() {
		parent::preset_for_admin();
		return $this;
	}

} // End Core_Tag
