<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Documentation_List
 * User: legion
 * Date: 15.07.14
 * Time: 15:31
 */
class Controller_Developer_Documentation_List extends Controller_Developer_Documentation_Template {

	public function action_index() {
		$doc = Force_Documentation::factory()->show_menu();

		$doc->heading1('Force_List')
			->heading2('Подключение списка')
			->example('$list = Force_List::factory();
$list->column(\'name\');
$list->column(\'description\')->label(__(\'common.description\'));
$list->column(\'button_edit\')->button_edit();
$list->column(\'button_delete\')->button_delete();

$list->apply($builder)
	->button_add()
	->each(function (Jelly_Model $model) {
		if (in_array($model->name, Core_User_Role::get_fixed_roles())) {
			$model->button_edit = \'\';
			$model->button_delete = \'\';
		}
	});',
				'Подключение списка на примере списка ролей.');
		$doc->heading3('Передача данных')
			->text([
				'Есть несколько способов передачи данных в список.',
				'При помощи универсального метода apply() или при помощи специализированных методов типа apply_array().',
				'Метод apply() автоматически определяет тип данных и вызывает нужный специализированный метод.',
			])
			->example('apply(&$data, $count_or_pagination = null, $apply_pagination = true)')
			->callout_info('$data', [
				'На данный момент может быть трёх различных типов: array(), Jelly_Builder и Jelly_Collection',
			])
			->callout_info('$count_or_pagination', [
				'Может содержать в себе максимальное число строк удовлетворяющих условиям выборки данных или уже настроенный объект класса Pagination.',
			])
			->callout_info('$apply_pagination', [
				'Параметр отвечающий за применение пагинации как таковое, уместно ставить его в FALSE когда необходимо вывести весь диапазон данных, не зависимо от настроек пагинации.'
			]);

		$doc->heading2('Работа с Jelly_Builder');

		$doc->heading3('Указание колонок')
			->text([
				'Если поле было объявлено в модели (Jelly_Model), то достаточно лишь упомянуть его имя.',
				'Такие данные, как label поля и его description могут быть извлечены из модели, если они были там указаны.',
				'Также на вид поля влияет тип поля в Jelly_Model.'
			]);

//		$doc->heading3('Обработка данных на лету');
		$doc->callout_warning('Статья ещё не закончена', 'Ожидайте...');

		$this->template->content = $doc->render();
	}

} // End Controller_Developer_Documentation_List