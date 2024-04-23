<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Tags
 * User: legion
 * Date: 09.09.14
 * Time: 20:38
 */
class Controller_Admin_Tags extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Tag::factory()->preset_for_admin()->get_builder();

		$filter = Force_Filter::factory(array(
			'title' => 'LIKE',
		))->apply($builder);

		$count = $builder->count();

		$builder->join('articles_tags', 'left')->on('articles_tags.tag_id', '=', 'tags.id')
			->group_by('tags.id')
			->select_column('tags.*')
			->select_column(DB::expr('count(articles_tags.article_id)'), 'links');

		$list = Force_List::factory()->preset_for_admin();

		$list->column('id');
		$list->column('title');
		$list->column('links')->label(__('common.links'))->col_control();
		$list->column('button_delete')->button_place();

		$list->apply($builder, $count)->button_add()->title(__('common.tags'))
			->each(function ($model) {
				if ($model->links < 1) {
					$model->button_delete = Force_Button::preset_delete($model->id);
				}
			});

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_add() {
		$model = Core_Tag::factory()->create();
		$this->_form($model);
	}

	public function action_edit() {
		$model = Core_Tag::factory()->preset_for_admin()->on_error_throw_404()->request_id()->get_one();
		$this->_form($model);
	}

	public function _form(Jelly_Model $model) {
		$form = Jelly_Form::factory($model)
			->preset_for_admin();
		$this->template->content = $form->render();
	}

	public function action_delete() {
		if (Core_Tag::factory()->preset_for_admin()->on_error_throw_404()->request_id()->delete()) {
			$this->_back_to_index();
		}
	}

} // End Controller_Admin_Tags