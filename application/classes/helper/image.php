<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Image
 * User: legion
 * Date: 25.04.12
 * Time: 19:23
 */
class Helper_Image {

	protected static $driver = 'Imagick';

	const MAX_WIDTH = 'max_width';
	const INVERSE_CUBE = 'inverse_cube';
	const FIT = 'fit';
	const AS_IS = 'as_is';
	protected static $allowed_formats = array(
		'jpg',
		'jpeg',
		'png',
		'gif',
		'swf',
	);
	protected static $force_as_is = array(
		'swf',
	);

	protected static function _resize($temp_filename, $old_filename, $image_type, $remove_temp = true, $crop_image = false) {
		self::remove_file($old_filename, $image_type);

		$filename = basename($temp_filename);
		$ext = strtolower(pathinfo($temp_filename, PATHINFO_EXTENSION));
		$config = Kohana::$config->load('images.' . $image_type);
		$destination = DOCROOT . $config['dir'];
		$destination = rtrim($destination, ' /');
		$destination = $destination . DIRECTORY_SEPARATOR . $filename;

		$crop_width = Kohana::$config->load('images.' . $image_type . '.crop.width');
		$crop_height = Kohana::$config->load('images.' . $image_type . '.crop.height');

		$crop_sharpen = Kohana::$config->load('images.' . $image_type . '.crop.sharpen');
		$crop_quality = Kohana::$config->load('images.' . $image_type . '.crop.quality');

		if (!$crop_quality) {
			$crop_quality = 100;
		}

		if ((empty($crop_width) && empty($crop_height)) || in_array($ext, self::$force_as_is)) {
			$crop_style = self::AS_IS;
		} else {
			$crop_style = Kohana::$config->load('images.' . $image_type . '.crop.style');
			if (empty($crop_style)) {
				$crop_style = Image::INVERSE;
			}
		}

		if (($crop_style == self::MAX_WIDTH) && !empty($crop_width)) {
			$image = Image::factory($temp_filename, self::$driver);
			if ($image->width > $crop_width) {
				$crop_style = Image::INVERSE;
			} else {
				$crop_style = self::AS_IS;
			}
		}

		switch ($crop_style) {
			case self::AS_IS:
				copy($temp_filename, $destination);
				break;
			case self::FIT:
				$image = Image::factory($temp_filename, self::$driver);

				if ($image->height > $image->width) {
					$target_height = $crop_height;
					$target_width = round($target_height / $image->height * $image->width);
				} else {
					$target_width = $crop_width;
					$target_height = round($target_width / $image->width * $image->height);
				}

				$image->resize($target_width, $target_height, Image::NONE);

				if ($crop_sharpen > 0) {
					$image->sharpen($crop_sharpen);
				}

				$image->save($destination, $crop_quality);

				break;
			case self::INVERSE_CUBE: // у этого кейса не должно быть break
				$image = Image::factory($temp_filename, self::$driver);

				if ($image->height < $image->width) {
					$temp_image = imagecreatetruecolor($image->width, $image->width);
					$y = (int)($image->width - $image->height) / 2;
					$x = 0;
				} else {
					$temp_image = imagecreatetruecolor($image->height, $image->height);
					$x = (int)($image->height - $image->width) / 2;
					$y = 0;
				}

				$white = imagecolorallocate($temp_image, 255, 255, 255);
				imagefill($temp_image, 0, 0, $white);

				switch ($image->mime) {
					case 'image/png':
						$image_old = imagecreatefrompng($temp_filename);
						imagecopyresampled($temp_image, $image_old, $x, $y, 0, 0, $image->width, $image->height, $image->width, $image->height);
						imagepng($temp_image, $temp_filename, 0);
						break;

					case 'image/jpeg':
						$image_old = imagecreatefromjpeg($temp_filename);
						imagecopyresampled($temp_image, $image_old, $x, $y, 0, 0, $image->width, $image->height, $image->width, $image->height);
						imagejpeg($temp_image, $temp_filename, 100);
						break;

					case 'image/gif':
						$image_old = imagecreatefromgif($temp_filename);
						imagecopyresampled($temp_image, $image_old, $x, $y, 0, 0, $image->width, $image->height, $image->width, $image->height);
						imagegif($temp_image, $temp_filename);
						break;
				}

				$crop_style = Image::INVERSE;
			// у этого кейса не должно быть break
			default:
				$image = Image::factory($temp_filename, self::$driver);
				$image->resize($crop_width, $crop_height, $crop_style);
				if ($crop_image && !is_null($crop_width) && !is_null($crop_height)) {
					$image->crop($crop_width, $crop_height);
				}

				if ($crop_sharpen > 0) {
					$image->sharpen($crop_sharpen);
				}

				$image->save($destination, $crop_quality);
		}

		$crop_types = Kohana::$config->load('images.' . $image_type . '.crop.types');
		if (is_array($crop_types)) {
			foreach ($crop_types as $crop_type) {
				self::crop($temp_filename, $old_filename, $crop_type, false);
			}
		}

		if ($remove_temp) {
			unlink($temp_filename);
		}
		return $filename;
	}

