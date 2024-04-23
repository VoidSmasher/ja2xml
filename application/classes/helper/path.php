<?php defined('SYSPATH') or die('Access denied.');

/**
 * @TODO need to rebuild Helper_Path to use config
 * Class: Helper_Path
 * User: legion
 * Date: 03.07.12
 * Time: 14:03
 * @deprecated
 */
class Helper_Path {

	public static function admin_path($object_path, $action = 'index', $object_id = null, $sub_object_id = null, $data_type = null, $force_back_url = false) {
		return self::path('admin', $object_path, $action, $object_id, $sub_object_id, $data_type, $force_back_url);
	}

	public static function developer_path($object_path, $action = 'index', $object_id = null, $sub_object_id = null, $data_type = null, $force_back_url = false) {
		return self::path('developer', $object_path, $action, $object_id, $sub_object_id, $data_type, $force_back_url);
	}

	public static function self($action = null, $object_id = null, $sub_object_id = null, $data_type = null, $force_back_url = false) {
		$request = Request::current();
		$directory = $request->directory();
		$controller = $request->controller();
		if (empty($action)) {
			$action = $request->action();
		}
		return self::path($directory, $controller, $action, $object_id, $sub_object_id, $data_type, $force_back_url);
	}

	public static function path($directory, $object_path, $action = 'index', $object_id = null, $sub_object_id = null, $data_type = null, $force_back_url = false) {
		$result = '/';
		$directory = trim($directory);
		if (empty($directory)) {
			return $result;
		}
		$result .= $directory . '/';
		$object_path = trim($object_path);
		if (empty($object_path)) {
			return $result;
		}
		$result .= $object_path;
		return self::url($result, $action, $object_id, $sub_object_id, $data_type, $force_back_url);
	}

	public static function url($uri, $action = 'index', $object_id = null, $sub_object_id = null, $data_type = null, $force_back_url = false) {
		$result = '/';
		$uri = trim($uri);
		if (empty($uri)) {
			return $result;
		}
		$result = $uri;
		if (!is_null($object_id) && in_array($action, array(
				'add',
				'index',
				'suggest',
			))
		) {
			$object_id = null;
		}
		if ($action == 'index') {
			$action = '';
		}
		if (!empty($action)) {
			$result .= '/' . $action;
			if (!empty($object_id)) {
				$result .= '/' . $object_id;
				if (!empty($sub_object_id)) {
					$result .= '/' . $sub_object_id;
				}
			}
		}
		if (!empty($data_type)) {
			$result .= '.' . $data_type;
		}
		$start_with = (strpos($result, '?') === false) ? '?' : '&';
		$current_action = Request::current()->action();
		if ($force_back_url || (($current_action == 'index') && in_array($action, array(
					'edit',
					'delete',
					'draft',
					'publish',
				)))
		) {
			$result .= Helper_Uri::insert_back_url($start_with);
		}
		return $result;
	}

} // End Helper_Path
