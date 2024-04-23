<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Force_Date
 * User: legion
 * Date: 25.07.17
 * Time: 18:19
 */
class Force_Date {

	const FORMAT_SQL = 'Y-m-d H:i:s';
	const FORMAT_SQL_TIME_ONLY = 'H:i:s';
	const FORMAT_SQL_DATE_ONLY = 'Y-m-d';

	/*
	 * Настройки по умолчанию, менять их НЕ надо, пользуйтесь factory
	 */
	protected $_show_today = false;
	protected $_show_date = true;
	protected $_show_time = true;
	protected $_show_year = true;
	protected $_show_current_year = null;
	protected $_show_seconds = false;
	protected $_show_full_range = false;

	protected $time_one = null;
	protected $time_saved = false;
	protected $time = 'now';
	protected $is_null = false;
	protected $default = null;

	public function __construct($time = 'now', $default = null) {
		$this->time = $time;
		$this->default = $default;
	}

	public static function factory($time = 'now', $default = null) {
		return new self($time, $default);
	}

	/**
	 * $strict - определяет строгость функции, будет ли она бросать Exception,
	 * если дата была указана в некорректном формате или молча отдавать значение по умолчанию.
	 *
	 * @param string    $time
	 * @param null      $default
	 * @param bool|true $strict
	 *
	 * @return int|mixed
	 * @throws Exception
	 */
	public static function timestamp($time = 'now', $default = null, $strict = true) {
		if (is_numeric($time)) {
			$result = (int)$time;
		} else if (is_string($time)) {
			$result = strtotime($time);
		} else if ($time instanceof DateTime) {
			$result = $time->getTimestamp();
		} else {
			$result = $time;
		}

		if (!is_integer($result)) {
			if ($strict) {
				$default = self::timestamp($default, $default, false);
				if (!is_integer($default)) {
					throw new Exception(self::_get_validation_error($time, $default));
				} else {
					return $default;
				}
			} else {
				return $default;
			}
		}

		return $result;
	}

	/**
	 * $strict - определяет строгость функции, будет ли она бросать Exception,
	 * если дата была указана в некорректном формате или молча отдавать значение по умолчанию.
	 *
	 * @param string    $time
	 * @param null      $default
	 * @param bool|true $strict
	 *
	 * @return DateTime|mixed
	 * @throws Exception
	 */
	public static function DateTime($time = 'now', $default = null, $strict = true) {
		$result = $time;

		if (is_numeric($time)) {
			$result = new DateTime();
			$result->setTimestamp((int)$time);
		} else if (is_string($time)) {
			$time_str = strtotime($time);
			if ($time_str) {
				$result = new DateTime();
				$result->setTimestamp($time_str);
			}
		}

		if (!($result instanceof DateTime)) {
			if ($strict) {
				$default = self::DateTime($default, $default, false);
				if (!($default instanceof DateTime)) {
					throw new Exception(self::_get_validation_error($time, $default));
				} else {
					return $default;
				}
			} else {
				return $default;
			}
		}

		return $result;
	}

	/**
	 * Для внутреннего использования
	 *
	 * @param $strict
	 *
	 * @return DateTime|mixed
	 * @throws Exception
	 */
	protected function _getDateTime($strict = true) {
		return $this->__getDateTime(false, $strict);
	}

	public function getDateTime($strict = true) {
		return $this->__getDateTime(true, $strict);
	}

	/**
	 * $strict - определяет строгость функции, будет ли она бросать Exception,
	 * если дата была указана в некорректном формате или молча отдавать значение по умолчанию.
	 *
	 * @param bool|true $clone
	 * @param bool|true $strict
	 *
	 * @return DateTime|mixed
	 * @throws Exception
	 */
	final protected function __getDateTime($clone = true, $strict = true) {
		if (!$this->time_saved) {
			$this->time_one = self::DateTime($this->time, $this->default, $strict);
			$this->time_saved = true;
		}

		$is_date_time = ($this->time_one instanceof DateTime);

		if ($strict && !$is_date_time) {
			throw new Exception(self::_get_validation_error($this->time, $this->default));
		}

		if ($clone && $is_date_time) {
			return clone $this->time_one;
		} else {
			return $this->time_one;
		}
	}

