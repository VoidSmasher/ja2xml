<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Tags
 * User: legion
 * Date: 14.11.14
 * Time: 18:26
 */
class Force_Form_Tags extends Force_Form_Input {

	protected $_icon_class = 'fa-battery-0';
	protected $_allow_combine = false;

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_before_save(Force_Form_Core $form, Jelly_Model $model) {
		return false;
	}

	/*
	 * !!! Возвращаемое значение определяет нужно ли пересохранять модель или нет
	 */
	public function apply_after_save(Force_Form_Core $form, Jelly_Model $model) {
		$name = $this->get_name();
		$value = $form->get_value($name);
		$tags = Core_Tag::add_tags($value);
		$model->set($name, $tags);
		return true;
	}

} // End Force_Form_Tags
