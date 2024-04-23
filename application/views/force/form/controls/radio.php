<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */
$group_class = ($multiple) ? 'checkbox' : 'radio';
if ($horizontal) {
	$group_class .= '-inline';
}
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>
	<div<?php echo HTML::attributes($div_attributes) ?>>
		<?php if (is_array($options)) foreach ($options as $_key => $_value) { ?>
			<div class='<?php echo $group_class; ?>'>
				<?php
				$checked = is_array($value) ? array_key_exists($_key, $value) : ($value == $_key);
				?>
				<label>
					<?php if ($multiple) {
						echo Form::checkbox($name . '[]', $_key, $checked, $attributes);
					} else {
						echo Form::radio($name, $_key, $checked, $attributes);
					} ?>&nbsp<?php echo $_value; ?>
				</label>
			</div>
			<?php if (!$horizontal) { ?>
				<div class="clearfix"></div>
			<?php } ?>
		<?php } ?>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