	public static function resize($temp_filename, $old_filename, $image_type, $remove_temp = true) {
		return self::_resize($temp_filename, $old_filename, $image_type, $remove_temp, false);
	}

	public static function crop($temp_filename, $old_filename, $image_type, $remove_temp = true) {
		return self::_resize($temp_filename, $old_filename, $image_type, $remove_temp, true);
	}

	public static function copy_image($filename, $image_type, $new_filename = false) {
		$product_image_dir = DOCROOT . Kohana::$config->load('images.' . $image_type . '.dir');

		if (!file_exists($product_image_dir . $filename)) {
			return false;
		}

		if(!$new_filename){
			$name = md5(uniqid());
			$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			$new_filename = $name . '.' . $ext;
		}

		try {
			copy($product_image_dir . $filename, $product_image_dir . $new_filename);
		} catch (Exception $e) {
			Log::exception($e, __CLASS__, __FUNCTION__, 'Unable to copy image: ' . $product_image_dir . $filename . ' type: ' . $image_type);
			return false;
		}

		$crop_types = Kohana::$config->load('images.' . $image_type . '.crop.types');

		if (is_array($crop_types)) {
			foreach ($crop_types as $crop_type) {
				self::copy_image($filename, $crop_type, $new_filename);
			}
		}

		return $new_filename;
	}

	public static function upload_for_model($file, Jelly_Model $model, $name, $image_type = null, $label = null, $file_required = null) {
		if (is_null($image_type)) {
			$image_type = $model->meta()->model() . '_' . $name;
		}
		if (empty($label)) {
			$label = $model->get_field_label($name);
		}
		if (!is_bool($file_required)) {
			$file_required = $model->get_value_from_rules($name, 'not_empty', false);
		}
		return self::upload($file, $model->{$name}, $image_type, $name, $label, $file_required);
	}

	public static function upload($file, $old_filename, $image_type, $name = null, $label = null, $file_required = false) {

		if (!Upload::valid($file)) {
			Helper_Error::add(__('image.upload.error.upload_failed'), $name, $label);
		}

		if (!Upload::not_empty($file)) {
			if (Helper_Error::no_errors() && $file_required && (empty($old_filename)) && ($file['error'] === UPLOAD_ERR_NO_FILE)) {
				Helper_Error::add(__('image.upload.error.no_upload'), $name, $label);
				return FALSE;
			}
		}

		if (Helper_Error::no_errors() && !Upload::type($file, self::$allowed_formats)) {
			Helper_Error::add(__('image.upload.error.not_image', array(
				':extensions' => implode(', ', self::$allowed_formats),
			)), $name, $label);
		}

		if (Helper_Error::no_errors()) {
			$max_file_size = Upload::get_max_file_size();
			if (!Upload::size($file, $max_file_size) || ($file['error'] === UPLOAD_ERR_FORM_SIZE)) {
				Helper_Error::add(__('image.upload.error.max_size_exceeded', array(
					':max_file_size' => Helper_String::humanize_file_size($max_file_size),
				)), $name, $label);
			}
		}

		if (!Upload::not_empty($file)) {
			return FALSE;
		}

		if (Helper_Error::has_errors()) {
			return FALSE;
		}

		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$filename = md5(uniqid()) . '.' . $ext;
		$directory = DOCROOT . Kohana::$config->load('images.upload.temp_directory');
		if ($temp_filename = Upload::save($file, $filename, $directory)) {
			return self::crop($temp_filename, $old_filename, $image_type);
		} else {
			Helper_Error::add(__('image.upload.error.upload_failed'), $name, $label);
		}

		return FALSE;
	}

