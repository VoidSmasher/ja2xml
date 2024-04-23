<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Admin_Weapons
 * User: legion
 * Date: 05.05.18
 * Time: 6:17
 */
class Controller_Admin_Weapons extends Controller_Admin_Template {

	use Controller_Common_Weapons_Accuracy;
	use Controller_Common_Weapons_Calibre;
	use Controller_Common_Weapons_Damage;
	use Controller_Common_Weapons_Data;
	use Controller_Common_Weapons_Range;
	use Controller_Common_Weapons_SP4T;
	use Controller_Common_Weapons_Velocity;
	use Controller_Common_List_Cell;
	use Controller_Common_List_Changes;

	public function action_index() {
		$this->_accuracy();
		$this->_damage();
		$this->_weapon_data();
		$this->_range();
		$this->_sp4t();
		$this->_velocity();

		$calibres = Core_Calibre::factory()->get_list()->as_array('ubCalibre', 'name');

		$this->_calibre($calibres);

		$builder = Core_Weapon_Mod::get_weapons_builder()
			->order_by('calibre_name')
			->order_by('length_barrel', 'DESC')
			->order_by('szWeaponName');

		$attachments = Core_Attachment_Data::get_attachments_list();
		$attachment_models = Core_Attachment_Data::get_attachments_list_of_models(true);

		$filter = Force_Filter::factory(array(
			Core_Weapon::get_filter_index()
				->where('weapons_mod.uiIndex'),
			Core_Calibre::get_filter_control()
				->where('weapons_mod.ubCalibre'),
			Force_Filter_Select::factory('weapon_type', 'Тип', Core_Weapon::get_type_list())
				->multiple()
				->multiple_reset_button()
				->where('weapons_mod.ubWeaponType'),
			Force_Filter_Select::factory('action', 'Механизм', Core_Weapon_Data::get_mechanism_action_list())
				->multiple()
				->multiple_reset_button()
				->where('data_weapons.mechanism_action'),
			Force_Filter_Select::factory('stock', 'Приклад', Core_Attachment_Data::get_stock_list(true))
				->multiple()
				->multiple_reset_button()
				->where('data_weapons.integrated_stock_index'),
			Core_Attachment_Data::get_filter_control_has_integrated(),
			Force_Filter_Select::factory('changes', 'Изменения', Helper_Form::get_boolean_options('yes', 'no')),
			Core_Weapon::get_filter_control_name()
				->where('weapons_mod.szWeaponName', 'LIKE'),
		))->apply($builder);

		Core_Weapon::check_filter_index($filter);
		Core_Calibre::check_filter_control($filter);
		Core_Weapon::check_filter_control_name($filter);
		Core_Attachment_Data::check_filter_control_has_integrated($filter, $builder);

		$filter_has_changes = $filter->get_value('changes');
		$filter_has_changes = ($filter_has_changes == 'yes');

		$collection = $builder->select_all();

		$list = Force_List::factory()->preset_for_admin();

		$list->button('Items', Force_URL::current()
			->controller('items')
			->get_url());
		$list->button('Data', Force_URL::current()
			->controller('data_weapons')
			->get_url());
		$list->button('Attachments', Force_URL::current()
			->controller('attachments_weapons')
			->get_url());

		/*
		 * SAVE
		 */
		if ($changes_count = $this->has_changes($collection, $attachment_models)) {
			$this->_save($builder);

			$button_save = Force_Button::factory(__('common.save'))
				->btn_danger()
				->confirmation('Save data?')
				->submit('#form-save')
				->popover('Changes', $changes_count, 'left');

			$this->template->content[] = Force_Form::factory([
				Force_Form_Hidden::factory('action', 'save'),
			])->attribute('id', 'form-save')->render();
		} else {
			$button_save = Force_Button::factory(__('common.save'))
				->btn_disabled();
		}
		$list->button_html($button_save->render());

		if (Form::is_post()) {
			Request::current()->redirect(Force_URL::current()->get_url());
		}

		/*
		 * LIST
		 */
		$popover_position = 'top';

		$list->column('uiIndex');
		$list->column('szWeaponName')->label('name');
		$list->column('weapon_image')->label('image');
		$list->column('ubWeaponClass')->label('class')->col_control();
		$list->column('ubWeaponType')->label('type')->col_control();
		$list->column('rarity');
		$list->column('calibre_name')->label('calibre');
		$list->column('ubMagSize')->label('Mag Size');
		$list->column('integrated_attachments')->label('Integrated Attachments');

		$list->column('mechanism_action')->label('action');
		$list->column('mechanism_trigger')->label('trigger');
		$list->column('mechanism_feature')->label('feature');
//		$list->column('calibre_damage')->label('Dam')->col_number();
//		$list->column('PercentNoiseReduction')->label('Percent Noise Reduction')->col_number();
//		$list->column('ubAttackVolume_calc')->label('Attack Volume Calc')->col_number();
//		$list->column('ubAttackVolume')->label('Attack Volume')->col_number();

		$list->column('ubDeadliness_bonus')->label('Deadli ness bns')->col_number();
		$list->column('ubDeadliness_new')->label('Deadli ness new')->col_number();
		$list->column('ubDeadliness')->label('Deadli ness old')->col_number();

		$list->column('nAccuracy_bonus')->label('Acc bns')->col_number();
		$list->column('nAccuracy_new')->label('Acc new')->col_number();
		$list->column('nAccuracy')->label('Acc old');
		$list->column('bAccuracy')->label('Acc bns')
			->popover('Accuracy bonus', 'This is an innate Chance-to-Hit Bonus (or penalty!) given by this gun due to its particular good (or bad) design.', $popover_position);

		$list->column('TwoHanded')->label('Two hand')->col_number();

		$list->column('ubImpact_new')->label('Dam new')->col_number();
		$list->column('ubImpact')->label('Dam old');

		$list->column('muzzle_velocity')->label('V0 real')->col_number();
		$list->column('bullet_speed')->label('V0 calc')->col_number();
		$list->column('bullet_weight')->label('BW')->col_number()
			->popover('Bullet weight', 'In grams.', $popover_position);

		$list->column('usRange_bonus')->label('Rng bns')->col_number();
		$list->column('usRange_new')->label('Rng new')->col_number();
		$list->column('usRange')->label('Rng old');

		$list->column('MaxDistForMessyDeath_bonus')->label('Rng crit bns')->col_number();
		$list->column('MaxDistForMessyDeath_new')->label('Rng crit new')->col_number();
		$list->column('MaxDistForMessyDeath')->label('Rng crit old');

		$list->column('weight_balance')->label('Bal ance')->col_number()
			->popover('Weapon balance', 'Front/Back', $popover_position);

		$list->column('Handling_bonus')->label('HD bns')->col_number();
		$list->column('Handling_new')->label('HD new')->col_number();
		$list->column('Handling')->label('HD old')->col_number();

		$list->column('ready_weight')->label('Rdy WT')->col_number()
			->popover('APs to Ready - Weight', 'Penalty for weapon weight.', $popover_position);
		$list->column('ready_weight_front')->label('Rdy WF')->col_number()
			->popover('APs to Ready - Weight Front', 'Penalty for weapon weight front.', $popover_position);
		$list->column('ready_length')->label('Rdy LN')->col_number()
			->popover('APs to Ready - Length', 'Penalty for weapon length.', $popover_position);

		$list->column('ubReadyTime_bonus')->label('Rdy bns')->col_number();
		$list->column('ubReadyTime_new')->label('Rdy new')->col_number();
		$list->column('ubReadyTime')->label('Rdy old');

		$list->column('mechanism_reload')->label('reload');
		$list->column('APsToReload_bonus')->label('Reload bns')->col_number();
		$list->column('APsToReload_new')->label('Reload new')->col_number();
		$list->column('APsToReload')->label('Reload old');

		$list->column('ubShotsPer4Turns_bonus')->label('Sp4T bns')->col_number();
		$list->column('ubShotsPer4Turns_new')->label('Sp4T new')->col_number();
		$list->column('ubShotsPer4Turns')->label('Sp4T old');

		$list->column('APsToReloadManually_bonus')->label('APTRM bns')->col_number();
		$list->column('APsToReloadManually_new')->label('APTRM new')->col_number();
		$list->column('APsToReloadManually')->label('APTRM old');

		$list->column('burst_length')->label('SpB new')->col_number();
		$list->column('ubShotsPerBurst')->label('SpB old');
		$list->column('bBurstAP_new')->label('Burst AP new')->col_number();
		$list->column('bBurstAP')->label('Burst AP old');

		$list->column('bAutofireShotsPerFiveAP_new')->label('AFSp 5AP new')->col_number();
		$list->column('bAutofireShotsPerFiveAP')->label('AFSp 5AP old');
		$list->column('fire_rate_auto')->label('Fire Rate')
			->popover('Fire Rate', 'Used to calculate full and burst auto.', $popover_position);
		$list->column('BR_ROF_new')->label('BR ROF new');
		$list->column('BR_ROF')->label('BR ROF old');

		$list->column('recoil')->label('Rec')->col_number()
			->popover('Recoil', 'Recoil calculated from weapon stats.', $popover_position);
		$list->column('bRecoilX_bonus')->label('Rec X bns')->col_number();
		$list->column('bRecoilX_new')->label('Rec X new')->col_number();
		$list->column('bRecoilX')->label('Rec X old');
		$list->column('bRecoilY_bonus')->label('Rec Y bns')->col_number();
		$list->column('bRecoilY_new')->label('Rec Y new')->col_number();
		$list->column('bRecoilY')->label('Rec Y old');

		$list->column('length_front')->label('length front')
			->popover('Front Length', 'Weapon length from muzzle to the middle of handle with trigger.', $popover_position);
		$list->column('height_diff_stock_barrel')->label('HDSB')
			->popover('Height Diff Stock Barrel', 'Difference in height between middle line of stock and middle line of a barrel. Higher values results in a higher impact on recoil.', $popover_position);
		$list->column('length_max')->label('Size Max')->col_number();
//		$list->column('length_min')->label('Size Min')->col_number();
		$list->column('length_barrel')->label('Ствол')->col_number()
			->popover(NULL, 'Длина ствола (мм)', $popover_position);
		$list->column('weight')->label('Вес')->col_number();

		$data = array();

		if ($filter_has_changes) {
			foreach ($collection as $index => $model) {
				if (array_key_exists($model->uiIndex, $this->changes)) {
					$data[$model->uiIndex] = $model;
				}
			}
			$list->apply($data, null, false);
		} else {
			$list->apply($builder, null, false);
		}

		$list->each(function (Model_Weapon_Group $model, array $attachments, array $attachment_models, array $calibres) {
			$row = Force_List_Row::factory();
			Bonus::clear();

//			$model->ubAttackVolume_calc = $model->get_attack_volume();

			/*
			 * BUTTON INDEX
			 */
			$button_index = Core_Weapon::button_index($model->uiIndex);

			$this->changes_for_index($model, $button_index);

			$model->format('uiIndex', $button_index->render());

			/*
			 * IMAGE
			 */
			Core_Item::row_image($model, $row, 'weapon_image');

			self::button_calibre($model, 'calibre_name', $calibres);

			$model->format('rarity', Core_Weapon_Data::get_rarity_label($model->rarity));

			$button_data = self::get_button_weapon_data($model, $model->szWeaponName, $model->weapon_name);
			$model->format('szWeaponName', $button_data->render());

			$button_two_handed = self::get_button_two_handed($model, $button_data);
			$model->format('TwoHanded', $button_two_handed->render());

			if (!empty($model->mag_size) && $model->mag_size != $model->ubMagSize) {
				$model->format('ubMagSize', $model->mag_size);
				$row->cell('ubMagSize')->attribute('style', 'color:red')
					->popover('Mag Size', Helper_String::to_string([
						'ORIGINAL: ' . $model->ubMagSize,
						'MOD: ' . $model->mag_size,
					], '<br/>'));
			}

			$model->format('integrated_attachments', Core_Attachment_Data::get_integrated_attachment_labels($model));
			Core_Weapon_Data::popover_possible_attachments($attachments, $model, $row, 'integrated_attachments');

			self::button_velocity($model, 'bullet_speed', Core_Calibre::calculate_bullet_speed($model, $model->length_barrel));
			self::button_velocity($model, 'muzzle_velocity');
			$row->cell('bullet_speed')->attribute('style', 'color:gray');
			$row->cell('bullet_energy')->attribute('style', 'color:gray');

			$damage = Core_Weapon::calculate_damage($model);
			$this->cell_duo_new($row, $model, 'ubImpact', $damage, 'red');
			self::button_damage($model, 'ubImpact_new', $damage);
			self::button_damage($model, 'ubImpact');

			$range = Core_Weapon::calculate_range($model);
			$this->cell_duo_new($row, $model, 'usRange', $range, '#009900', 'usRange_bonus');
			self::button_range($model, 'usRange_new', $range);
			self::button_range($model, 'usRange');

			$accuracy = Core_Weapon::calculate_accuracy($model);
			$this->cell_duo_new($row, $model, 'nAccuracy', $accuracy, '#0099BB', 'nAccuracy_bonus');
			self::button_accuracy($model, 'nAccuracy_new', $accuracy);
			self::button_accuracy($model, 'nAccuracy');

			$model->length_front = Core_Weapon_Data::get_length_front($model);

			$this->cell_one($row, $model, 'ubWeaponClass', Core_Weapon::get_class_label($model));
			$this->cell_one($row, $model, 'ubWeaponType', Core_Weapon::get_type_label($model));

			$weight_front_percent = Core_Weapon_Data::get_weight_front_percent($model);
			if ($weight_front_percent > 0) {
				$model->weight_balance = (int)$weight_front_percent . '/' . (100 - $weight_front_percent);
			}
			if ($model->weight_front_percent > 0) {
				$row->cell('weight_balance')->attribute('style', 'color:#3388FF');
			}

			$ready_weight = Core_Weapon::get_ready_weight($model, $weight_front_percent);
			$model->ready_weight = number_format($ready_weight, 3);
			$ready_weight_front = Core_Weapon::get_ready_weight_front($model, $weight_front_percent);
			$model->ready_weight_front = number_format($ready_weight_front, 3);
			$ready_length = Core_Weapon::get_ready_length($model, $weight_front_percent);
			$model->ready_length = number_format($ready_length, 3);
			$row->cell('ready_length')->attribute('style', 'color:#6495ED');
			$row->cell('length_max')->attribute('style', 'color:#6495ED');

			$this->cell_duo_new($row, $model, 'ubReadyTime', Core_Weapon::calculate_ready($model), '#009977', 'ubReadyTime_bonus');

			$this->cell_duo_new($row, $model, 'Handling', Core_Weapon::calculate_handling($model), '#007799', 'Handling_bonus');

			$sp4t = Core_Weapon::calculate_sp4t($model);
			$this->cell_duo_new($row, $model, 'ubShotsPer4Turns', $sp4t, '#337777', 'ubShotsPer4Turns_bonus');
			self::button_sp4t($model, 'ubShotsPer4Turns_new', $sp4t);
			self::button_sp4t($model, 'ubShotsPer4Turns');
			$model->format('ubShotsPer4Turns', number_format($model->ubShotsPer4Turns, 2));

			$this->cell_duo_new($row, $model, 'APsToReloadManually', Core_Weapon::calculate_aptrm($model), '#777733', 'APsToReloadManually_bonus');
			$this->cell_duo_new($row, $model, 'APsToReload', Core_Weapon::calculate_reload($model), '#773377', 'APsToReload_bonus');

			$model->format('fire_rate_auto', Core_Weapon_Data::get_fire_rate($model));

			$this->cell_duo_new($row, $model, 'BR_ROF', Core_Weapon_Data::calculate_br_rof($model), 'silver');

			$this->cell_duo_new($row, $model, 'bBurstAP', Core_Weapon::calculate_burst_ap($model), '#3388FF');

			$this->cell_duo_new($row, $model, 'bAutofireShotsPerFiveAP', Core_Weapon::calculate_auto_shots($model), 'red');

			$this->cell_duo_new($row, $model, 'bRecoilX', Core_Weapon::calculate_recoil_x($model), '#3388FF', 'bRecoilX_bonus');

			$this->cell_duo_new($row, $model, 'bRecoilY', Core_Weapon::calculate_recoil_y($model), 'red', 'bRecoilY_bonus');

			$recoil = Core_Weapon::calculate_recoil($model);
			$model->format('recoil', number_format($recoil, 2));

			$model->sbs = round(223 * sqrt(sqrt($model->length_barrel)) - 140);

			if ($model->height_diff_stock_barrel == 0) {
				$model->format('height_diff_stock_barrel', '');
			}

			$weight = Core_Weapon_Data::get_weight($model);
			$weight = number_format($weight, 1);
			$model->format('weight', $weight);
			$ubWeight = $model->ubWeight / 10;

			if ($weight != $ubWeight) {
				$weight_data = [
					$ubWeight . ' old',
					$weight . ' new',
				];
				$row->cell('weight')->attribute('style', 'color:red')
					->popover('Weight (kg)', Helper_String::to_string($weight_data, '<br/>'), 'left');
			}

			$this->cell_duo_new($row, $model, 'ubDeadliness', Core_Weapon::calculate_deadliness($model, $attachment_models), 'orange', 'ubDeadliness_bonus');

			$this->cell_duo_new($row, $model, 'MaxDistForMessyDeath', Core_Weapon::calculate_messy_range($model), 'red', 'MaxDistForMessyDeath_bonus');

			$this->cell_one($row, $model, 'ubShotsPerBurst');

			Bonus::clear();
			return $row;
		}, $attachments, $attachment_models, $calibres);

		$this->template->content[] = $filter->render();
		$this->template->content[] = $list->render();
	}

