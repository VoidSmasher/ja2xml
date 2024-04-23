<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Helper_Time
 * User: legion
 * Date: 28.04.12
 * Time: 3:38
 * @deprecated
 */
class Helper_Time {

	const SECONDS_IN_MINUTE = 60;
	const SECONDS_IN_HOUR = 3600;
	const SECONDS_IN_DAY = 86400;
	const SECONDS_IN_WEEK = 604800;
	const SECONDS_IN_MONTH = 2592000; // 30 days
	const SECONDS_IN_YEAR = 31536000; // 365 days
	const FORMAT_SQL = 'Y-m-d H:i:s';
	const FORMAT_SQL_DATE_ONLY = 'Y-m-d';

	private static $hours = array();
	private static $minutes = array();

	/*
	 * TIME
	 */

	public static function humanize_time_format($date, $show_seconds = true) {
		if (is_numeric($date)) {
			return self::humanize_unix_time_format($date, $show_seconds);
		} else {
			return self::humanize_mysql_time_format($date, $show_seconds);
		}
	}

	public static function humanize_mysql_time_format($mysql_date, $show_seconds = false) {
		$unix_timestamp = strtotime($mysql_date);
		return self::humanize_unix_time_format($unix_timestamp, $show_seconds);
	}

	public static function humanize_unix_time_format($unix_timestamp, $show_seconds = false) {
		if ($show_seconds) {
			$result = date('H:i:s', $unix_timestamp);
		} else {
			$result = date('H:i', $unix_timestamp);
		}
		return $result;
	}

	/*
	 * DATE or DATETIME
	 */

	public static function humanize_date_format($date, $show_time = true, $show_current_year = false, $show_seconds = false) {
		if (is_numeric($date)) {
			return self::humanize_unix_date_format($date, $show_time, $show_current_year, $show_seconds);
		} else {
			return self::humanize_mysql_date_format($date, $show_time, $show_current_year, $show_seconds);
		}
	}

	public static function humanize_mysql_date_format($mysql_date, $show_time = true, $show_current_year = false, $show_seconds = false) {
		$unix_timestamp = strtotime($mysql_date);
		return self::humanize_unix_date_format($unix_timestamp, $show_time, $show_current_year, $show_seconds);
	}

	public static function humanize_unix_date_format($unix_timestamp, $show_time = true, $show_current_year = false, $show_seconds = false) {
		if (empty($unix_timestamp)) {
			return '';
		}
		$day = date('j', $unix_timestamp);
		$month = date('n', $unix_timestamp);
		$year = date('Y', $unix_timestamp);
		if (($day == date('j')) && ($month == date('n')) && ($year == date('Y'))) {
			$result = __('today');
		} else {
			$result = __('day_and_month.' . $month, array(':day' => $day));
			if (($year != date('Y')) || $show_current_year) {
				$result .= ' ' . $year;
			}
		}
		if ($show_time) {
			$result .= __('time_after_date', array(':time' => self::humanize_unix_time_format($unix_timestamp, $show_seconds)));
		}
		return $result;
	}

	// 0000-00-00 00:00 from 00.00.0000
	public static function mysql_date_from_date($date) {
		return Helper_Time::mysql_date_from_unix_timestamp(strtotime($date));
	}

	public static function mysql_date_from_unix_timestamp($unix_timestamp = null, $with_time = true) {
		if (is_null($unix_timestamp)) {
			$unix_timestamp = time();
		}

		if ($with_time) {
			return date(self::FORMAT_SQL, $unix_timestamp);
		} else {
			return date(self::FORMAT_SQL_DATE_ONLY, $unix_timestamp);
		}
	}

	public static function check_new($timestamp) {
		$delta = time() - $timestamp;
		return ($delta <= self::SECONDS_IN_WEEK);
	}

	public static function time_form($name, $time = false, $settings = array()) {
		$time_string = '00:00:00';

		if ($time) {
			$time_string = substr($time, 11);
		}

		return Form::input($name, $time_string, $settings);
	}

	public static function time_select_form($name, $time = false, $settings = array()) {

		$hour = '00';
		$min = '00';
		$sec = '00';

		if ($time) {
			$hour = date('H', strtotime($time));
			$min = date('i', strtotime($time));
			$sec = date('s', strtotime($time));
		}

		if (empty(self::$hours)) {
			for ($i = 0; $i < 24; $i++) {
				self::$hours[] = ($i < 10) ? '0' . $i : $i;
			}
		}

		if (empty(self::$minutes)) {
			for ($i = 0; $i < 60; $i++) {
				self::$minutes[] = ($i < 10) ? '0' . $i : $i;
			}
		}

		return View::factory(HELPER_VIEW . 'time_select_form')
			->bind('hours', self::$hours)
			->bind('mins', self::$minutes)
			->bind('hour', $hour)
			->bind('min', $min)
			->bind('sec', $sec)
			->set('name', $name)
			->set('settings', $settings);
	}

