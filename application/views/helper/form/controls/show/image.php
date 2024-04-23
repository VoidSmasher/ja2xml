<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:53
 */
?>
<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $label; ?></label>

	<div class="col-sm-10">
		<img src="<?php echo $src; ?>" class="control-image" <?php echo HTML::attributes($attributes); ?> />
	</div>
</div>
