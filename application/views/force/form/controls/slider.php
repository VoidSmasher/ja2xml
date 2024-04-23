<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: telepat
 * Date: 5/10/17
 * Time: 7:10 PM
 */
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label): ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php endif; ?>
	<div<?php echo HTML::attributes($div_attributes) ?>>
		<?php if ($value && is_array($value) && !empty($value)): ?>
			<?php foreach( $value as $image ): ?>
			<img src="<?php echo URL::site($path . $image); ?>" />
			<label class="btn btn-default">
				<?php echo Form::checkbox($name_remove . '[]', $image, false); ?>
				<?php echo __('file.remove'); ?>
			</label>
			<br /><br />
			<?php endforeach; ?>
		<?php endif; ?>

		<span><input<?php echo HTML::attributes($attributes); ?>/></span>
		<br />
	</div>
</div>
<div class="clearfix"></div>