	/**
	 * $strict - определяет строгость функции, будет ли она бросать Exception,
	 * если дата была указана в некорректном формате или молча отдавать значение по умолчанию.
	 *
	 * @param bool|true $strict
	 *
	 * @return int|mixed
	 */
	public function getTimestamp($strict = true) {
		if (!(($datetime = $this->_getDateTime($strict)) instanceof DateTime)) {
			return $datetime;
		}
		return $datetime->getTimestamp();
	}

	/*
	 * SQL DATE FORMAT
	 */

	/**
	 * $strict - определяет строгость функции, будет ли она бросать Exception,
	 * если дата была указана в некорректном формате или молча отдавать значение по умолчанию.
	 *
	 * @param bool|true $strict
	 *
	 * @return string|mixed
	 */
	public function format_sql($strict = true) {
		if (!(($datetime = $this->_getDateTime($strict)) instanceof DateTime)) {
			return $datetime;
		}

		if ($this->_show_date && !$this->_show_time) {
			$date_format = self::FORMAT_SQL_DATE_ONLY;
		} elseif (!$this->_show_date && $this->_show_time) {
			$date_format = self::FORMAT_SQL_TIME_ONLY;
		} else {
			$date_format = self::FORMAT_SQL;
		}

		return $datetime->format($date_format);
	}

	/*
	 * HUMANIZE
	 */

	/**
	 * @return string
	............................................________
	....................................,.-‘”...................``~.,
	.............................,.-”..................................."-.,
	.........................,/...............................................”:,
	.....................,?......................................................\,
	.................../...........................................................,}
	................./......................................................,:`^`..}
	.............../...................................................,:”........./
	..............?.....__.........................................:`.........../
	............./__.(....."~-,_..............................,:`........../
	.........../(_....”~,_........"~,_....................,:`........_/
	..........{.._$;_......”=,_......."-,_.......,.-~-,},.~”;/....}
	...........((.....*~_.......”=-._......";,,./`..../”............../
	...,,,___.\`~,......"~.,....................`.....}............../
	............(....`=-,,.......`........................(......;_,,-”
	............/.`~,......`-...............................\....../\
	.............\`~.*-,.....................................|,./.....\,__
	,,_..........}.>-._\...................................|..............`=~-,
	.....`=~-,_\_......`\,.................................\
	...................`=~-,,.\,...............................\
	................................`:,,...........................`\..............__
	.....................................`=-,...................,%`>--==``
	........................................_\..........._,-%.......`\
	...................................,<`.._|_,-&``................`\
	 */
	public function render() {
		return $this->humanize();
	}

	/**
	 * @return string
	 */
	public function humanize() {
		if (!(($datetime = $this->_getDateTime(false)) instanceof DateTime)) {
			return (string)$datetime;
		}

		$date = '';
		$time = '';
		if ($this->_show_date) {
			$date = $this->humanize_date();
		}
		if ($this->_show_time) {
			$time = $this->humanize_time();
		}

		if (!empty($date) && !empty($time)) {
			$string = __('date.date_and_time', [
				':date' => $date,
				':time' => $time,
			]);
		} else if (empty($date) && !empty($time)) {
			$string = $time;
		} else if (!empty($date) && empty($time)) {
			$string = $date;
		} else {
			$string = '';
		}

		return $string;
	}

	/**
	 * @return string
	 */
	public function humanize_date() {
		if (!(($datetime = $this->_getDateTime(false)) instanceof DateTime)) {
			return (string)$datetime;
		}

		if (!$this->_show_date) {
			return '';
		}

//		@todo Уточнить форматы даты для разных локалей | вынести форматы в i18n
//		$locale = i18n::get_lang();
//		switch ($locale) {
//			case 'ru':
//				$date_format = 'j F';
//				break;
//			default:
		$date_format = 'j F';
//		}

		/*
		 * Четыре состояния:
		 * - показывать год для всех дат
		 * - показывать для текущего, но не показывать для остальных
		 * - скрывать текущий, но показывать для остальных
		 * - не показывать вообще
		 */
		if (is_bool($this->_show_current_year) && ($datetime->format('Y') == date('Y'))) {
			if ($this->_show_current_year) {
				$date_format .= ' Y';
			}
		} elseif ($this->_show_year) {
			$date_format .= ' Y';
		}

		return $this->format($date_format, false);
	}

