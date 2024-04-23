<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 12.05.14
 * Time: 19:51
 */
?>
<div>
	<legend><?php echo __('auth.title.remind'); ?></legend>

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
		<div class="col-sm-7 col-sm-push-5">
			<button type="submit" class="btn btn-primary"><?php echo __('auth.button.remind'); ?></button>
		</div>
	</div>

	<?php echo Form::close(); ?>
</div>

<div class="clearfix"></div>