	public static function save_image($image, $image_type, $save_as_temp = false) {
		if (!Upload::valid($image) OR
			!Upload::not_empty($image) OR
			!Upload::type($image, array(
				'jpg',
				'jpeg',
				'png',
				'gif',
				'swf',
			))
		) {
			return FALSE;
		}

		$config = Kohana::$config->load('images.' . $image_type);
		$name = md5(uniqid());
		$ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
		$filename = $name . $ext;
		if ($save_as_temp) {
			$directory = DOCROOT . Kohana::$config->load('images.upload.temp_directory');
		} else {
			$directory = DOCROOT . $config['dir'];
		}

		if ($file = Upload::save($image, $filename, $directory)) {
			return $filename;
		}

		return FALSE;
	}

	public static function add_session_image($base_name, $image_type) {
		$current_images = Session::instance()
			->get('current_images', array());
		$current_images[$image_type][$base_name] = true;
		Session::instance()
			->set('current_images', $current_images);
		$crop_types = Kohana::$config->load('images.' . $image_type . '.crop.types');
		if (is_array($crop_types)) {
			foreach ($crop_types as $crop_type) {
				self::add_session_image($base_name, $crop_type);
			}
		}
	}

	public static function remove_node_from_session($base_name, $image_type) {
		$result = true;
		$current_images = Session::instance()
			->get('current_images', array());
		if (!empty($current_images) && array_key_exists($image_type, $current_images)) {
			if (array_key_exists($base_name, $current_images[$image_type])) {
				unset($current_images[$image_type][$base_name]);
				Session::instance()
					->set('current_images', $current_images);
			}
		}
		return $result;
	}

	public static function remove_current_images_from_session() {
		return Session::instance()
			->delete('current_images');
	}

	/**
	 * @static
	 *
	 * @param $base_name
	 * @param $image_type
	 *
	 * @return string
	 */
	public static function get_filename_from_current_session_or_from_cdn($base_name, $image_type) {
		$load_from_cdn = true;
		$current_images = Session::instance()
			->get('current_images', array());
		if (!empty($current_images) && array_key_exists($image_type, $current_images)) {
			$load_from_cdn = !array_key_exists($base_name, $current_images[$image_type]);
		}
		if ($load_from_cdn) {
			return self::get_cdn_filename($base_name, $image_type);
		} else {
			return self::get_filename($base_name, $image_type);
		}
	}

	/**
	 * @static
	 *
	 * @param        $base_name
	 * @param        $image_type
	 * @param string $alt
	 * @param null   $additional_attributes
	 *
	 * @return string
	 */
	public static function get_image_from_current_session_or_from_cdn($base_name, $image_type, $alt = '', $additional_attributes = null) {
		$load_from_cdn = true;
		$current_images = Session::instance()
			->get('current_images', array());
		if (!empty($current_images) && array_key_exists($image_type, $current_images)) {
			$load_from_cdn = !array_key_exists($base_name, $current_images[$image_type]);
		}
		if ($load_from_cdn) {
			return self::get_cdn_image($base_name, $image_type, $alt, $additional_attributes);
		} else {
			return self::get_image($base_name, $image_type, $alt, $additional_attributes);
		}
	}

	protected static function _get_swf_object($filename, $image_type) {
		$width = Kohana::$config->load('images.' . $image_type . '.crop.width');
		$height = Kohana::$config->load('images.' . $image_type . '.crop.height');
		if (!empty($width)) {
			$width = ' width="' . $width . '"';
		}
		if (!empty($height)) {
			$height = ' height="' . $height . '"';
		}

		return '<object type="application/x-shockwave-flash" data="' . $filename . '"' . $width . $height . '></object>';
	}

