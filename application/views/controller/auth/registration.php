<?php defined('SYSPATH') or die('Access denied.');
/**
 * User: legion
 * Date: 23.04.12
 * Time: 14:43
 */
?>
<div class="col-md-6 col-lg-5">
	<legend><?php echo __('auth.title.registration'); ?></legend>

	<?php echo Form::open(null, array(
		'role' => 'form',
		'class' => 'form-horizontal fixed-width',
	)); ?>

	<div class="personal-data">
		<div class="form-group">
			<?php echo Form::label('surname', __('user.surname'), array(
				'class' => 'col-sm-5 control-label',
			)); ?>
			<div class="col-sm-7">
				<?php echo Form::input('surname', $user_data['surname'], array(
					'id' => 'surname',
					'class' => 'form-control'
				)); ?>
			</div>
		</div>

		<div class="form-group">
			<?php echo Form::label('name', __('user.name'), array(
				'class' => 'col-sm-5 control-label',
			)); ?>
			<div class="col-sm-7">
				<?php echo Form::input('name', $user_data['name'], array(
					'id' => 'name',
					'class' => 'form-control'
				)); ?>
			</div>
		</div>
	</div>

	<div class="registry-data">
		<div class="form-group">
			<?php echo Form::label('username', __('user.login'), array(
				'class' => 'col-sm-5 control-label',
			)); ?>
			<div class="col-sm-7">
				<?php echo Form::input('username', $user_data['username'], array(
					'id' => 'username',
					'class' => 'form-control'
				)); ?>
			</div>
		</div>

		<div class="form-group">
			<?php echo Form::label('email', __('user.email'), array(
				'class' => 'col-sm-5 control-label',
			)); ?>
			<div class="col-sm-7">
				<?php echo Form::input('email', $user_data['email'], array(
					'id' => 'email',
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

	<div class="form-checkbox">
		<label class="checkbox">
			<input type="checkbox" name="agree"> <?php echo __('auth.registration.eula.agree'); ?>
		</label>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-5 col-sm-7">
			<span class="help-block"><?php echo __('form.all_fields_are_required'); ?></span>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-7 col-sm-push-5">
			<button type="submit" class="btn btn-primary"><?php echo __('auth.button.register'); ?></button>
		</div>
	</div>
	<?php echo Form::close(); ?>
</div>
<div class="col-md-6 col-lg-7">
	<legend><?php echo $eula['title']; ?></legend>
	<small><?php echo $eula['content']; ?></small>
</div>

<div class="clearfix"></div>
