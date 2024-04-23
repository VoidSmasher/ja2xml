<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 28.01.14
 * Time: 2:23
 */
?>
<div>
	<legend><?php echo __('auth.title.remind'); ?></legend>

	<?php echo Form::open(null, array(
		'role' => 'form',
		'class' => 'form-horizontal fixed-width',
	)); ?>

	<div class="form-group">
		<?php echo Form::label('password', __('user.password_new'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::password('password', null, array(
				'id' => 'password',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo Form::label('password_confirm', __('user.password_confirm'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::password('password_confirm', null, array(
				'id' => 'password_confirm',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>

	<?php if (!empty($captcha)): ?>
		<div class="form-group">
			<?php echo Form::label('captcha', __('captcha.enter_text_from_picture'), array(
				'class' => 'col-sm-5 control-label',
			)); ?>
			<div class="col-sm-7">
				<?php echo $captcha; ?>
				<?php echo Form::input('captcha', null, array(
					'id' => 'captcha',
					'autocomplete' => 'off',
					'style' => 'text-transform: uppercase',
					'class' => 'form-control input-lg',
				)); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="form-group">
		<div class="col-sm-offset-5 col-sm-7">
			<span class="help-block"><?php echo __('form.all_fields_are_required'); ?></span>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-7 col-sm-push-5">
			<button type="submit" class="btn btn-primary"><?php echo __('auth.button.change_password'); ?></button>
		</div>
	</div>

	<?php echo Form::close(); ?>
</div>

<div class="clearfix"></div>
