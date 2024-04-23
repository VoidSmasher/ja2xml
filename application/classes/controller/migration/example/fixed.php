<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Example_Fixed
 * User: legion
 * Date: 06.07.12
 * Time: 3:19
 */
class Controller_Migration_Example_Fixed extends Controller_Migration_Template {

	/*
	 * Это основная страница миграции.
	 * Здесь можно настроить вид миграции, например добавить объяснение что это миграция делает.
	 * Наличие этого action не обазательно, в упрощённом виде он лежит в Controller_Migration_Template
	 */
	public function action_index() {
		$this->migration->title('Миграция с фиксированным шагом');
		$this->migration->description('Пример миграции с фиксированным шагом.');
		$this->migration->description('Смотрите комментарии в коде миграции.');
		parent::action_index();
	}

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
	 * action_json_start вызывается перед тем, как будет вызван первый action_json_process.
	 * Для начальной подготовки, инициализации и т.п.
	 */
	public function action_json_start() {
		/*
		 * Sleep здесь не нужен, он лишь симулирует работу скрипта, который призван начать работу миграции.
		 * Например, добавить временные поля в базу, которые будут использованы во время работы миграции.
		 */
		sleep(3);
		$this->migration->start($this->get_count());
	}

	/*
	 * action_json_process - основной процесс миграции.
	 * Именно здесь и происходит выполнение миграции данных.
	 * Каждый вызов action_json_process выполняет ОДИН ШАГ миграции, пока не будет выполнен get_count() ДЕЙСТВИЙ.
	 *
	 * Один шаг может выполнять несколько действий.
	 *
	 * Например, можно перебирать таблицу базы данных не по одной записи, а сразу по 10.
	 * Таким образом, количество пройденных шагов может не совпадать с количеством выполненных действий.
	 */
	public function action_json_process() {
		/*
		 * Получаем количество незавершённых изменений.
		 * В данном примере идёт работа с внутренним $count в Helper_Migration, который хранится в сессии.
		 */
		$count = $this->migration->get_count_of_undone_items();

		/*
		 * Проверяем количество изменений.
		 * Если таковых не осталось, то останавливаем миграцию.
		 * Если $count будет равен null, то проверка будет идти по каунту миграции в сессии.
		 */
		$this->migration->check_count($count);

		/*
		 * Выполняем необходимые действия над выбранными элементами.
		 * Выполненные действия суммируем.
		 */
		$changes = 0;
		for ($i = 1; $i < 10; $i++) {
			/*
			 * Какие-то действия могут быть успешными, какие-то нет.
			 */
			$changes += rand(0, 1);
		}

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
	 * action_json_finish вызывается после последнего вызова action_json_process.
	 */
	public function action_json_finish() {
		/*
		 * Sleep здесь не нужен, он лишь симулирует работу скрипта, который призван завершить работу миграции.
		 * Например, удалить временные поля из базы, которые использовались во время работы миграции.
		 */
		sleep(3);
		$this->migration->stop();
	}

} // End Controller_Migration_Example_Fixed