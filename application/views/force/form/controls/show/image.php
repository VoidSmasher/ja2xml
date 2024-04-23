<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 14.11.14
 * Time: 22:27
 */
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<img src="<?php echo $value; ?>" <?php echo HTML::attributes($attributes); ?> />
	</div>
</div>