<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: JA2_Item_Stances
 * User: legion
 * Date: 19.07.2020
 * Time: 7:06
 * Класс создан чтобы запретить прямой доступ к stances
 */
class JA2_Item_Stances {

	private $stances = array();

	public function get_stances() {
		return $this->stances;
	}

	/**
	 * @param $field
	 * @param null $json
	 * @return JA2_Stance
	 */
	protected function stance($field, $json = null) {
		if (!array_key_exists($field, $this->stances)) {
			$this->stances[$field] = JA2_Stance::factory($field, $json);
		}
		if (!is_null($json)) {
			$this->stances[$field]->set_json($json);
		}
		return $this->stances[$field];
	}

} // End JA2_Item_Stances
