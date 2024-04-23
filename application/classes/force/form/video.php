<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Form_Video
 * User: legion
 * Date: 06.06.17
 * Time: 18:44
 */
class Force_Form_Video extends Force_Form_Input {

	const YOUTUBE = 'youtube';
	const VIMEO = 'vimeo';
	const RUTUBE = 'rutube';
	const NO_PARSE = 'video';

	protected static $_parsers = [
		self::YOUTUBE => [
			'id' => '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
			'player' => 'http://www.youtube.com/embed/:video_id',
			'image' => 'http://img.youtube.com/vi/:video_id/0.jpg',
		],
		self::VIMEO => [
			'id' => '/[http|https]+:\/\/(?:www\.|)(?:player\.|)vimeo\.com\/(?:video\/|)([a-zA-Z0-9_\-]+)(&.+)?/i',
			'player' => 'http://player.vimeo.com/video/:video_id',
			'image' => '',
		],
		self::RUTUBE => [
			'id' => '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/embed\/([a-zA-Z0-9_\-]+)/i',
			'player' => 'http://rutube.ru/video/embed/:video_id',
			'image' => '',
		],
	];

	protected $_icon_class = 'fa-video-camera';
	protected $_video_type = self::NO_PARSE;
	protected $_video_id = null;
	protected $_video_player = null;
	protected $_video_image = null;

	public function __construct($name = null, $label = null, $value = null) {
		parent::__construct($name, $label, $value);
	}

	public static function factory($name = null, $label = null, $value = null) {
		return new self($name, $label, $value);
	}

	protected function _render_simple() {
		$value = $this->get_video_player($this->get_value());
		return FORM::input($this->get_name(), $value, $this->get_attributes());
	}

	public function render() {
		$this->_view_data['value'] = $this->get_video_player($this->get_value());

		return parent::render();
	}

	public function get_video_id($value) {
		if (is_null($this->_video_id)) {
			$value = trim($value);

			$parser = Arr::get(self::$_parsers, $this->_video_type);

			$this->_video_id = self::parse_video_id(Arr::get($parser, 'id'), $value);
			$this->_video_player = self::parse_video_player(Arr::get($parser, 'player'), $this->_video_id);
			$this->_video_image = self::parse_video_image(Arr::get($parser, 'image'), $this->_video_id);
		}

		return (string)$this->_video_id;
	}

	public function get_video_player($value) {
		$this->get_video_id($value);
		return (string)$this->_video_player;
	}

	public function get_video_image($value) {
		$this->get_video_id($value);
		return (string)$this->_video_image;
	}

	/*
	 * HELPERS
	 */

	public static function parse_video_id($parser, $value) {
		$video_id = '';

		if (empty($parser) || empty($value)) {
			return '';
		}

		/*
		 * URL ли ты о value или не URL?
		 */
		if (!filter_var($value, FILTER_VALIDATE_URL)) {
			$video_id = $value;
		}

		if (empty($video_id) && preg_match($parser, $value, $match)) {
			$video_id = $match[1];
		}
		return $video_id;
	}

	public static function parse_video_player($parser, $video_id) {
		if (empty($parser) || empty($video_id)) {
			return '';
		}
		return strtr($parser, [
			':video_id' => $video_id,
		]);
	}

	public static function parse_video_image($parser, $video_id) {
		if (empty($parser) || empty($video_id)) {
			return '';
		}
		return strtr($parser, [
			':video_id' => $video_id,
		]);
	}

	/*
	 * VIDEO TYPES
	 */

	public function youtube() {
		return $this->set_type(self::YOUTUBE);
	}

	public function vimeo() {
		return $this->set_type(self::VIMEO);
	}

	public function rutube() {
		return $this->set_type(self::RUTUBE);
	}

	/*
	 * TYPE
	 */

	public function get_type() {
		return $this->_video_type;
	}

	/**
	 * @param $type
	 *
	 * @return Force_Form_Video
	 */
	public function set_type($type) {
		$type = strtolower($type);
		if (array_key_exists($type, self::$_parsers)) {
			$this->_video_type = $type;
		} else {
			$this->_video_type = self::NO_PARSE;
		}
		switch ($type) {
			case self::YOUTUBE:
				$this->_icon_class = 'fa-youtube-play';
				break;
			case self::VIMEO:
				$this->_icon_class = 'fa-vimeo';
				break;
			default:
				$this->_icon_class = 'fa-video-camera';
		}
		$this->_video_id = NULL;
		return $this;
	}

	public static function get_control_class_by_type($type) {
		$type = strtolower($type);
		$class = '';
		if (($type == self::NO_PARSE) || array_key_exists($type, self::$_parsers)) {
			$class = 'Force_Form_Video';
		}
		return $class;
	}

	/**
	 * @param $type
	 *
	 * @return false|Force_Form_Video
	 */
	public static function create_control_by_type($type) {
		$type = strtolower($type);
		$control = false;
		if (($type == self::NO_PARSE) || array_key_exists($type, self::$_parsers)) {
			$control = Force_Form_Video::factory($type, __('common.' . $type))
				->set_type($type);
		}
		return $control;
	}

	/*
	 * FORM APPLY
	 * Все пояснения в Force_Form_Control
	 */

	public function apply_value_before_save(Force_Form_Core $form, $new_value = null, $old_value = null) {
		$this->value($this->get_video_id($new_value));
		return $this->get_value();
	}

	/*
	 * ARRAY
	 */

	public function as_array() {
		$data = parent::as_array();
		$data['type'] = $this->get_type();
		return $data;
	}

	public function parse_array(array $data) {
		parent::parse_array($data);
		$this->set_type(Arr::get($data, 'type'));
		return $this;
	}

	/*
	 * HTML
	 */

	public function transform_to_html($value = null, array $attributes = null) {
		if (is_null($value)) {
			$value = $this->get_value();
		}
		return $this->get_video_player($value);
	}

} // End Force_Form_Video
