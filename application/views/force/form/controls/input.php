<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 15.07.14
 * Time: 4:08
 */
$show_input_group = (!empty($before_input) || !empty($after_input));
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<?php echo ($show_input_group) ? '<div class="input-group">' : ''; ?>
		<?php echo $before_input; ?>
		<?php echo FORM::input($name, $value, $attributes); ?>
		<?php echo $after_input; ?>
		<?php echo ($show_input_group) ? '</div>' : ''; ?>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