	/**
	 * @return string
	 */
	public function humanize_time() {
		if (!(($datetime = $this->_getDateTime(false)) instanceof DateTime)) {
			return (string)$datetime;
		}

		if (!$this->_show_time) {
			return '';
		}

//		@todo Уточнить форматы времени для разных локалей | вынести форматы в i18n
//		$locale = i18n::get_lang();
//		switch ($locale) {
//			case 'ru':
//				$time_format = 'H:i';
//				break;
//			default:
		$time_format = 'H:i';
//		}

		if ($this->_show_seconds) {
			$time_format .= ':s';
		}

		/*
		 * date работает быстрее чем $this->format()
		 * а в данном случае возможности последнего не нужны
		 */
		return date($time_format, $datetime->getTimestamp());
	}

	/*
	 * FORMAT
	 */

	protected function _get_today_string($format, DateTime $datetime) {
		if ($this->_show_today && (date('d.m.Y') == $datetime->format('d.m.Y'))) {
			if (self::_has_str($format, 'H:i:s')) {
				$string = __('date.today_at_time', [
					':time' => $datetime->format('H:i:s'),
				]);
			} else if (self::_has_str($format, 'H:i')) {
				$string = __('date.today_at_time', [
					':time' => $datetime->format('H:i'),
				]);
			} else {
				$string = __('date.today');
			}
		} else {
			$string = $datetime->format($format);
		}
		return $string;
	}

	/**
	 * $strict - определяет строгость функции, будет ли она бросать Exception,
	 * если дата была указана в некорректном формате или молча отдавать значение по умолчанию.
	 *
	 * @param string     $format
	 * @param bool|false $strict
	 *
	 * @return string
	 * @throws Exception
	 */
	public function format($format = 'd.m.Y H:i:s', $strict = false) {
		if (!(($datetime = $this->_getDateTime($strict)) instanceof DateTime)) {
			return (string)$datetime;
		}

		$string = (string)$this->_get_today_string($format, $datetime);

		$convert = (i18n::get_lang() != 'en');

		if ($convert) {
			// День недели кратко
			if (self::_has_chars($format, 'Dr')) {
				$string = strtr($string, [
					'Mon' => i18n::get_default('date.D.Mon', 'Mon'),
					'Tue' => i18n::get_default('date.D.Tue', 'Tue'),
					'Wed' => i18n::get_default('date.D.Wed', 'Wed'),
					'Thu' => i18n::get_default('date.D.Thu', 'Thu'),
					'Fri' => i18n::get_default('date.D.Fri', 'Fri'),
					'Sat' => i18n::get_default('date.D.Sat', 'Sat'),
					'Sun' => i18n::get_default('date.D.Sun', 'Sun'),
				]);
			}

			// День недели полно
			if (self::_has_chars($format, 'l')) {
				$string = strtr($string, [
					'Monday' => i18n::get_default('date.l.Monday', 'Monday'),
					'Tuesday' => i18n::get_default('date.l.Tuesday', 'Tuesday'),
					'Wednesday' => i18n::get_default('date.l.Wednesday', 'Wednesday'),
					'Thursday' => i18n::get_default('date.l.Thursday', 'Thursday'),
					'Friday' => i18n::get_default('date.l.Friday', 'Friday'),
					'Saturday' => i18n::get_default('date.l.Saturday', 'Saturday'),
					'Sunday' => i18n::get_default('date.l.Sunday', 'Sunday'),
				]);
			}

			// Числительное
			if (self::_has_chars($format, 'S')) {
				$string = strtr($string, [
					'st' => i18n::get_default('date.S.st', 'st'),
					'nd' => i18n::get_default('date.S.nd', 'nd'),
					'rd' => i18n::get_default('date.S.rd', 'rd'),
					'th' => i18n::get_default('date.S.th', 'th'),
				]);
			}

			// Месяц кратко
			if (self::_has_chars($format, 'Mr')) {
				$string = strtr($string, [
					'Jan' => i18n::get_default('date.M.Jan', 'Jan'),
					'Feb' => i18n::get_default('date.M.Feb', 'Feb'),
					'Mar' => i18n::get_default('date.M.Mar', 'Mar'),
					'Apr' => i18n::get_default('date.M.Apr', 'Apr'),
					'May' => i18n::get_default('date.M.May', 'May'),
					'Jun' => i18n::get_default('date.M.Jun', 'Jun'),
					'Jul' => i18n::get_default('date.M.Jul', 'Jul'),
					'Aug' => i18n::get_default('date.M.Aug', 'Aug'),
					'Sep' => i18n::get_default('date.M.Sep', 'Sep'),
					'Oct' => i18n::get_default('date.M.Oct', 'Oct'),
					'Nov' => i18n::get_default('date.M.Nov', 'Nov'),
					'Dec' => i18n::get_default('date.M.Dec', 'Dec'),
				]);
			}

			// Месяц полно
			if (self::_has_chars($format, 'F')) {
				if (self::_has_chars($format, 'dj')) {
					$string = strtr($string, [
						'January' => i18n::get_default('date.F_day.January', 'January'),
						'February' => i18n::get_default('date.F_day.February', 'February'),
						'March' => i18n::get_default('date.F_day.March', 'March'),
						'April' => i18n::get_default('date.F_day.April', 'April'),
						'May' => i18n::get_default('date.F_day.May', 'May'),
						'June' => i18n::get_default('date.F_day.June', 'June'),
						'July' => i18n::get_default('date.F_day.July', 'July'),
						'August' => i18n::get_default('date.F_day.August', 'August'),
						'September' => i18n::get_default('date.F_day.September', 'September'),
						'October' => i18n::get_default('date.F_day.October', 'October'),
						'November' => i18n::get_default('date.F_day.November', 'November'),
						'December' => i18n::get_default('date.F_day.December', 'December'),
					]);
				} else {
					$string = strtr($string, [
						'January' => i18n::get_default('date.F.January', 'January'),
						'February' => i18n::get_default('date.F.February', 'February'),
						'March' => i18n::get_default('date.F.March', 'March'),
						'April' => i18n::get_default('date.F.April', 'April'),
						'May' => i18n::get_default('date.F.May', 'May'),
						'June' => i18n::get_default('date.F.June', 'June'),
						'July' => i18n::get_default('date.F.July', 'July'),
						'August' => i18n::get_default('date.F.August', 'August'),
						'September' => i18n::get_default('date.F.September', 'September'),
						'October' => i18n::get_default('date.F.October', 'October'),
						'November' => i18n::get_default('date.F.November', 'November'),
						'December' => i18n::get_default('date.F.December', 'December'),
					]);
				}
			}
		}
		return $string;
	}

