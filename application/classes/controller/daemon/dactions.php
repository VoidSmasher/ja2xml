<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Controller_Daemon Dactions
 * User: Verstov Andrey
 */
class Controller_Daemon_Dactions extends Controller_Daemon_Template {

	public function action_index() {
		set_time_limit(60);
		ini_set('memory_limit', '-1');

		Log::instance()->add(Log::INFO, 'Start do deferred actions')
			->write();

		$limit = Kohana::$config->load('daemons.cron_settings.dactions.config.limit');

		if (empty($limit)) {
			$limit = 10;
		}

		$actions = Core_Deferred_Action::factory()->get_builder()
			->where('canceled_at', '=', null)
			->where('executed_at', '=', null)
			->where('tries', '<', Core_Deferred_Action::LIMIT)
			->limit($limit)
			->select_all();

		foreach ($actions as $action) {
			$result = Core_Deferred_Action::factory()->auto_send($action);

			Log::instance()->add(Log::INFO, 'Deferred action ID: ' . $action->id . ' result: ' . (int)$result)
				->write();
		}

		Log::instance()->add(Log::INFO, 'End deferred actions')
			->write();

		return true;
	}

} // End Controller Daemon Dactions