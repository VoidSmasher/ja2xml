<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Help
 * User: legion
 * Date: 29.09.19
 * Time: 4:42
 */
class Controller_Admin_Help extends Controller_Admin_Template {

	public function action_index() {
		Helper_Assets::add_styles('assets/common/css/bootstrap-docs.min.css');

		$doc = Force_Documentation::factory();

		$doc->heading1('JA2 mod, напоминалка');
		$doc->callout_danger('Таблицы', array(
			'Таблицы <b>items</b>, <b>weapons</b>, <b>attachments</b>, <b>incompatible</b> не меняются при сохранении данных.',
			'Вся работа идёт ТОЛЬКО с таблицами _mod. Если сохранил и нужно вернуть, не надо делать импорт данных.',
			'Достаточно просто удалить таблицу _mod и склонировать нужную таблицу в _mod.',
		));

		$doc->callout_warning('Data', array(
			'Таблицы начинающиеся с префикса data_ служат для сбора внешних данных.',
			'Работа построена так, что можно начисто сносить таблицу _mod и клонировать её заново из оригинальной.',
			'Данные мода не будут утеряны и их можно будет заново накатить поверх таблицы _mod кнопкой "Сохранить" в соответствующем разделе.',
			'Здесь важно учесть, что при сносе таблицы <b>items_mod</b> придётся пройти по нескольким разделам для восстановления данных мода.',
			'А именно: <a href="/admin/data_attachments">Attachments Data</a>, <a href="/admin/attachments">Attachments</a>, <a href="/admin/items">Items</a>, <a href="/admin/lbe">Load Bearing Equipment</a>.',
			'Данные в Attachments Data должны быть сохранены прежде чем будут сохранены данные в Items иначе придётся возвращаться и сохранять Items повторно.'
		));

		$doc->callout_info('Импорт/Экспорт', array(
			'Импорт и экспорт данных сделаны посредством <a href="/migration">миграций</a>.',
			'Оригинальные XML файлы лежат в папке original в корне проекта.',
			'По факту импорт делается только один раз.',
			'Экспорт данных производится в папку htdocs/uploads.',
		));

		$this->template->content = $doc->render();
	}

} // End Controller_Admin_Help