<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 17.11.14
 * Time: 18:25
 */
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<div<?php echo HTML::attributes($attributes); ?>>
			<?php if (!empty($value)) { ?>
				<?php echo nl2br($value); ?>
			<?php } else { ?>
				<?php echo $value; ?>
			<?php } ?>
		</div>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
