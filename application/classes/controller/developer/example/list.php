<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Example_List
 * User: legion
 * Date: 19.08.14
 * Time: 8:07
 */
class Controller_Developer_Example_List extends Controller_Developer_Template {

	public function action_index() {
		$builder = Core_Tag::factory()->get_builder();

		$filter = Force_Filter::factory([
			Force_Filter_Input::factory('title')->where('title', 'LIKE'),
		])->apply($builder);

		$list = Force_List::factory([
			'id',
			Force_List_Column::factory('title')->label(__('common.title'))->col_main(),

			/*
			 * Такого поля в модели нет, но мы зададим его в each().
			 */
			'uniqid',

			/*
			 * Кнопка, вариант 1: указываем кнопку напрямую
			 * параметры $link_without_id и $id_field как бы намекают, что ссылка дальше будет собираться
			 * из частей, по ходу вывода строк. Сборка выполняется конкатенацией:
			 * $link_without_id . '/' . $model->{$id_field}
			 * Если такой вариант не устраивает, следует воспользоваться третьим вариантом.
			 *
			 * Примечание: при установке $id_field в null - в качестве ID будет взят ключ массива:
			 * $link_without_id . '/' . $key
			 * Внимание! Ключ массива НЕ является номером строки всего массива данных и обнуляется на каждой странице.
			 */
			Force_List_Column::factory()->button(Force_Button::factory('info')->btn_info(), '/info'),

			/*
			 * Кнопка, вариант 2: указываем кнопку при помощи предустановленных значений:
			 * button_edit()
			 * button_delete()
			 * принимают ссылки в том же формате, что и выше, но имеют значения по умолчанию (выставляют action
			 * в edit и delete для редактирования и удаления соответственно), поэтому ссылки нужно указывать
			 * только если crud кастомизирован.
			 * $name в factory колонки для кнопок передаётся только в том случае, если потребуется дальше
			 * поменять значение поля.
			 */
			Force_List_Column::factory('edit')->button_edit(),

			/*
			 * Кнопка, вариант 3, этап 1: установка параметров поля.
			 * button_place определяет только ширину поля и устанавливает пустой заголовок. Подобное указание
			 * предполагает описание кнопки в each(),
			 * обращение к кнопке будет происходить по $name переданному в factory.
			 */
			Force_List_Column::factory('delete')->button_place(),
		])
			->preset_for_admin()
			->button_add()
			->button(Force_Button::factory('КРАСНАЯ КНОПКА')->btn_danger())
			->button_html(__('common.name'))
			->apply_jelly_builder($builder)
			/*
			 * Модель передаётся по ссылке, но указывать & перед моделю не обязательно, так как классы можно
			 * передавать по ссылке в параметры функции, а вот с массивами такой трюк не работает.
			 * function ($model), но function (&$row)
			 */
			->each(function ($model, $a, $b) {
				$model->title .= ' ' . $a . ' ' . $b;
				$model->uniqid = uniqid();
				/*
				 * Кнопка, вариант 3, этап 2: полная кастомизация кнопки.
				 * Здесь присваиваем и указываем параметры кнопки самостоятельно.
				 * Если присваиваем объект кнопки, как указано ниже, то ему будут добавлены вызовы:
				 * ->btn_xs()->render().
				 * Если же указать у кнопки render() самостоятельно, то кнопка будет показана такая какую определили.
				 */
				$model->delete = Force_Button::preset_delete('/developer/example_list/delete/' . $model->id);
				$row_params = Force_List_Row::factory();
				if (($model->{Force_List::ROW_NUMBER} % 2) == 0) {
					/*
					 * Выше объявлена кнопка с $name = 'edit' поэтому можно обратиться к этому полю и поменять
					 * его значение. Изменения вступят в силу, только если новое значение отлично от null.
					 * Если у модели есть такое поле и в нём есть значения отличные от null,
					 * то кнопка в таких строках отображаться не будет, вместо неё будут выведены значения поля.
					 */
					$model->edit = 'ololo';
					$row_params->cell('edit')
						->overwrite_column_attributes(true)
						->attribute('class', 'table-col-right')
						->attribute('style', 'color:red');
				} else {
					$key = rand(0, 3);
					$colors = array(
						'danger',
						'warning',
						'success',
						'info',
					);
					$row_params->attribute('class', $colors[$key]);
				}
				return $row_params;
			}, 'два', 'слова')
			->title('Пример обработки Jelly_Collection');

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_assoc_array() {
		$builder = Core_Tag::factory()->get_builder();

		$filter = Force_Filter::factory([
			Force_Filter_Input::factory('title')->where('title', 'LIKE'),
		])->apply($builder);

		$count = $builder->count();

		$pagination = Helper_Pagination::get_admin_pagination($count);
		$collection = $builder->apply_pagination($pagination)->select_all();

		$data = $collection->as_array();

		$list = Force_List::factory()->preset_for_admin();

		$list->column('id');
		$list->column('title')->label(__('common.title'));
		$list->column('button_edit')->button_edit();

		$list->apply_array($data, $pagination, false)
			->title('Пример обработки Assoc Array')
			/*
			 * При определении параметра $row необходимо указывать, что он передаётся по ссылке.
			 * Классы можно передавать по ссылке в параметры функции, а вот с массивами такой трюк не работает.
			 * function ($model), но function (&$row)
			 */
			->each(function (&$row, $a, $b) {
				$col = 'title';
				$title = '';
				if (array_key_exists($col, $row)) {
					$row[$col] .= ' ' . $a . ' ' . $b;
					$title = $row[$col];
				}
				$attr = Force_List_Row::factory();
				if (strlen($title) > 20) {
					$attr->cell('title')->attribute('style', 'color:green');
				}
				return $attr;
			}, 'раз', 'два')
			->add_row_before([
				'title' => 'третья',
			])
			->add_row_before([
				'title' => 'вторая',
			])
			->add_row_before([
				'title' => 'первая',
			])
			->add_row_after([
				'title' => 'предпоследняя',
			])
			->add_row_after([
				'title' => 'последняя',
			], Force_List_Row::factory()->attribute('style', 'font-weight:bold'));

		$list->column('id')->attribute('style', 'color:red');

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	public function action_dynamic_array() {
		/*
		 * 15 строк, по три колонки в каждой.
		 */
		$data = array(
			[
				0,
				1,
				2,
			],
			array(
				3,
				4,
				5,
			),
			array(
				6,
				Force_Form_Checkbox::factory('asdsad')->simple(),
				8,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
			array(
				9,
				0,
				1,
			),
		);

		$list = Force_List::factory()->preset_for_admin();

		$list->column(0)->label('Колонка А');
		$list->column(1)->label('Колонка Б');
		$list->column(2)->label('Колонка В');
		/*
		 * Вторым параметром указывается ключ колонки из которой брать значения,
		 * которые будут выступать в роли ID.
		 */
		$list->column('edit')->button_edit(null, 0);

		$list->title('Пример обработки Dynamic Array')
			->apply_array($data)
			->each(function (&$row, $a, $b) {
				$col = 2;
				if (array_key_exists($col, $row)) {
					$row[$col] .= ' ' . $a . ' ' . $b;
				}
			}, 'раз', 'два');

		$this->template->content[] = $list->render();
	}

	public function action_csv() {

		/*
		 * CSV обрабатывается как динамический массив. Но, так как, столбцы обладают загаловками, то
		 * оные автоматически будут подставлены при указании только номера столбца.
		 *
		 * Для файлов типа CSV описывать колонки не обязательно.
		 */
		$list = Force_List::factory()
			->preset_for_admin()
			->title('Пример обработки CSV');

		$list->column(2);
		$list->column(0)->label('Чистое имя');
		$list->column(3);
		/*
		 * При установке $id_field в null - в качестве ID будет взят ключ массива:
		 * $link_without_id . '/' . $key
		 * Внимание! Ключ массива НЕ является номером строки всего массива данных и обнуляется на каждой странице.
		 *
		 * Специально для файлов введено поле с ключом Force_List::ID_FIELD_FILE_LINE_NUMBER.
		 * По умолчанию в колонках не отображается.
		 */
		$list->column(Force_List::ID_FIELD_FILE_LINE_NUMBER)->label('№');
		$list->column('edit')->button_edit(null, Force_List::ID_FIELD_FILE_LINE_NUMBER);

		$list->apply_csv(DOCROOT . 'uploads/teams.csv');

		$this->template->content[] = $list->render();
	}

	public function action_csv_simple() {

		/*
		 * Для файлов типа CSV описывать колонки не обязательно.
		 */
		$list = Force_List::factory()
			->preset_for_admin()
			->title('Пример обработки CSV (упрощённый)')
			->apply_csv(DOCROOT . 'uploads/teams.csv');

		$this->template->content[] = $list->render();
	}

} // End Controller_Developer_Example_List