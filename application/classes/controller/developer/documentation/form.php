<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Developer_Documentation_Form
 * User: legion
 * Date: 15.07.14
 * Time: 15:31
 */
class Controller_Developer_Documentation_Form extends Controller_Developer_Documentation_Template {

	public function action_index() {
		$doc = Force_Documentation::factory()->show_menu();

		$doc->heading1('Force_Form')
			->heading2('Подключение формы');

		$doc->heading2('Компоненты типа INPUT');

		$doc->heading3('Force_Form_Input')
			->example('Force_Form_Input::factory($name = null, $label = null, $value = null)')
			->text([
				'Самый простой компонент формы для обработки произвольной строки.',
				'У ряда перечисленных далее компонентов factory выглядит аналогичным образом.',
			]);
		$doc->heading3('Force_Form_Alias')
			->text([
				'Преобразует введённый текст в текст приемлемый для вставки в URL.',
				'Ограничивает ввод данных',
			]);
		$doc->heading3('Force_Form_Float')
			->text('Решает проблему конвертации чисел с плавающей запятой из формата принятого в HTML, в корректный формат для PHP.');
		$doc->heading3('Force_Form_Tags');

		$doc->heading3('Force_Form_Checkbox');
		$doc->heading3('Force_Form_Radio');

		$doc->heading2('Работа с датой');
		$doc->heading3('Force_Form_Date');
		$doc->heading3('Force_Form_Date_Range')
			->text('Скорей всего будет поглощён компонентом Force_Form_Date');

		$doc->heading2('Загрузка файлов');
		$doc->heading3('Force_Form_Image')
			->text('Ожидается наследование от Force_Form_File с сопутствующим глубоким перепилом содержимого.');
		$doc->heading3('Force_Form_File')
			->example('Force_Form_File::factory($name = null, $label = null, $file_type = null, $file_name = null)')
			->text([
				'Компонент для загрузки файлов.'
			])
			->callout_info('$file_type', [
				'Обработка загружаемых файлов осуществляется с использованием конфигурационного файла <b>config/files</b>.',
				'file_type - это ключ для массива настроек в конфигурационном файле.',
			]);

		$doc->heading2('Обработка текста');
		$doc->heading3('Force_Form_Textarea');
		$doc->heading3('Force_Form_Markdown');
		$doc->heading3('Force_Form_Redactor');

		$doc->heading2('Нестандартные компоненты');
		$doc->heading3('Force_Form_ManyToMany');

		$doc->heading3('Force_Form_Combine')
			->example('Force_Form_Combine::factory($name = null, $label = null, array $controls = array(), $value = null)')
			->text([
				'Пожалуй один из самых сложных компонентов формы.',
				'Предоставляет возможность используя другие компоненты формы сформировать JSON из данных обработанных этими компонентами.',
				'Несовместим с рядом компонентов. При некорректном подключении бросает HTTP_Exception_500 с пояснениями.',
			])
			->callout_info('Наследник класса Force_Form_Control', [
				'Несмотря на то, что компонент принимает в себя массив других компонентов, он не является потомком класса Force_Form_Container, а наследуется от Force_Form_Control и с точки зрения формы ведёт себя как обычный компонент формы.',
			])
			->callout_info('Клонирование', 'Для создания новых экземпляров компонентов, используются клоны переданных компонентов. Таким образом, все параметры переданных компонентов (кроме id и name) будут и у всех сгенерированных в ходе работы пользователя с формой.');

		$doc->heading3('Force_Form_HTML')
			->example('Force_Form_HTML::factory($html = null)')
			->text([
				'Позволяет вставить в форму любой HTML, например, так можно вставить в форму Force_List',
			]);

		$doc->heading2('Компоненты только для чтения');

		$doc->heading3('Force_Form_Note');
		$doc->heading3('Force_Show_Image');
		$doc->heading3('Force_Show_Value');

		$doc->heading2('Jelly_Form')
			->text([
				'Надстройка над Force_Form. Использует Jelly_Form_Generator для создания и обновления элементов формы.',
			])
			->callout_warning('"Всё или ничего"', [
				'Не требует указания никаких компонентов, чтобы полностью сгенерировать форму из модели.',
				'В тоже время, если хотя бы один компонент был указан, значит и все остальные должны быть указаны вручную.',
			]);

		$doc->callout_warning('Статья ещё не закончена', 'Ожидайте...');


		$this->template->content = $doc->render();
	}

} // End Controller_Developer_Documentation_Form