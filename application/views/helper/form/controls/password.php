<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:35
 */
?>
<div class="form-group<?php if (Helper_Error::get_by_name($field_name)) echo ' has-error'; ?>">
	<label for="<?php echo $field_name; ?>" class="col-sm-2 control-label"><?php echo $label; ?></label>

	<div class="col-sm-10">
		<input type="password" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo $value; ?>" <?php echo HTML::attributes($attributes); ?>>
	</div>
</div>