	public static function sec_to_time($seconds, $add_zero_to_hours = false) {
		$hours = floor($seconds / 3600);
		$minutes = floor($seconds % 3600 / 60);
		$seconds = $seconds % 60;

		$zero = ($add_zero_to_hours) ? '%02d' : '%d';

		return sprintf($zero . ":%02d:%02d", $hours, $minutes, $seconds);
	}

	public static function time_to_sec($time) {
		if (is_array($time)) {
			$hours = $time['hour'];
			$minutes = $time['min'];
			$seconds = $time['sec'];
		} else {
			if (strpos($time, ' ') !== false) {
				$time = explode(' ', $time);
				$time = $time[1];
			}
			$time = explode(':', $time);
			$hours = $time[0];
			$minutes = $time[1];
			$seconds = $time[2];

		}
		$result = $hours * 3600;
		$result += $minutes * 60;
		$result += $seconds;

		return $result;
	}

	public static function parse_duration($duration) {
		if ($duration != 0) {
			$time = explode(',', $duration);
			$hours = floor($time[0] / 3600);
			$h = ($hours > 0) ? $hours . ':' : '';

			$tmp = $time[0] - ($hours * 3600);
			$minutes = floor($tmp / 60);
			$m = (($minutes < 10) && ($hours > 0)) ? '0' . $minutes . ':' : $minutes . ':';

			$s = $tmp - ($minutes * 60);
			if (isset($time[1])) {
				$msec = ($time[1] != '') ? $time[1] : '';
				if ($msec > 9 && $msec < 100) {
					$msec = $msec . '0';
				} elseif ($msec < 10) {
					$msec = $msec . '00';
				}
			} else {
				$msec = '000';
			}
			$time = $h . $m . $s . '.' . $msec . '  (' . $duration . ')';
		} else {
			$time = '0';
		}
		return $time;
	}

	public static function colorize_time($time_str) {
		for ($num = 0; $num < strlen($time_str); $num++) {
			if (in_array($time_str{$num}, array(
				'0',
				':',
			))
			) {
				continue;
			} else {
				break;
			}
		}
		if ($num > 0) {
			$zeros = substr($time_str, 0, $num);
			$value = substr($time_str, $num);
			$time_str = '<span style="color:silver">' . $zeros . '<span style="color:black">' . $value . '</span></span>';
		}
		return $time_str;
	}

	public static function colorize_duration($duration) {
		return Helper_Time::colorize_time(Helper_Time::sec_to_time($duration, true));
	}

	public static function delta_time_in_seconds($time_from, $time_to) {
		if (!is_numeric($time_from)) {
			$time_from = strtotime($time_from);
		}
		if (!is_numeric($time_to)) {
			$time_to = strtotime($time_to);
		}

		if ($time_from >= $time_to) {
			return 0;
		}

		$delta = $time_to - $time_from;

		return $delta;
	}

	public static function humanize_delta_time($time_from, $time_to, $show_seconds = false, $after = false) {
		$delta_time_in_seconds = self::delta_time_in_seconds($time_from, $time_to);

		if (empty($delta_time_in_seconds)) {
			return null;
		}

		$delta_time_strings = array();

		$i18n_prefix = ($after) ? 'after_' : '';

		$delta_days = floor($delta_time_in_seconds / self::SECONDS_IN_DAY);
		if ($delta_days > 0) {
			$delta_time_strings[] = $delta_days . ' ' . i18n::get_numeric($i18n_prefix . 'day', $delta_days);
			$delta_time_in_seconds = $delta_time_in_seconds - ($delta_days * self::SECONDS_IN_DAY);
		}

		$delta_hours = floor($delta_time_in_seconds / self::SECONDS_IN_HOUR);
		if ($delta_hours > 0) {
			$delta_time_strings[] = $delta_hours . ' ' . i18n::get_numeric($i18n_prefix . 'hour', $delta_hours);
			$delta_time_in_seconds = $delta_time_in_seconds - ($delta_hours * self::SECONDS_IN_HOUR);
		}

		$delta_minutes = floor($delta_time_in_seconds / self::SECONDS_IN_MINUTE);
		if ($delta_minutes > 0) {
			$delta_time_strings[] = $delta_minutes . ' ' . i18n::get_numeric($i18n_prefix . 'minute', $delta_minutes);
			$delta_time_in_seconds = $delta_time_in_seconds - ($delta_minutes * self::SECONDS_IN_MINUTE);
		}

		if ($show_seconds && ($delta_time_in_seconds > 0)) {
			$delta_time_strings[] = $delta_time_in_seconds . ' ' . i18n::get_numeric($i18n_prefix . 'second', $delta_time_in_seconds);
		}

		return implode(' ', $delta_time_strings);
	}

	public static function make_times($start = '09:00', $finish = '18:00', $modificator = '+30min') {
		$times = array();
		$start_time = new DateTime($start);
		$finish_time = new DateTime($finish);
		do {
			$times[] = $start_time->format('H:i');
			$start_time->modify($modificator);
		}while( $start_time <= $finish_time );
		return $times;
	}

} // End Helper_Time
