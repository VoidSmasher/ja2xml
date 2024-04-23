<?php defined('SYSPATH') or die('Access denied.');

/**
 * Class: Core_Bullet
 * User: legion
 * Date: 26.09.19
 * Time: 5:21
 */
class Core_Bullet extends Core_Common {

	use Core_Common_Static;

	protected static $model_class = 'Model_Bullet';
	protected $model_name = 'bullet';

	public function __construct($id = self::UNDEFINED) {
		parent::__construct($id);
	}

	public static function factory($id = self::UNDEFINED) {
		return new self($id);
	}

	public static function get_bullet_energy($bullet_speed, $bullet_weight) {
		// Переводим граммы в килограммы
		$bullet_weight = $bullet_weight / 1000;

		// E = mv^2 / 2
		$energy = ($bullet_weight * ($bullet_speed * $bullet_speed)) / 2;

		return round($energy);
	}

	public static function get_range($bullet_speed, $bullet_weight, $bullet_diameter, $bullet_coefficient) {
		$g = 9.81;
		// Плотность воздуха kg/m3
		$air_density = 1.29;
		// Угол стрельбы в градусах
		$alpha = 1;
		// Переводим угол из градусов в радианы
		$alpha = $alpha * pi() / 180;

		// Заглушка, чтобы не было деления на ноль
		if ($bullet_weight < 1) {
			$bullet_weight = 1;
		}

		// Переводим граммы в киллограммы
		$bullet_weight = $bullet_weight / 1000;

		// Переводим миллиметры в метры
		$bullet_diameter = $bullet_diameter / 1000;

		// Находим радиус от диаметра
		$r = $bullet_diameter / 2;

		// площадь поперечного сечения: PI * R^2
		$S = M_PI * pow($r, 2);

		$k = ($bullet_coefficient * $S * $air_density) / (2 * $bullet_weight);

		// Скорости по осям
		$Vx = $bullet_speed * cos($alpha);
		$Vy = $bullet_speed * sin($alpha);

		// Скорость набегающего потока = скорость пули
		$V = sqrt(pow($Vx, 2) + pow($Vy, 2));

		// Проекция ускорения - производная от проекции скорости
//		$dVx = -$k * $V * $Vx;
//		$dVy = -$k * $V * $Vy - $g;

		$range = $k * $V * $Vx;

		return $range;
	}

	/**
	 * @return Force_Filter_Select
	 */
	public static function get_filter_control() {
		$session = Session::instance();
		$bullet_type = $session->get('bullet_type');

		$filter_control = Force_Filter_Select::factory('bullet_type', 'Bullet type', Core_Calibre::get_types_list());

		if ($bullet_type) {
			$filter_control->default_value($bullet_type);
		}

		return $filter_control;
	}

	public static function check_filter_control(Force_Filter $filter) {
		$session = Session::instance();
		$bullet_type = $session->get('bullet_type');

		$filter_control = $filter->get_control('bullet_type');
		if ($filter_control instanceof Force_Filter_Select) {
			$bullet_type = $filter_control->get_value();
		}

		if (empty($bullet_type)) {
			$session->delete('bullet_type');
		} else {
			$session->set('bullet_type', $bullet_type);
		}

		return $bullet_type;
	}

} // End Core_Bullet