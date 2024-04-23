<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Overview
 * User: legion
 * Date: 15.07.14
 * Time: 18:08
 */
class Controller_Developer_Overview extends Controller_Developer_Documentation_Template {

	public function action_index() {
		Helper_Assets::add_styles('assets/common/css/bootstrap-docs.min.css');

		$doc = Force_Documentation::factory();

		$doc->heading1('Developer Zone');
		$doc->callout_danger('Внимание!!!', array(
			'Вы вторгаетесь в зону повышенной опасности!',
			'Выполняйте свои действия обдуманно и осторожно!',
		));

		$this->template->content = $doc->render();
	}

} // End Controller_Developer_Overview