<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 14.11.14
 * Time: 21:51
 */
?>
<div<?php echo $group_attributes; ?>>
	<?php if ($show_label) { ?>
		<label<?php echo HTML::attributes($label_attributes); ?>><?php echo $label; ?></label>
	<?php } ?>

	<div<?php echo HTML::attributes($div_attributes) ?>>
		<p class="control-text"><?php echo $value; ?></p>
		<?php if (!empty($description)) { ?>
			<small><?php echo nl2br($description); ?></small>
		<?php } ?>
	</div>
</div>
