<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Documentation_Filter
 * User: legion
 * Date: 15.07.14
 * Time: 20:46
 */
class Controller_Developer_Documentation_Filter extends Controller_Developer_Documentation_Template {

	public function action_index() {
		$doc = Force_Documentation::factory()->show_menu();

		$doc->heading1('Force_Filter')
			->heading2('Подключение фильтров')
			->example('$builder = Core_User::factory()->get_builder();
$roles = Core_User_Role::factory()->get_list_as_array();

$filter = Force_Filter::factory(array(
	Force_Filter_Input::factory(\'name\', \'Имя пользователя\')
		->where(DB::expr(\'CONCAT_WS("", users.name, users.surname, users.patronymic)\'), \'LIKE\'),
	Force_Filter_Select::factory(\'role\', \'по привелегиям\', $roles)
		->join_on(\'roles_users\', \'users.id\', \'=\', \'roles_users.user_id\')
		->where(\'roles_users.role_id\'),
))->apply($builder);
...
$this->template->content[] = $filter->render();',
				'Подключение фильтра на примере списка пользователей')
			->callout_warning('Условия работы', [
				'Для работы фильтра необходим хотя бы один <b>where()</b> у которого НЕ будет указан ТРЕТИЙ параметр.',
				'Второй параметр, также не является обязательным и по умолчанию имеет значение \'=\'.',
				'При этом это значение будет автоматически измененно на IS, если значение будет NULL (аналогично для \'!=\').',
				'Если такого <b>where()</b> не было указано, то следует выполнить применение фильтра в ручном режиме.',
			])
			->example('switch ($role = $filter->get_value(\'role\')) {
	case \'login\':
		$this->builder->where(\'role.name\', \'=\', $role);
		break;
	...
}', 'Обработка значений фильтра в ручном режиме');

		$doc->heading2('Компоненты');

		$doc->heading3('Force_Filter_Input')
			->example(
				'Force_Filter_Input::factory($name, $label = \'\');',
				Force_Filter_Input::factory('name', 'input'));

		$doc->heading3('Force_Filter_Select')
			->example(
				'Force_Filter_Select::factory($name, $label = \'\', array $values = array(), $add_placeholder = true)',
				Force_Filter_Select::factory('name', 'select', array(
					'условие 1',
					'условие 2',
				))->multiple()->multiple_reset_button())
			->callout_info('$add_placeholder', [
				'Если указано значение true, то к имеющимся выборам, будет добавлен ещё один в начало.',
				'Его значение равно 0, а отображается он как \'---\'.',
				'При этом в <b>условия обработки значений</b> (см. ниже) добавляется условие \'!=0\'.',
			]);

		$doc->heading3('Force_Filter_Date')
			->example(
				'Force_Filter_Date::factory($name, $label = \'\');',
				Force_Filter_Date::factory('name', 'date')
			);

		$doc->heading2('Условия фильтра (conditions)')
			->text([
				'Каждый компонент наследуется от класса Force_Filter_Conditions, который хранит в себе все методы задающие условия выборки.',
				'Получить доступ к этим методам можно напрямую из компонента.',
			])
			->example('Force_Filter_Select::factory(\'role\', \'по привелегиям\', Core_User_Role::factory()->get_list_as_array())
	->join_on(\'roles_users\', \'users . id\', \'=\', \'roles_users . user_id\')
	->where(\'roles_users . role_id\');'
			);

		$doc->heading3('Where')
			->example('where($column, $op = \'=\', $value = \'\')')
			->callout_info('$column', [
				'Поле, по которому будет произведена выборка.',
			])
			->callout_danger('Указывайте имена таблиц', [
				'Названия полей необходимо указывать с названиями таблиц, причем именно таблиц, а не моделей Jelly - это позволит избежать банальных ошибок.',
			])
			->callout_info('$op', [
				'Опереатор сравнения.',
				'Если вдруг так получится, что \'op\' указан \'=\', а значение придет NULL, то пугаться не надо Jelly сама преобразует \'=\' в \'IS\', и аналогично \'!=\' в \'IS NOT\'.',
			])->callout_info('$value', [
				'Если указано, то используется указанное, в противном случае значение берется из поля формы фильтра, т.е. должен быть хотя бы один \'where\', где параметр \'value\' не определен.',
			])->example('Force_Filter_Input::factory(\'param\', \'label\')
	->where(\'id\', \'NOT IN\', DB::expr(\'(SELECT id FROM table2 WHERE field = `:value`)\'));', [
				'<b>Database_Expression</b>',
				'В $value можно передать объект класса Database_Expression, возвращаемый, например, функцией DB::expr().',
				'Причём, если в описании указать строку \':value\', то она будет заменена значением из фильтра.',
			])->example('Force_Filter_Input::factory(\'param\', \'label\')
	->where([
		\'field_1\',
		\'field_2\',
		...
		\'field_N\',
	]);', 'Фильтр по нескольким полям одновременно');

		$doc->heading3('Join')
			->example('join_on($table, $c1, $op, $c2, $join_type = NULL)')
			->callout_info('$table', 'Название модели в Jelly или имя таблицы.')
			->callout_info('$c1, $op и $c2', 'Реализуют конструкцию ON из SQL JOIN.')
			->example('JOIN table1 ON table1.column operator table2.column')
			->callout_info('$join_type', 'Тип связи, определяется языком SQL (LEFT, RIGHT, INNER, и т.д.).')
			->callout_warning('Указывайте имена таблиц', 'Названия полей крайне желательно указывать с названиями таблиц, причем именно таблиц, а не моделей Jelly - это позволит избежать банальных ошибок.');

		$doc->heading3('Другие варианты условий')
			->text([
				'Остальные условия работают также как и в database builder. Полный их перечень можно найти в классе Force_Filter_Conditions.',
			]);

		$doc->heading2('Условия обработки значений Value Rules')
			->text([
				'Указываются для компонента Force_Filter_Select и позволяют управлять значениями списка.',
				'Фактически данная конструкция призвана отсечь ряд значений списка.',
				'Но также есть способ и преобразовать одно значение в другое. Задаётся в виде массива.',
			])
			->example(
				'Force_Filter_Select::factory(\'param\', \'label\', array(
	0 => \'---\',
	1 => \'условие 1\',
	2 => \'условие 2\',
))->set_value_rules(array(
	\'!=\' => 0,
), false)', 'Часто первым элементом выпадающего списка является \'---\' со значением \'0\'. Его можно обойти следующим образом:'
			);

		$doc->heading3('Операции сравнения')
			->text([
				'Ключами массива являются операторы сравнения, а в значениях указывается с чем сравнивать значение формы.',
				'Строгие операторы сравнения не используются, так как все приходящие из формы значения являются строками.',
			])
			->example('array(
	\'>\' => 0,
	\'>=\' => 1,
	\'!=\' => \'default\',
	\'==\' => 2,
	\'<=\' => 3,
	\'<\' => 4
)');

		$doc->heading3('Ограничение по типу')
			->text([
				'Поскольку все поступающие значения идут в виде строки, есть только один актуальный способ проверить значение по типу - не является ли строка числом.',
			])->example('array(
	\'is_numeric\' => true, //значение фильтра должно быть числом
	\'is_numeric\' => false, //значение фильтра не должно быть числом
)'
			)
			->callout_info('Обработка условий', [
				'Все операции сравнения, включая is_numeric выполняются последовательно сверху вниз, как были указаны, до первого же несоответствия.',
				'Чтобы условие фильтра попало в запрос необходимо, чтобы оно прошло ВСЕ проверки указанные в set_value_rules.',
			]);

		$doc->heading3('Сравнение с NULL')
			->example('array(
	\'==\' => NULL,
)', 'Будет выполнено как is_null()'
			)
			->example('array(
	\'!=\' => NULL,
)', 'Будет выполнено как !is_null()'
			);

		$doc->heading3('Замена значений')
			->text('Кроме операций сравнения можно производить и замены.');

		$doc->example('array(
	\'=\' => array(
		\'default\' => NULL,
	),
)',
			[
				'Значение из формы фильтра можно изменить на более подходящее для SQL-запроса.',
				'В данном примере значение \'default\' будет изменено на NULL.',
			])
			->callout_info('Приоритет присвоения', [
				'Операция присвоения всегда выполняется ДО операций сравнения.',
			])
			->example('array(
	\'!=\' => NULL,
	\'=\' => array(
		\'no_link\' => NULL,
	),
)', 'Если в указанном ниже примере из формы фильтра придёт значение \'no_link\', то фильтр не будет выполнен, потому что сначала значение \'no_link\' будет приведено к NULL, а только потом уже выполнена операция != NULL.');

		$doc->heading2('Упрощённый формат указания полей')
			->text('Создаёт только элементы типа input.');

		$doc->example('$builder = Core_Role_User::factory()->get_builder();

$filter = Force_Filter::factory(array(
	\'id\',
	\'name\' => \'LIKE\',
))->apply($builder);
...
$this->template->content[] = $filter->render();', 'Пример указания');
		$doc->example('Force_Filter_Input::factory($name)->where($field, $op)',
			'Упрощённый формат фактически является аналогом следующей конструкции:');
		$doc->example('array(
	\'id\',
)
...
array(
	\'id\' => \'=\',
)',
			'Если оператор не указан, то по умолчанию он будет равен \'=\'. Таким образом следующие два определения идентичны.');

		$doc->text([
			'Не смотря на то, что имя поля может быть как ключём так и значением массива, при разборе набора полей все поля определяются корректно.',
			'Формат указания операторов аналогичен значениям принимаемым конструкцией where класса Kohana_Database_Query_Builder_Where.',
		]);

		$this->template->content[] = $doc->render();
	}

} // End Controller_Developer_Documentation_Filter