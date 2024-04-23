<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Articles
 * User: ener
 * Date: 26.04.12
 * Time: 21:57
 */
class Controller_Admin_Articles extends Controller_Admin_Template {

	protected $sort_table = 'articles';

	public function action_index() {
		$builder = Core_Article::factory()->preset_for_admin()->get_builder()->order_by('title');

		$filter = Force_Filter::factory(array(
			Force_Filter_Input::factory('title', __('common.title_or_alias'))
				->where(array(
					'articles.title',
					'articles.alias',
				), 'LIKE'),
		))->apply($builder);

		$list = Force_List::factory()->preset_for_admin();

		$list->column('id');
		$list->column('sort_field')->sorting_data();
		$list->column('title');
		$list->column('tags_cache')->label(__('common.tags'))->attribute('width', '25%');
		$list->column('is_published')->col_control();
		$list->column('created_at');
		$list->column('button_edit')->button_edit();
		$list->column('button_delete')->button_delete();

		$list->apply($builder)
			->button_add()
			->each(function (Jelly_Model $model) {
//				$model->tag = Core_Tag::get_tag_string_from_tags_collection($model->tags);
				$model->format('is_published', Force_Label::factory($model->is_published)->preset_boolean_published());
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Article::factory()->create();
		$model->sort_field = Core_Article::factory()->preset_for_admin()->get_count() + 1;
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Article::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	protected function _form(Jelly_Model $model) {
//		$author = Core_User::factory($model->author_id)->get_one();

//		if (Core_User::is_loaded($author)) {
//			$author = $author->get_name();
//		} else {
//			$author = null;
//		}

		$form = Jelly_Form::factory($model, array(
			Force_Form_Section::factory(__('form.tab.common'), array(
				'title',
				Force_Form_Alias::factory('alias'),
				Force_Form_Redactor::factory('description'),
				Force_Form_Tags::factory('tags')->value($model->tags_cache),
				'is_published',
				Force_Form_Date::factory('created_at')->pick_time(),

				/*
				 * По умолчанию image_type будет сгенерирован автоматически от имени модели и имени поля
				 * т.е. image_type по умолчанию будет article_image
				 *
				 * Но в данном случае основным является не article_image, а article_image_large
				 * поэтому указываем принудительно. Указывается всегда тот image_type, в котором описана нарезка.
				 *
				 * При этом есть пожелание, чтобы в форме была показана только одна картинка, в таких случаях
				 * дополнительно указываем image_type_to_display().
				 */
				Force_Form_Image::factory('image')
					->image_type('article_image_large')
					->image_type_to_display('article_image_large'),
			)),
			/*
			 * Force_Form_Combine не является наследником класса Force_Form_Container, однако для своего вывода
			 * использует render() от Force_Form_Section. Иными словами Force_Form_Combine сам создаёт для себя секцию.
			 *
			 * Force_Form_Combine не принимает в себя ряд компонентов и будет злостно ругаться на эту тему.
			 * Одними из таких запрещённых компонентов являются все наследники класса Force_Form_Container,
			 * например Force_Form_Section.
			 */
			Force_Form_Combine::factory('content', __('common.content'), [
				Force_Form_Redactor::factory(),
				Force_Form_Video::factory()->youtube(),
				Force_Form_Image::factory()
					->use_title()
					->image_type('article_image_large')
					->image_type_to_display('article_image_large'),
			]),
		))->preset_for_admin();

		if ($form->is_ready_to_apply()) {
			$form->apply_before_save();

			$tags = Arr::get($_POST, 'tags', '');
			$tags = explode(',', $tags);
			foreach ($tags as $_key => $_tag) {
				$tags[$_key] = trim($_tag);
			}
			$model->tags_cache = implode(', ', $tags);

			if (empty($model->created_at)) {
				$model->created_at = time();
			}

			$form->save();
			$form->apply_after_save();
			$form->redirect();
		}

		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Article::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

} // End Controller_Admin_Articles