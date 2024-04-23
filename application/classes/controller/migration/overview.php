<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Overview
 * User: legion
 * Date: 10.04.18
 * Time: 13:31
 */
class Controller_Migration_Overview extends Controller_Migration_Template {

	public function action_index() {
		Helper_Assets::add_styles('assets/common/css/bootstrap-docs.min.css');

		$doc = Force_Documentation::factory();

		$doc->heading1('Migration Zone');
		$doc->callout_danger('Внимание!!!', array(
			'Вы вторгаетесь в зону повышенной опасности!',
			'Выполняйте свои действия обдуманно и осторожно!',
		));

		$doc->callout_info('Меню', array(
			'Все миграции автоматически добавляются в меню сканированием файлов.',
		));

		$doc->callout_info('Как сделать?', array(
			'Для того чтобы узнать как правильно составить миграцию смотрите код миграций из раздела Example.',
		));

		$this->template->content = $doc->render();
	}

} // End Controller_Migration_Overview