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
		<?php echo Form::select($field_name . ($multiple ? '[]' : ''), $options, $selected, $attributes); ?>
		<?php /*
	<select class="form-control" name="<?php echo $field_name . ; ?>" id="<?php echo $field_name; ?>">
		<?php if (is_array($options)): foreach ($options as $option_value => $option_label): ?>
			<option value="<?php echo $option_value; ?>"<?php if ($option_value == $selected) echo ' selected="selected'; ?>><?php echo $option_label; ?></option>
		<?php endforeach; endif; ?>
	</select>
*/
		?>
	</div>
</div>
