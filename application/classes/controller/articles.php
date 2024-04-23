<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Articles
 * User: ener
 * Date: 03.09.12
 * Time: 1:13
 */
class Controller_Articles extends Controller_Bootstrap_Template {

	public function before() {
		parent::before();
		Force_Breadcrumbs::instance()->add(__('menu.articles'), '/:con');
	}

	public function action_index() {
		$this->action_tag();
	}

	public function action_tag() {
		$tag_id = $this->request->param('id');

		$search_term = trim(Arr::get($_GET, 'term'));

		if ($tag_id > 0) {
			$tag = Core_Tag::factory($tag_id)->get_one();
			if ($tag->loaded()) {
				Force_Breadcrumbs::instance()->add($tag->title);
				$articles = Core_Article::factory()->get_builder()->join('articles_tags')
					->on('articles_tags.article_id', '=', 'articles.id')->where('tag_id', '=', $tag_id)
					->select_all();
			} else {
				$articles = Core_Article::factory()->get_list();
			}
		} else {
			if (!empty($search_term)) {
				Force_Breadcrumbs::instance()->add($search_term);
				$search_term_sql = Helper_Html::prepare_value_for_sql($search_term);
				$articles = Core_Article::factory()->get_builder()
					->where_open()
					->where('tags_cache', 'LIKE', $search_term_sql)
					->or_where('title', 'LIKE', $search_term_sql)
					->or_where('description', 'LIKE', $search_term_sql)
					->or_where('content', 'LIKE', $search_term_sql)
					->where_close()
					->order_by('created_at', 'desc')
					->select_all();
			} else {
				$articles = Core_Article::factory()->get_builder()
					->order_by('created_at', 'desc')
					->select_all();
			}
		}

		$articles_count = $articles->count();

		$tags = $this->_display_tags();

		$search = $this->_display_search($search_term);
		$this->template->page_title = 'Статьи';
		$this->template->content = View::factory(CONTROLLER_VIEW . 'articles/index')
			->bind('articles', $articles)
			->bind('articles_count', $articles_count)
			->bind('tags', $tags)
			->bind('search', $search)
			->set('breadcrumbs', Force_Breadcrumbs::instance()->render());
	}

	public function action_post() {
		$model = Core_Article::factory()->request_alias()->on_error_throw_404()->get_one();

		Force_Breadcrumbs::instance()->add($model->title);

		$tags = $this->_display_tags($model);
		$search = $this->_display_search();

		$model->description = Markdown::instance()->transform($model->description);
		$content = Force_Form_Combine::transform_to_array($model->content);

		$this->template->page_title = $model->title . ' :: Статьи';

		$this->template->content = View::factory(CONTROLLER_VIEW . 'articles/post')
			->bind('article', $model)
			->bind('tags', $tags)
			->bind('search', $search)
			->bind('content', $content)
			->set('breadcrumbs', Force_Breadcrumbs::instance()->render());
	}

	protected function _display_tags(Jelly_Model $model = null) {
		if (Core_Article::is_model($model)) {
			$tags = Core_Tag::factory()->article($model->id)->get_list_as_array();
		} else {
			$tags = Core_Tag::factory()->actualize_with_articles()->get_list_as_array();
		}

		$tags_count = count($tags);
		if ($tags_count > 0) {
			return View::factory(CONTROLLER_VIEW . 'articles/tags')
				->bind('tags_count', $tags_count)
				->bind('tags', $tags)
				->render();
		}
		return '';
	}

	protected function _display_search($term = '') {
		return View::factory(CONTROLLER_VIEW . 'articles/search')->bind('term', $term);
	}

} // End Controller_Articles