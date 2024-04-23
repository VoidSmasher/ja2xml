<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */
?>
<div class="form-group<?php if (Helper_Error::get_by_name($field_name)) echo ' has-error'; ?>">
	<label for="<?php echo $field_name; ?>" class="col-sm-2 control-label"><?php echo $label; ?></label>

	<div class="col-sm-10">
		<?php echo Form::textarea($field_name, $value, $attributes); ?>
	</div>
</div>