	public static function get_image_by_src($filename, $image_type, $alt = '', $additional_attributes = null, $is_cdn = false, $write_size = true) {
		$width = Kohana::$config->load('images.' . $image_type . '.crop.width');
		$height = Kohana::$config->load('images.' . $image_type . '.crop.height');
		$style = Kohana::$config->load('images.' . $image_type . '.crop.style');
		$attributes = array(
			'alt' => htmlspecialchars($alt),
		);
		if ($write_size) {
			if ($style == self::MAX_WIDTH) {
				if (!empty($width)) {
					$attributes['style'] = 'max-width:' . $width . 'px;';
				}
			} else {
				if (!empty($width)) {
					$attributes['width'] = $width;
				}
				if (!empty($height)) {
					$attributes['height'] = $height;
				}
			}
		}
		if (is_array($additional_attributes)) {
			$attributes = array_merge($attributes, $additional_attributes);
		}
		if ($is_cdn) {
			$default = self::get_static_filename(Kohana::$config->load('images.' . $image_type . '.default'));
			return '<img onerror="this.src=\'' . $default . '\'" src="' . $filename . '" ' . HTML::attributes($attributes) . '>';
		} else {
			return '<img src="' . $filename . '" ' . HTML::attributes($attributes) . '>';
		}
	}

	protected static function _get_cdn_image($filename, $image_type, $alt = '', $additional_attributes = null) {
		$can_use_cdn_images = self::check_cdn_read_permission();
		return self::get_image_by_src($filename, $image_type, $alt, $additional_attributes, $can_use_cdn_images);
	}

	public static function get_image($image, $image_type, $alt = '', $additional_attributes = null, $default = null, $write_size = true) {
		$filename = self::get_filename($image, $image_type, $default);

		$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

		if ($ext == 'swf') {
			return self::_get_swf_object($filename, $image_type);
		} else {
			return self::get_image_by_src($filename, $image_type, $alt, $additional_attributes, $write_size);
		}
	}

	public static function get_cdn_image($image, $image_type, $alt = '', $additional_attributes = null) {
		$filename = self::get_cdn_filename($image, $image_type);

		$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

		if ($ext == 'swf') {
			return self::_get_swf_object($filename, $image_type);
		} else {
			return self::_get_cdn_image($filename, $image_type, $alt, $additional_attributes);
		}
	}

	public static function get_filename($image, $image_type, $default = null) {
		$image = trim($image, ' /');
		if (empty($image)) {
			if (empty($default)) {
				$default = Kohana::$config->load('images.' . $image_type . '.default');
			}
			return self::get_static_filename($default);
		}

		$directory = Kohana::$config->load('images.' . $image_type . '.dir');
		$directory = trim($directory, ' /');

		$filename = $directory . DIRECTORY_SEPARATOR . $image;
		if (!file_exists(DOCROOT . $filename)) {
			$filename = self::get_static_filename(Kohana::$config->load('images.' . $image_type . '.default'));
		} else {
			$server = Force_URL::get_current_host();
			$filename = $server . DIRECTORY_SEPARATOR . $filename;
		}
		return $filename;
	}

	public static function get_cdn_filename($image, $image_type) {
		$can_use_cdn_images = self::check_cdn_read_permission();
		$image = trim($image, ' /');

		if ($can_use_cdn_images && !empty($image)) {
			$cdn_url = Kohana::$config->load('environment.cdn.images.url');
			$cdn_url = rtrim($cdn_url, ' /');

			$directory = Kohana::$config->load('images.' . $image_type . '.dir');
			$directory = trim($directory, ' /');

			$cdn_path = $cdn_url . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $image;
		} else {
			$cdn_path = self::get_filename($image, $image_type);
		}
		return $cdn_path;
	}

	public static function remove_image($image_filename, $image_type) {
		$result = self::remove_file($image_filename, $image_type);

		if ($result) {
			$crop_types = Kohana::$config->load('images.' . $image_type . '.crop.types');
			if (is_array($crop_types)) {
				foreach ($crop_types as $crop_type) {
					self::remove_image($image_filename, $crop_type);
				}
			}
		}
		return $result;
	}

