<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 01.09.12
 * Time: 15:49
 */
?>
<div class="form-group<?php if (Helper_Error::get_by_name($name)) echo ' has-error'; ?>">
	<label for="<?php echo $name; ?>" class="col-sm-2 col-xs-12 control-label"><?php echo $label; ?></label>

	<div class="col-sm-5 col-xs-6">
		<div class="pull-left"><h5>Непривязанные</h5></div>
		<div class="pull-right">
			<button class="btn btn-default two-lists-button-right" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string('Добавить выделенные'); ?>>
				<i class="glyphicon glyphicon-arrow-right"></i></button>
		</div>
		<div class="clearfix"></div>
		<?php echo Form::select($name, $options, null, $attributes_left_list); ?>
	</div>
<?php /*
	<div class="col-lg-2 col-md-2 col-sm-7 col-xs-7 col-lg-offset-0 col-md-offset-0 col-sm-offset-5 col-xs-offset-4">
		<div class="two-lists-buttons-horizontal">
			<button class="btn btn-default two-lists-button-left" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string('Убрать выделенные', 'left'); ?>>
				<i class="glyphicon glyphicon-arrow-left"></i></button>
			<button class="btn btn-default two-lists-button-right" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string('Добавить выделенные'); ?>>
				<i class="glyphicon glyphicon-arrow-right"></i></button>
		</div>
		<div class="two-lists-buttons-vertical">
			<button class="btn btn-default two-lists-button-left" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string('Убрать выделенные', 'left'); ?>>
				<i class="glyphicon glyphicon-arrow-up"></i></button>
			<button class="btn btn-default two-lists-button-right" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string('Добавить выделенные'); ?>>
				<i class="glyphicon glyphicon-arrow-down"></i></button>
		</div>
	</div>
*/ ?>
	<div class="col-sm-5 col-xs-6">
		<div class="pull-left">
			<button class="btn btn-default two-lists-button-left" type="button" rel="<?php echo $name; ?>"<?php echo Helper_Popover::get_as_string('Убрать выделенные', 'left'); ?>>
							<i class="glyphicon glyphicon-arrow-left"></i></button>
		</div>
		<div class="pull-right"><h5>Привязанные</h5></div>
		<div class="clearfix"></div>
		<?php echo Form::select($name, $selected_options, null, $attributes_right_list); ?>
	</div>
</div>

<div id="<?php echo $name . '_values'; ?>">
	<?php echo $selected_values; ?>
</div>