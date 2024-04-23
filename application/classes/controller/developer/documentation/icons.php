<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Documentation_Icons
 * User: legion
 * Date: 15.07.14
 * Time: 18:08
 */
class Controller_Developer_Documentation_Icons extends Controller_Developer_Documentation_Template {

	public function action_index() {
		$doc = Force_Documentation::factory();

		$doc->heading1('Icons')->text('В коробку подключено два типа иконок:');
		$doc->ul(array(
			HTML::anchor('http://getbootstrap.com/components/#glyphicons', 'Bootstrap Glyphicons', array('target' => '_blank')),
			HTML::anchor('http://fortawesome.github.io/Font-Awesome/icons', 'Font Awesome Icons', array('target' => '_blank')),
		));

		$doc->heading2('Использование иконок в меню')
			->text('В меню можно использовать как тот так и другой тип иконок. Причём, для упрощения можно опускать указание общего класса.');

		$doc->example("'icon' => 'glyphicon-list'", 'Можно опустить класс glyphicon. При обработке menu генератор для всех иконок начинающихся с glyphicon- самостоятельно добавит класс glyphicon.');
		$doc->example("'icon' => 'fa-th'", 'Можно опустить класс fa. При обработке menu генератор для всех иконок начинающихся с fa- самостоятельно добавит класс fa.');

		$doc->text('Результат можно рассмотреть на "живом" примере - верхнее меню этого раздела.');

		$this->template->content = $doc->render();
	}

} // End Controller_Developer_Documentation_Icons