	public static function remove_file($image_filename, $image_type) {
		$result = true;
		if (!empty($image_filename)) {
			$filename = DOCROOT . Kohana::$config->load('images.' . $image_type . '.dir') . $image_filename;
			if (file_exists($filename)) {
				$result = unlink($filename);
			}
		}
		if ($result) {
			$result = self::remove_file_from_cdn($image_filename, $image_type);
		}
		if ($result) {
			self::remove_node_from_session($image_filename, $image_type);
		}
		return $result;
	}

	public static function remove_file_from_cdn($image, $image_type) {
		$result = true;
		$can_use_cdn_images = self::check_cdn_write_permission();
		if ($can_use_cdn_images && !empty($image)) {
			$cdn_ftp = Kohana::$config->load('environment.cdn.images.ftp');
			$connection = Helper_Ftp::get_connection($cdn_ftp);
			if ($connection && @ftp_chdir($connection, $cdn_ftp['root'])) {
				$filename = Kohana::$config->load('images.' . $image_type . '.dir') . $image;
				$result = Helper_Ftp::remove_file($connection, $filename);
			}
		}
		return $result;
	}

	/*
	 * STATIC
	 */

	public static function get_static_filename($filename) {
		if (empty($filename)) {
			return '';
		}
		$filename = trim($filename, ' /');

		$directory = Kohana::$config->load('images.static.directory');
		$directory = trim($directory, ' /');

		$server = Force_URL::get_current_host();
		return $server . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $filename;
	}

	public static function get_static_image($filename, $attributes = null) {
		$filename = self::get_static_filename($filename);
		return '<img src="' . $filename . '" ' . HTML::attributes($attributes) . '>';
	}

	/*
	 * STATIC FROM CDN
	 */

	public static function get_static_filename_from_cdn($filename) {
		if (empty($filename)) {
			return '';
		}
		$filename = trim($filename, ' /');

		$can_use_cdn_images = self::check_cdn_read_permission();
		if ($can_use_cdn_images) {

			$cdn_url = Kohana::$config->load('environment.cdn.images.url');
			$cdn_url = rtrim($cdn_url, ' /');

			$cdn_path = $cdn_url . DIRECTORY_SEPARATOR . $filename;
		} else {
			$server = Force_URL::get_current_host();
			$cdn_path = $server . DIRECTORY_SEPARATOR . $filename;
		}
		return $cdn_path;
	}

	public static function get_static_image_from_cdn($filename, $attributes = null) {
		$filename = self::get_static_filename_from_cdn($filename);
		return '<img src="' . $filename . '" ' . HTML::attributes($attributes) . '>';
	}

	/*
	 * CDN
	 */

	public static function copy_image_to_cdn($filename, $image_type) {
		$result = false;
		$can_use_cdn_images = self::check_cdn_write_permission();
		if ($can_use_cdn_images) {
			$dir = Kohana::$config->load('images.' . $image_type . '.dir');
			$source = DOCROOT . $dir . $filename;
			if (!empty($filename) && file_exists($source)) {
				$cdn_ftp = Kohana::$config->load('environment.cdn.images.ftp');
				$connection = Helper_Ftp::get_connection($cdn_ftp);
				if ($connection && @ftp_chdir($connection, $cdn_ftp['root'])) {
					$destination = $dir . $filename;
					$result = Helper_Ftp::copy_file($connection, $source, $destination);
				}
			}
			$crop_types = Kohana::$config->load('images.' . $image_type . '.crop.types');
			if (is_array($crop_types)) {
				foreach ($crop_types as $crop_type) {
					self::copy_image_to_cdn($filename, $crop_type);
				}
			}
		}
		return $result;
	}

	public static function check_cdn_path($image, $image_type) {
		$cdn_path = self::get_cdn_filename($image, $image_type);
		try {
			$result = (boolean)@file_get_contents($cdn_path, false, null, 0, 1);
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}

	public static function check_cdn_read_permission() {
		return (boolean)Kohana::$config->load('environment.cdn.images.read');
	}

	public static function check_cdn_write_permission() {
		$result = false;
		if (self::check_cdn_read_permission()) {
			$result = (boolean)Kohana::$config->load('environment.cdn.images.write');
		}
		return $result;
	}

} // End Helper_Image
