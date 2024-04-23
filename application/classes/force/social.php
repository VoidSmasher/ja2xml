<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Social
 * User: legion
 * Date: 21.05.16
 * Time: 9:56
 */
class Force_Social extends Force_Attributes {

	const CONFIG_PARAM = 'socials';

	const FACEBOOK = 'facebook';
	const GOOGLE = 'google';
	const INSTAGRAM = 'instagram';
	const LINKEDIN = 'linkedin';
	const ODNOKLASSNIKI = 'odnoklassniki';
	const PINTEREST = 'pinterest';
	const TWITTER = 'twitter';
	const VIMEO = 'vimeo';
	const VKONTAKTE = 'vkontakte';
	const VK = 'vk';
	const YOUTUBE = 'youtube';

	protected static $_links = [
		self::FACEBOOK => 'https://facebook.com/:account-name',
		self::GOOGLE => 'https://plus.google.com/:account-name',
		self::INSTAGRAM => 'https://instagram.com/:account-name',
		self::LINKEDIN => 'https://linked.in/:account-name',
		self::ODNOKLASSNIKI => 'https://ok.ru/:account-name',
		self::TWITTER => 'https://twitter.com/:account-name',
		self::VIMEO => 'https://vimeo.com/:account-name',
		self::VKONTAKTE => 'https://vk.com/:account-name',
		self::VK => 'https://vk.com/:account-name',
		self::PINTEREST => 'https://pinterest.com/:account-name',
		self::YOUTUBE => 'https://youtube.com/:account-name',
	];

	protected static $_parsers = [
//		self::FACEBOOK => [
//			'user_id' => '/[http|https]+:\/\/(?:www\.|)facebook\.com\/([a-zA-Z0-9_\-]+)(&.+)?/i',
//			'link' => 'https://facebook.com/:user_id',
//		],
	];

	protected $_use_font_awesome = false;
	protected $_icon_fixed_width = false;
	protected static $_instances = array();
	protected $_socials = array();
	protected $_filename = 'project_custom';

	public function __construct($filename = 'project_custom') {
		$this->attribute('target', '_blank');
		$this->attribute('rel', 'nofollow');
		$this->_filename = (string)$filename;
		$this->load_socials();
	}

	/**
	 * @param string $filename
	 *
	 * @return Force_Social
	 */
	public static function instance($filename = 'project_custom') {
		if (!array_key_exists($filename, self::$_instances)) {
			self::$_instances[$filename] = new self($filename);
		}
		return self::$_instances[$filename];
	}

	public function load_socials() {
		$_socials = Force_Config::instance($this->_filename)->get_param(self::CONFIG_PARAM, array());
		if (!is_array($_socials)) {
			$_socials = array();
		}
		$this->_socials = $_socials;
		return $this;
	}

	/*
	 * GET
	 */

	public static function get_label($name) {
		return i18n::get_default('social.' . $name, ucfirst($name));
	}

	public function get_control($name, $description = null) {
		$label = self::get_label($name);
		$value = Arr::get($this->_socials, $name);
		$name = self::CONFIG_PARAM . '[' . $name . ']';

		$control = Force_Form_Input::factory($name, $label, $value);
		if (!empty($description)) {
			$control->description($description);
		}
		return $control;
	}

	public function get_link($name, $protocol = 'http') {
		$link = Arr::get($this->_socials, $name);
		$link = self::_check_link_protocol($name, $link, $protocol);
		return $link;
	}

	public static function generate_link($name, $id, $protocol = 'http') {
		$link = self::_check_link_protocol($name, $id, $protocol);
		return $link;
	}

	protected static function _check_link_protocol($name, $link, $protocol = 'http') {
		$link = trim($link);
		if (!empty($link)) {
			if (array_key_exists($name, self::$_links)) {
				$link = strtr(self::$_links[$name], [
					':account-name' => $link,
				]);
			}
			$protocol_url = parse_url($link, PHP_URL_SCHEME);
			if (empty($protocol_url)) {
				$link = URL::site($link, $protocol);
			}
		}
		return $link;
	}

	/**
	 * @return array
	 */
	public function as_array() {
		$socials = $this->_socials;
		foreach ($socials as $name => $link) {
			if (empty($link)) {
				unset ($socials[$name]);
				continue;
			}
			$socials[$name] = self::_check_link_protocol($name, $link);
		}
		return $socials;
	}

	/*
	 * FONT-AWESOME
	 */

	public function use_font_awesome() {
		$this->_use_font_awesome = true;
		return $this;
	}

	public function icon_fixed_width($value = true) {
		$this->_icon_fixed_width = boolval($value);
		return $this;
	}

	/*
	 * RENDER
	 */

	public function render() {
		$html = array();
		$attributes = $this->get_attributes();
		foreach ($this->_socials as $name => $link) {
			$link = self::_check_link_protocol($name, $link);
			if (!empty($link)) {
				$icon_class = $name;
				if ($this->_use_font_awesome) {
					$icon_class = 'fa-' . $icon_class;
				}
				$icon = Helper_Bootstrap::get_icon($icon_class, $this->_icon_fixed_width);
				$attributes['class'] = $name;
				$html[] = HTML::anchor($link, $icon, $attributes);
			}
		}
		return implode("\n", $html);
	}

	public static function parse_url($name, $link) {
		$link = trim($link);

		if (empty($link)) {
			return $link;
		}

		$parsers = Arr::get(self::$_parsers, $name, []);

		/*
		 * Парсеры не указаны, парсить нечем, возвращаем link
		 */
		if (!is_array($parsers) || empty($parsers)) {
			return $link;
		}

		$constructor = Arr::get($parsers, 'link', '');

		/*
		 * Если сборщика урлы нет, то и разбирать её соответственно нет никакого смысла.
		 * Возвращаем что было передано и сказке конец.
		 */
		if (empty($constructor)) {
			return $link;
		}

		$user_id = $link;

		/*
		 * Если link - это URL, то сразу же его парсим, если есть чем конечно
		 * Если нет, значит - это уже переданный user_id
		 */
		if (filter_var($link, FILTER_VALIDATE_URL)) {
			$user_id_parser = Arr::get($parsers, 'user_id', '');

			/*
			 * Нам передали URL, но парсить его нечем.
			 */
			if (empty($user_id_parser)) {
				return $link;
			}

			/*
			 * Пытаемся распарсить URL, если не получится, значит какая-то была передана хрень.
			 * И даже возвращать её нет никакого смысла.
			 * У нас тут серьёзная контора и всякие гадости мы сохранять не будем! xD
			 */
			if (preg_match($user_id_parser, $link, $match)) {
				$user_id = $match[1];
			} else {
				return '';
			}
		}

		/*
		 * Что-то нихрена не напарсилось, передали какую-то херь, ну её наф.
		 */
		if (empty($user_id)) {
			return '';
		}

		return strtr($constructor, [
			':user_id' => $user_id,
		]);
	}

} // End Force_Social
