<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php echo Form::select($name . '[hour]', $hours, $hour, $settings); ?>:<?php echo Form::select($name . '[min]', $mins, $min, $settings); ?>:<?php echo Form::select($name . '[sec]', $mins, $sec, $settings); ?>