	/*
	 * SHOW
	 */

	public function show_today_instead_of_current_date($value = true) {
		$this->_show_today = boolval($value);
		return $this;
	}

	public function hide_today_instead_of_current_date() {
		$this->_show_today = true;
		return $this;
	}

	public function show_date($value = true) {
		$this->_show_date = boolval($value);
		return $this;
	}

	public function hide_date() {
		$this->_show_date = false;
		return $this;
	}

	public function show_time($value = true) {
		$this->_show_time = boolval($value);
		return $this;
	}

	public function hide_time() {
		$this->_show_time = false;
		return $this;
	}

	public function show_year($value = true) {
		$this->_show_year = boolval($value);
		return $this;
	}

	public function hide_year() {
		$this->_show_year = false;
		return $this;
	}

	public function show_current_year($value = true) {
		$this->_show_current_year = boolval($value);
		return $this;
	}

	public function hide_current_year() {
		$this->_show_current_year = false;
		return $this;
	}

	public function show_seconds($value = true) {
		$this->_show_seconds = boolval($value);
		if ($this->_show_seconds && !$this->_show_time) {
			$this->_show_time = true;
		}
		return $this;
	}

	public function hide_seconds() {
		$this->_show_seconds = false;
		return $this;
	}

	public function show_full_range($value = true) {
		$this->_show_full_range = boolval($value);
		return $this;
	}

	public function hide_full_range() {
		$this->_show_full_range = false;
		return $this;
	}

	/*
	 * STATIC
	 */

	public function time_list($time_from = '00:00', $time_to = '23:00', $modifier = '+1 hour') {
		$times = array();
		$time_from = new DateTime($time_from);
		$time_to = new DateTime($time_to);

		$time_format = 'H:i';

		if ($this->_show_seconds) {
			$time_format .= ':s';
		}

		do {
			$times[] = $time_from->format($time_format);
			$time_from->modify($modifier);
		} while ($time_from <= $time_to);

		return $times;
	}

	/*
	 * DELTA TIME
	 */

	public function delta_time_in_seconds($time_two = 'now') {
		$time_one = $this->getTimestamp();
		$time_two = self::timestamp($time_two);
		if ($time_one >= $time_two) {
			return 0;
		}

		$delta = $time_two - $time_one;

		return $delta;
	}

