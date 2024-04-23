<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Bullets
 * User: legion
 * Date: 06.07.12
 * Time: 3:19
 */
class Controller_Migration_Bullets extends Controller_Migration_Template {

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
		$count_calibres = Core_Calibre::factory()->get_builder()
			->where('bullet_weight', 'IS NOT', NULL)
			->count();
		$count_bullets = Core_Bullet::factory()->get_count();

		$count = $count_calibres - $count_bullets;
		if ($count < 0) {
			$count = 0;
		}
		return $count;
	}

	/*
	 * Это основной action миграции - шаг миграции.
	 * В одном шаге может быть произведено несколько действий.
	 * Для работы миграции достаточно наличия двух методов, этого и get_count() объявленного выше.
	 * Для примера миграции с минимальными объявлениями см. миграцию tagsclean
	 */
	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$calibres = Core_Calibre::factory()->get_builder()
			->where('bullet_weight', 'IS NOT', NULL)
			->select_all()
			->as_array('ubCalibre', 'ubCalibre');

		$bullets = Core_Bullet::factory()->get_list()->as_array('ubCalibre', 'ubCalibre');

		$ubCalibre = NULL;

		foreach ($calibres as $calibre) {
			if (!array_key_exists($calibre, $bullets)) {
				$ubCalibre = $calibre;
				break;
			}
		}

		$calibre = Core_Calibre::factory()->get_builder()
			->where('ubCalibre', '=', $ubCalibre)
			->limit(1)
			->select();

		try {
			$model = Core_Bullet::factory()->create();
			$model->ubCalibre = $calibre->ubCalibre;
			$model->test_barrel_length = $calibre->test_barrel_length;

			$bullet_name = $calibre->bullet_name;

			if (empty($bullet_name)) {
				$bullet_name = 'Default';
			}

			$model->bullet_name = $bullet_name;

			$model->bullet_weight = $calibre->bullet_weight;
			$model->bullet_weight_gran = $calibre->bullet_weight_gran;
			$model->bullet_start_speed = $calibre->bullet_start_speed;
			$model->bullet_start_energy = $calibre->bullet_start_energy;
			$model->pellet_weight = $calibre->pellet_weight;
			$model->pellet_number = $calibre->pellet_number;
			$model->bullet_coefficient = $calibre->bullet_coefficient;
			$model->save();
		} catch (Exception $e) {
			$this->migration->send_error($e->getMessage());
		}

		$changes = (int)$model->saved();

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

} // End Controller_Migration_Bullets