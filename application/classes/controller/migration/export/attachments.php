<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Migration_Export_Attachments
 * User: legion
 * Date: 24.10.17
 * Time: 9:17
 */
class Controller_Migration_Export_Attachments extends Controller_Migration_Template {

	protected $migration_title = 'Экспорт данных в Attachments.xml';

	const FILE_PATH = 'htdocs/uploads/Items/Attachments.xml';

	public function get_count() {
		return 1;
	}

	public function action_index() {
		$filename = APPPATH . '../' . self::FILE_PATH;
		$this->migration->description('Пишет в файл: ' . $filename);
		$this->migration->description('Если такой файл уже существует, он будет создан заново.');
		parent::action_index();
	}

	public function action_json_process() {
		$this->migration->check_count($this->get_count());

		$filename = APPPATH . '../' . self::FILE_PATH;

		$changes = 0;

		try {
			if (file_exists($filename)) {
				unlink($filename);
			}

			$contents = '<?xml version="1.0" encoding="utf-8"?>
<ATTACHMENTLIST>
</ATTACHMENTLIST>';

			$xml = simplexml_load_string($contents);

			$list = Core_Attachment_Mod::factory()
				->order_by('attachmentIndex', 'ASC')
				->order_by('itemIndex', 'ASC')
				->get_list()->as_array();

			foreach ($list as $model) {
				$weapon = $xml->addChild('ATTACHMENT');

				foreach ($model as $field => $value) {
					if ($field == 'id') {
						continue;
					}
					if (is_float($value)) {
						$value = number_format($value, 2, '.', '');
					} else {
						$value = htmlspecialchars($value);
					}
					$weapon->addChild($field, $value);
				}

				$changes++;
			}

			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			$dom->save($filename);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__);
			$this->migration->send_error($e->getMessage());
		}

		$this->migration->set_changes_count($changes);

		$this->migration->send_result();
	}

}