	/**
	 * @param string     $time_two
	 * @param bool|false $i18n_after_delta_time
	 * @param null       $default
	 *
	 * @return string
	 */
	public function humanize_delta_time($time_two = 'now', $i18n_after_delta_time = false) {
		if (!(($datetime = $this->_getDateTime(false)) instanceof DateTime)) {
			return (string)$datetime;
		}

		$delta_time_in_seconds = $this->delta_time_in_seconds($time_two);

		if (empty($delta_time_in_seconds)) {
			return (string)$this->default;
		}

		$delta_time_strings = array();

		$i18n_prefix = ($i18n_after_delta_time) ? 'after_' : '';

		$delta_days = floor($delta_time_in_seconds / Date::DAY);
		if ($delta_days > 0) {
			$delta_time_strings[] = $delta_days . ' ' . i18n::get_numeric($i18n_prefix . 'day', $delta_days);
			$delta_time_in_seconds = $delta_time_in_seconds - ($delta_days * Date::DAY);
		}

		$show_time = ($this->_show_full_range) ? $this->_show_time : empty($delta_time_strings);

		if (!$show_time) {
			return self::_delta_time_as_string($delta_time_strings, $this->default);
		}

		$delta_hours = floor($delta_time_in_seconds / Date::HOUR);
		if ($delta_hours > 0) {
			$delta_time_strings[] = $delta_hours . ' ' . i18n::get_numeric($i18n_prefix . 'hour', $delta_hours);
			$delta_time_in_seconds = $delta_time_in_seconds - ($delta_hours * Date::HOUR);
		}

		if (!$this->_show_full_range && !empty($delta_time_strings)) {
			return self::_delta_time_as_string($delta_time_strings, $this->default);
		}

		$delta_minutes = floor($delta_time_in_seconds / Date::MINUTE);
		if ($delta_minutes > 0) {
			$delta_time_strings[] = $delta_minutes . ' ' . i18n::get_numeric($i18n_prefix . 'minute', $delta_minutes);
			$delta_time_in_seconds = $delta_time_in_seconds - ($delta_minutes * Date::MINUTE);
		}

		if (!$this->_show_full_range && !empty($delta_time_strings)) {
			return self::_delta_time_as_string($delta_time_strings, $this->default);
		}

		$show_seconds = ($this->_show_full_range) ? $this->_show_seconds : empty($delta_time_strings);

		if ($show_seconds && ($delta_time_in_seconds > 0)) {
			$delta_time_strings[] = $delta_time_in_seconds . ' ' . i18n::get_numeric($i18n_prefix . 'second', $delta_time_in_seconds);
		}

		return self::_delta_time_as_string($delta_time_strings, $this->default);
	}

	/*
	 * DATETIME
	 */

	/**
	 * @param string     $time_two
	 * @param bool|false $absolute
	 *
	 * @return bool|DateInterval
	 * @throws Exception
	 */
	public function diff($time_two = 'now', $absolute = false) {
		$time_two = self::DateTime($time_two, null, true);
		return $this->_getDateTime(true)->diff($time_two, $absolute);
	}

	public function modify($modify) {
		$this->_getDateTime(true)->modify($modify);
		return $this;
	}

	public function setDate($year, $month, $day) {
		$this->_getDateTime(true)->setDate($year, $month, $day);
		return $this;
	}

	public function setTime($hour, $minute, $second = 0) {
		$this->_getDateTime(true)->setTime($hour, $minute, $second);
		return $this;
	}

	/*
	 * HELPERS
	 */

	final protected static function _get_validation_error($time, $default) {
		return 'Not a valid date/time format. Time: ' . var_export($time, true) . ' default: ' . var_export($default, true);
	}

	final protected static function _has_chars($format, $chars) {
		$result = false;
		$length = strlen($chars);
		for ($i = 0; $i < $length; $i++) {
			$result = (strpos($format, $chars[$i]) !== false);
			if ($result) {
				break;
			}
		}
		return $result;
	}

	final protected static function _has_str($format, $string) {
		return (strpos($format, $string) !== false);
	}

	final protected static function _delta_time_as_string(array $delta_time_strings, $default = null) {
		if (empty($delta_time_strings)) {
			return (string)$default;
		}
		return implode(' ', $delta_time_strings);
	}

	/*
	 * WEEK
	 */

	final public static function get_week_start_by_week_number($year, $week) {
		$start_year = mktime(0, 0, 0, 1, 1, $year);
		$start_year_week_day = date('N', $start_year);

		$start = $start_year + ((7 * ($week - 1)) + 1 - $start_year_week_day) * Date::DAY;

		return $start;
	}

} // End Force_Date
