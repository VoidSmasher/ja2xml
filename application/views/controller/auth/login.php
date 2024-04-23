<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 23.04.12
 * Time: 14:43
 */
if (Helper_Auth::get_permission('allow_registration')) {
	$left_column = ' class="col-md-6 col-lg-5"';
	$right_column = ' class="col-md-6 col-lg-7"';
} else {
	$left_column = '';
	$right_column = '';
}
?>
<div<?php echo $left_column; ?>>
	<legend><?php echo __('auth.title.login'); ?></legend>

	<?php echo Form::open(null, array(
		'role' => 'form',
		'class' => 'form-horizontal fixed-width',
	)); ?>

	<div class="form-group">
		<?php echo Form::label('login', __('user.login_or_email'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::input('login', null, array(
				'id' => 'login',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo Form::label('password', __('user.password'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::password('password', null, array(
				'id' => 'password',
				'class' => 'form-control'
			)); ?>
			<small><a href="/auth/remind/"><?php echo __('auth.button.forgot_password'); ?></a></small>
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
			<button type="submit" class="btn btn-primary"><?php echo __('auth.button.login'); ?></button>
		</div>
	</div>
	<?php echo Form::close(); ?>
</div>
<?php if (Helper_Auth::get_permission('allow_registration')): ?>
	<div<?php echo $right_column; ?>>
		<legend><?php echo __('auth.title.registration'); ?></legend>

		<div class="fixed-width">
			<p><?php echo __('auth.registration.teaser'); ?></p>

			<div class="form-group">
				<div class="col-sm-7 col-sm-push-5">
					<a href="/auth/registration" class="btn btn-default"><?php echo __('auth.button.register'); ?></a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<div class="clearfix"></div>
