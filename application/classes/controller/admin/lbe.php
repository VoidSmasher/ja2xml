<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_LBE
 * User: legion
 * Date: 14.11.19
 * Time: 23:31
 */
class Controller_Admin_LBE extends Controller_Admin_Template {

	public function action_index() {
		$builder = Core_Item_Mod::factory()->preset_for_admin()->get_builder()
			->join('items', 'LEFT')->on('items.uiIndex', '=', 'items_mod.uiIndex')
			->join('load_bearing_equipment', 'LEFT')->on('load_bearing_equipment.lbeIndex', '=', 'items_mod.ubClassIndex')
			->where('usItemClass', '=', Core_LBE::ITEM_CLASS_LBE)
			->select_column('items_mod.*')
			->select_column('items.szItemName', 'item_szItemName')
			->select_column('items.szLongItemName', 'item_szLongItemName')
			->select_column('items.szItemDesc', 'item_szItemDesc')
			->select_column('items.szBRDesc', 'item_szBRDesc')
			->select_column('load_bearing_equipment.lbeAvailableVolume', 'lbeAvailableVolume')
			->select_column('load_bearing_equipment.lbePocketIndex1', 'lbePocketIndex1')
			->select_column('load_bearing_equipment.lbePocketIndex2', 'lbePocketIndex2')
			->select_column('load_bearing_equipment.lbePocketIndex3', 'lbePocketIndex3')
			->select_column('load_bearing_equipment.lbePocketIndex4', 'lbePocketIndex4')
			->select_column('load_bearing_equipment.lbePocketIndex5', 'lbePocketIndex5')
			->select_column('load_bearing_equipment.lbePocketIndex6', 'lbePocketIndex6')
			->select_column('load_bearing_equipment.lbePocketIndex7', 'lbePocketIndex7')
			->select_column('load_bearing_equipment.lbePocketIndex8', 'lbePocketIndex8')
			->select_column('load_bearing_equipment.lbePocketIndex9', 'lbePocketIndex9')
			->select_column('load_bearing_equipment.lbePocketIndex10', 'lbePocketIndex10')
			->select_column('load_bearing_equipment.lbePocketIndex11', 'lbePocketIndex11')
			->select_column('load_bearing_equipment.lbePocketIndex12', 'lbePocketIndex12')
			->select_column('load_bearing_equipment.lbeClass', 'lbeClass')
			->select_column('load_bearing_equipment.lbeCombo', 'lbeCombo');

		$filter = Force_Filter::factory(array())->apply($builder);

		$pockets = Core_Pocket::factory()->get_list()->as_array('pIndex', 'pVolume');

		$this->_save($builder, $pockets);

		$list = Force_List::factory()->preset_for_admin()
			->title('Load Bearing Equipment');

		$list->column('uiIndex');
		$list->column('szLongItemName');
		$list->column('AttachmentClass');
		$list->column('nasAttachmentClass');
		$list->column('lbeAvailableVolume');
		$list->column('pVolume');

		$button_save = Force_Button::factory(__('common.save'))
			->submit()
			->btn_danger()
			->confirmation('Save data?');

		$save = Force_Form::factory([
			Force_Form_Hidden::factory('action', 'save'),
		])->button_submit($button_save);

		$list->button_html($save->render());

		$list->apply($builder, null, false)
			->each(function (Jelly_Model $model, array $pockets) {

				if ($model->lbeAvailableVolume == 0) {
					$model->format('lbeAvailableVolume', '');
				}

				if ($model->nasAttachmentClass) {
					$volume = $this->get_volume($model, $pockets);

					if ($volume) {
						$model->pVolume = $volume;
					}
				}

				$labels = array(
					'AttachmentClass' => Core_Item::get_AttachmentClass_label($model->AttachmentClass),
					'nasAttachmentClass' => Core_Item::get_nasAttachmentClass_label($model->nasAttachmentClass),
				);

				foreach ($labels as $label_field => $label) {
					if ($label instanceof Force_Label) {
						$model->format($label_field, $label->render());
					}
				}
			}, $pockets);

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	protected function get_volume(Jelly_Model $model, array $pockets) {
		$volume = 0;
		for ($i=1; $i<=12; $i++) {
			$lbePocketIndex = $model->{'lbePocketIndex' . $i};
			if ($lbePocketIndex > 0) {
				$volume += Arr::get($pockets, $lbePocketIndex, 0);
			}
		}
		return $volume;
	}

	/*
	 * SAVE
	 */

	protected function _save(Jelly_Builder $builder, array $pockets) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'save') {
			return false;
		}

		$items = $builder->select_all();

		foreach ($items as $model) {
			if (strpos($model->szLongItemName, 'NIV Default') !== false) {
				continue;
			}

			$str = array();

			if ($model->lbeAvailableVolume) {
				$str[] = 'Space: ' . $model->lbeAvailableVolume;
			}

			if ($model->nasAttachmentClass) {
				$volume = $this->get_volume($model, $pockets);

				if ($volume) {
					$model->szItemName = $model->item_szItemName . ' (' . $volume . ')';
					$model->szLongItemName = $model->item_szLongItemName . ' (' . $volume . ')';
					$str[] = 'Volume: ' . $volume;
				}
			}

			$str = implode(' ', $str);

			if (!empty($str)) {
				$str = ' ' . $str;
			}

			$model->szItemDesc = $model->item_szItemDesc . $str;
			$model->szBRDesc = $model->item_szBRDesc . $str;

			try {
				$model->save();
			} catch (Exception $e) {
				Log::exception($e, __CLASS__, __FUNCTION__);
			}
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

}