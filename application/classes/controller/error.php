<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class: Controller_Error
 * User: legion
 * Date: 14.11.17
 * Time: 18:31
 */
class Controller_Error extends Controller_Error_Layout {

	public function action_index() {
		$code = $this->request->param('code');
		$this->_ajax = false;
		$this->request
			->response()
			->status($code);

		$title = i18n::get_default('error.' . $code . '.title', $code);
		$text = Arr::get(Response::$messages, $code);
		$text = i18n::get_default('error.' . $code . '.text', $text);

		$this->template->title = $title;
		$this->template->text = $text;
		$this->template->code = $code;
		$this->template->show_back_to_main = ($code != 500);
	}

} // End Controller_Error
