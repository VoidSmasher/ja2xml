<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */
?>
<?php if ($form_horizontal): ?>
	<div<?php echo $group_attributes; ?>>
		<label>
			<?php echo Form::checkbox($name, NULL, $value, $attributes); ?>
			<?php if ($show_label): ?>
				<?php echo $label; ?>
			<?php endif; ?>
		</label>
	</div>
	<?php if ($form_horizontal): ?>
		<div class="clearfix"></div>
	<?php endif; ?>
	<?php if (!empty($description)): ?>
		<div class="form-group">
			<small<?php echo HTML($description_attributes) ?>><?php echo nl2br($description); ?></small>
		</div>
	<?php endif; ?>
<?php else: ?>
	<div<?php echo $group_attributes; ?>>
		<label>
			<?php echo Form::checkbox($name, NULL, $value, $attributes); ?>
			<?php if ($show_label): ?>
				<?php echo $label; ?>
			<?php endif; ?>
		</label>
	</div>
	<?php if (!empty($description)): ?>
		<div class="form-group">
			<small><?php echo nl2br($description); ?></small>
		</div>
	<?php endif; ?>
<?php endif; ?>
