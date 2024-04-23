<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 28.08.14
 * Time: 7:19
 */
?>
<div class="well">
	<h4><?php echo __('common.search'); ?></h4>
	<?php echo Form::open('/articles', array('method' => 'GET')); ?>
	<div class="input-group">
		<input name="term" type="text" class="form-control" value="<?php echo $term; ?>">
<span class="input-group-btn">
	<button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
</span>
	</div>
	<?php echo Form::close(); ?>
</div>