	/*
	 * SAVE
	 */

	protected function has_changes(Jelly_Collection $collection, array $attachment_models) {
		foreach ($collection as $model) {
			if (!($model instanceof Model_Weapon_Group)) {
				continue;
			}

			Bonus::clear();
			$no_semi_auto = $model->no_semi_auto ? 1 : NULL;

			$this->set_value($model, 'ubCalibre', Core_Weapon_Data::get_calibre($model));
			$this->set_value($model, 'ubWeaponClass', Core_Weapon::get_weapon_class($model));
			$this->set_value($model, 'ubWeaponType', Core_Weapon::get_weapon_type($model));
			$this->set_value($model, 'ubMagSize', Core_Weapon_Data::get_mag_size($model));

			$this->set_value($model, 'ubImpact', Core_Weapon::calculate_damage($model));
			$this->set_value($model, 'usRange', Core_Weapon::calculate_range($model));
			$this->set_value($model, 'nAccuracy', Core_Weapon::calculate_accuracy($model));
			$this->set_value($model, 'ubReadyTime', Core_Weapon::calculate_ready($model));
			$this->set_value($model, 'bAutofireShotsPerFiveAP', Core_Weapon::calculate_auto_shots($model));
			$this->set_value($model, 'bBurstAP', Core_Weapon::calculate_burst_ap($model));
			$this->set_value($model, 'ubShotsPer4Turns', Core_Weapon::calculate_sp4t($model));
			$this->set_value($model, 'ubShotsPerBurst', $model->burst_length);
			$this->set_value($model, 'APsToReloadManually', Core_Weapon::calculate_aptrm($model));
			$this->set_value($model, 'APsToReload', Core_Weapon::calculate_reload($model));
			$this->set_value($model, 'Handling', Core_Weapon::calculate_handling($model));
			$this->set_value($model, 'bRecoilX', Core_Weapon::calculate_recoil_x($model));
			$this->set_value($model, 'bRecoilY', Core_Weapon::calculate_recoil_y($model));
			$this->set_value($model, 'MaxDistForMessyDeath', Core_Weapon::calculate_messy_range($model));

			$this->set_value($model, 'ubDeadliness', Core_Weapon::calculate_deadliness($model, $attachment_models));

			$this->set_value($model, 'NoSemiAuto', $no_semi_auto);

			if (!empty($model->weapon_name)) {
				$this->set_value($model, 'szWeaponName', $model->weapon_name);
			}
			Bonus::clear();
		}

		return $this->get_changes_count();
	}

	protected function _save(Jelly_Builder $builder) {
		if (!Form::is_post()) {
			return false;
		}

		$action = $this->request->post('action');
		if ($action != 'save') {
			return false;
		}

		$weapons = $builder->select_all();

		foreach ($weapons as $model) {
			$this->save_changes($model);
		}

		Request::current()->redirect(Force_URL::current()->get_url());

		return true;
	}

} // End Controller_Admin_Weapons