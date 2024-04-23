<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Example_Tags
 * User: legion
 * Date: 06.07.12
 * Time: 3:19
 */
class Controller_Migration_Example_Tags extends Controller_Migration_Template {

	protected $migration_title = 'Добавить числовые теги';
	protected $migration_description = [
		'ВНИМАНИЕ!',
		'Эта миграция выполняет реальные действия над базой данных!',
		'Будут добавлены числовые теги новостей: от 1 до 1000.',
	];

	public function get_count() {
		/*
		 * Это пример миграции с фиксированным счётчиком.
		 * Допустим нужно выполнить одну операцию 1000 раз.
		 * Т.е. требуется 1000 действий.
		 *
		 * Для примера миграции с вычисляемым счётчиком, см. миграцию tagsclean
		 */
		$count = 1000;
		return $count;
	}

	/*
	 * action_json_start вызывается перед тем, как будет вызван action_json_process.
	 * action_json_start нужен для начальной подготовки, инициализации и т.п.
	 * Например здесь можно добавить в таблицу поля нужные для миграции.
	 * Здесь вызываем метод start миграции.
	 * Наличие этого action не обазательно, именно в таком виде он лежит в Controller_Migration_Template
	 */
	public function action_json_start() {
		$this->migration->start($this->get_count());
	}

	/*
	 * Это основной action миграции - шаг миграции.
	 * В одном шаге может быть произведено несколько действий.
	 * Для работы миграции достаточно наличия двух методов, этого и get_count() объявленного выше.
	 * Для примера миграции с минимальными объявлениями см. миграцию tagsclean
	 */
	public function action_json_process() {
		/*
		 * Получаем количество незавершённых действий.
		 * В данном примере идёт работа с внутренним счётчиком в Helper_Migration, который хранится в сессии.
		 */
		$count = $this->migration->get_count_of_undone_items();

		/*
		 * Проверяем количество действий.
		 * Если таковых не осталось, то миграция будет остановлена.
		 * В нашем случае у нас требуется 1000 действий.
		 */
		$this->migration->check_count($count);

		/*
		 * Если необходимо, то в любой момент мы можем вывести сообщение в миграцию.
		 * Всего существует пять типов сообщений:
		 * - message() - сообщение на белом фоне
		 * - message_info() - на голубом фоне
		 * - message_warning() - на жёлтом фоне
		 * - message_danger() - на красном фоне
		 * - message_success() - на зелёном фоне
		 * Например, будем выводить сообщение сколько действий осталось, на каждые 100 изменений.
		 */
		$remain = $count % 100;
		if ($remain == 0 && $count != 1000) {
			$this->migration->message_info("Осталось {$count} изменений");
		}

		/*
		 * Выполняем необходимые действия над выбранными элементами.
		 * Выполненные действия суммируем.
		 */
		$changes = 0;
		$result = Core_Tag::factory()->create()
			->set('title', $count);

		try {
			$result->save();
		} catch (Exception $e) {
			/*
			 * Если произошла ошибка, её можно отобразить методом send_error().
			 * Второй параметр этого метода отвечает за остановку миграции.
			 * Ошибки нужно сообщать именно через send_error() - так миграция будет знать,
			 * что в ходе её выполнения были ошибки.
			 */
			$this->migration->send_error($e->getMessage());
		}
		$changes += (int)$result->saved();

		/*
		 * Сообщаем сколько действий было выполнено.
		 */
		$this->migration->set_changes_count($changes);

		/*
		 * Отправляем результат работы.
		 * При необходимости здесь же можно остановить миграцию.
		 */
		$this->migration->send_result();
	}

	/*
	 * action_json_finish нужен для завершения миграции
	 * Например здесь можно удалить из таблицу поля которые были нужны для этой миграции
	 * Здесь вызываем метод stop миграции
	 * Наличие этого action не обазательно, именно в таком виде он лежит в Controller_Migration_Template
	 */
	public function action_json_finish() {
		$this->migration->stop();
	}

} // End Controller_Migration_Example_Tags