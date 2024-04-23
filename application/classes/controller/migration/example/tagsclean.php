<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Example_TagsClean
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Example_TagsClean extends Controller_Migration_Template {

	protected $migration_title = 'Удалить числовые теги';
	protected $migration_description = [
		'ВНИМАНИЕ!',
		'Эта миграция выполняет реальные действия над базой данных!',
		'Будут удлены все числовые теги новостей.',
	];

	public function get_count() {
		/*
		 * Это пример миграции с вычисляемым счётчиком.
		 * Здесь для определения количества операций выполняется запрос к базе данных.
		 * Для миграции с фиксированным счётчиком см. миграции tags и fixed.
		 */
		return Core_Tag::factory()->get_builder()
			->where('title', 'REGEXP', '[0-9]')
			->count();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		/*
		 * Поскольку эта миграция очень простая, она выполняется в один шаг.
		 * Сразу стираются все записи удовлетворяющие условию.
		 *
		 * Использование одношаговых миграций также оправдано, поскольку они запускаются в стандартном режиме,
		 * снабжаются комментариями и позволяют без нервов ходить по страницам миграции, не боясь, что что-то
		 * будет сразу же выполнено.
		 */
		$changes = 0;

		try {
			$changes = Core_Tag::factory()->get_builder()
				->where('title', 'REGEXP', '[0-9]')
				->delete();
		} catch (Exception $e) {
			$this->migration->send_error($e->getMessage());
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Example_TagsClean