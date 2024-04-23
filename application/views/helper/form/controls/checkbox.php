<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.14
 * Time: 13:36
 */
?>
<div class="checkbox col-sm-10 col-sm-offset-2">
	<label>
		<?php echo Form::checkbox($field_name, NULL, (boolean)$value, $attributes); ?>
<?php /*
		<input type="checkbox" name="<?php echo $field_name; ?>" value="<?php echo $value; ?>">
*/ ?>
		<?php echo $label; ?>
	</label>
</div>