<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_File_Core
 * User: legion
 * Date: 09.08.17
 * Time: 1:12
 * Require:
 * - Force_Control_Name
 * - Force_Control_Label
 */
trait Force_File_Core {

	protected $file_type = null;
	protected $upload = [];
	protected $save_as_json = false;

	public function upload($old_filename = null) {
		$name = method_exists($this, 'get_name') ? $this->get_name() : null;
		$label = method_exists($this, 'get_label') ? $this->get_label() : null;
		$is_required = method_exists($this, 'is_required') ? $this->is_required() : false;

		if (empty($this->upload) && !empty($name)) {
			$this->file(Arr::get($_FILES, $name, []));
		}

		if (!Upload::valid($this->upload)) {
			Helper_Error::add(__('file.upload.error.upload_failed'), $name, $label);
			return FALSE;
		}

		if (!Upload::not_empty($this->upload)) {
			if (Helper_Error::no_errors() && $is_required && (empty($old_filename)) && (Arr::get($this->upload, 'error') === UPLOAD_ERR_NO_FILE)) {
				Helper_Error::add(__('file.upload.error.no_upload'), $name, $label);
				return FALSE;
			}
		}

		$accept = $this->get_accepted_mime();
		$mime = File::mime(Arr::get($this->upload, 'tmp_name'));

		if (Helper_Error::no_errors() && !in_array($mime, $accept)) {
			Helper_Error::add(__('file.upload.error.wrong_type', array(
				':extensions' => implode(', ', $this->get_accepted_extensions()),
			)), $name, $label);
		}

		if (Helper_Error::no_errors()) {
			$max_file_size = $this->get_max_file_size();
			if (!Upload::size($this->upload, $max_file_size) || ($this->upload['error'] === UPLOAD_ERR_FORM_SIZE)) {
				Helper_Error::add(__('file.upload.error.max_size_exceeded', array(
					':max_file_size' => Helper_String::humanize_file_size($max_file_size),
				)), $name, $label);
			}
		}

		if (!Upload::not_empty($this->upload)) {
			return FALSE;
		}

		if (Helper_Error::has_errors()) {
			return FALSE;
		}

		$ext = strtolower(pathinfo(Arr::get($this->upload, 'name'), PATHINFO_EXTENSION));
		$filename = md5(uniqid()) . '.' . $ext;
		$directory = DOCROOT . $this->get_dir();

		if ($filename = Upload::save($this->upload, $filename, $directory)) {
			if ($this->save_as_json) {
				return json_encode(array(
					'uploaded' => Arr::get($this->upload, 'name'),
					'saved' => $filename,
				), JSON_UNESCAPED_UNICODE);
			} else {
				return $filename;
			}
		} else {
			Helper_Error::add(__('image.upload.error.upload_failed'), $name, $label);
		}

		return FALSE;
	}

	public function use_json($value = true) {
		$this->save_as_json = boolval($value);
		return $this;
	}

	/*
	 * FILE
	 */

	public function file(array $upload) {
		$this->upload = $upload;
		return $this;
	}

	/*
	 * FILE TYPE
	 */

	public function file_type($file_type) {
		$this->file_type = (string)$file_type;
		return $this;
	}

	public function get_file_type() {
		return $this->file_type;
	}

	/*
	 * CONFIG
	 */

	/**
	 * @param $file_type
	 *
	 * @return array
	 * @throws Kohana_Exception
	 */
	public function get_config() {
		$config = Kohana::$config->load('files.' . $this->file_type);

		if ($config instanceof Kohana_Config_Group) {
			$config = $config->as_array();
		}

		return $config;
	}

	/**
	 * @return array
	 */
	public function get_accepted_mime() {
		$file_config = $this->get_config();
		$accept = Arr::get($file_config, 'accept', []);
		if (is_string($accept)) {
			$accept = array($accept);
		}
		if (!is_array($accept)) {
			$accept = [];
		}
		return $accept;
	}

	/**
	 * @return array
	 */
	public function get_accepted_extensions() {
		$mime_types = $this->get_accepted_mime();
		$extensions = array();
		foreach ($mime_types as $mime) {
			$types = File::exts_by_mime($mime);
			if (is_array($types)) {
				$extensions += $types;
			}
		}
		return array_unique($extensions);
	}

	public function get_max_file_size() {
		$file_config = $this->get_config();
		$max_file_size = Arr::get($file_config, 'max_file_size', 0);
		$max_file_size = Num::bytes($max_file_size);
		$max_file_size_system = Upload::get_max_file_size();

		if ($max_file_size > $max_file_size_system || $max_file_size == 0) {
			$max_file_size = $max_file_size_system;
		}

		return $max_file_size;
	}

	public function get_dir() {
		$upload = Kohana::$config->load('files.upload.directory');
		$file_config = $this->get_config();
		$dir = Arr::get($file_config, 'dir', '');
		$dir = rtrim($dir, DIRECTORY_SEPARATOR . ' ');
		if (!empty($dir)) {
			$dir .= DIRECTORY_SEPARATOR;
		}
		return $upload . $dir;
	}

	public function get_file_name($saved_name, $default = '') {
		$saved_name = trim($saved_name);
		if (empty($saved_name)) {
			return $default;
		}

		$dir = $this->get_dir();
		$filename = $dir . $saved_name;

		$host = Force_URL::get_current_host();

		$filename = ltrim($filename, DIRECTORY_SEPARATOR . ' ');
		if (!empty($filename)) {
			$filename = DIRECTORY_SEPARATOR . $filename;
		}

		$filename = $host . $filename;

		return $filename;
	}

	public function get_file_size($saved_name, $humanize = true) {
		$size = 0;
		$saved_name = trim($saved_name);
		if (!empty($saved_name)) {
			$filename = $this->get_file_name($saved_name);
			if (file_exists($filename)) {
				$size = filesize($filename);
			}
		}
		if ($humanize) {
			$size = Helper_String::humanize_file_size($size);
		}
		return $size;
	}

} // End Force_File_Core
