<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 24.10.14
 * Time: 13:10
 */
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<div class="input-group date">
			<?php echo FORM::input($name, $value, $attributes); ?>
			<label class="input-group-addon"<?php
			if (array_key_exists('id', $label_attributes)) {
				echo ' for="' . $label_attributes['id'] . '"';
			}
			?>><?php echo $icon; ?></label>
		</div>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
