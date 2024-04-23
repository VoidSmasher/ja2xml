<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: Andrey Verstov
 * Date: 11.07.14
 * Time: 13:36
 */
?>

<div class="form-group<?php if (Helper_Error::get_by_name($field_name)) echo ' has-error'; ?>">
	<label for="<?php echo $field_name; ?>" class="col-sm-2 control-label"><?php echo $label; ?></label>
	<div class="col-sm-10">
		<?php echo Form::textarea($field_name, $value, $attributes); ?>
		<br>

	</div>

</div>

<script type="text/javascript">
	(function () {
		$("textarea[name='<?php echo $field_name; ?>']").pagedownBootstrap();
		var idPreview<?php echo $field_name; ?> = $('textarea[name="<?php echo $field_name; ?>"]').attr('id').replace('input','preview');
		var idButtonRow<?php echo $field_name; ?> = $('textarea[name="<?php echo $field_name; ?>"]').attr('id').replace('input','button-row');
		var id<?php echo $field_name; ?> = $('textarea[name="<?php echo $field_name; ?>"]').attr('id').replace('wmd-input-','');
		$('#'+idButtonRow<?php echo $field_name; ?> + ' #wmd-button-group4-' + id<?php echo $field_name; ?>).after('<button class="btn btn-primary" id="preview_<?php echo $field_name; ?>"><i class="fa fa-search"></i></button>')

		$('#' + idPreview<?php echo $field_name; ?>).hide();

		$('#preview_<?php echo $field_name; ?>').click(function(e){
			e.preventDefault();
			$('#' + idPreview<?php echo $field_name; ?>).toggle();

		});
	})();
</script>