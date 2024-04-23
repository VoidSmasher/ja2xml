<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<?php
		echo Form::textarea($name, $value, $attributes);
		if (!empty($description)) {
			echo "\n<small>" . nl2br($description) . '</small>';
		}
		?>
	</div>
</div>
