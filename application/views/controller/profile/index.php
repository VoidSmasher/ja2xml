<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 04.09.14
 * Time: 20:08
 */
$required = '<span class="text-danger">*</span> ';
?>
<legend><?php echo __('auth.title.personal_data'); ?></legend>

<?php echo Form::open(null, array(
	'role' => 'form',
	'class' => 'form-horizontal fixed-width',
)); ?>

<div class="personal-data">
	<div class="form-group<?php if (Helper_Error::has_error('surname')) echo ' has-error'; ?>">
		<?php echo Form::label('surname', $required . __('user.surname'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::input('surname', $user_data['surname'], array(
				'id' => 'surname',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>

	<div class="form-group<?php if (Helper_Error::has_error('name')) echo ' has-error'; ?>">
		<?php echo Form::label('name', $required . __('user.name'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::input('name', $user_data['name'], array(
				'id' => 'name',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>

	<div class="form-group<?php if (Helper_Error::has_error('patronymic')) echo ' has-error'; ?>">
		<?php echo Form::label('patronymic', __('user.patronymic'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::input('patronymic', $user_data['patronymic'], array(
				'id' => 'patronymic',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>
</div>

<div class="registry-data">
	<div class="form-group<?php if (Helper_Error::has_error('login')) echo ' has-error'; ?>">
		<?php echo Form::label('username', $required . __('user.login'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::input('username', $user_data['username'], array(
				'id' => 'username',
				'class' => 'form-control'
			)); ?>
		</div>
	</div>

	<div class="form-group<?php if (Helper_Error::has_error('email')) echo ' has-error'; ?>">
		<?php echo Form::label('email', $required . __('user.email'), array(
			'class' => 'col-sm-5 control-label',
		)); ?>
		<div class="col-sm-7">
			<?php echo Form::input('email', $user_data['email'], array(
				'id' => 'email',
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

<div class="form-group">
	<div class="col-sm-offset-5 col-sm-7">
		<span class="help-block"><?php echo $required . __('form.marked_fields_are_required'); ?></span>
	</div>
</div>

<div class="form-group">
	<div class="col-sm-7 col-sm-push-5">
		<button type="submit" class="btn btn-primary"><?php echo __('form.button.save'); ?></button>
	</div>
</div>
<?php echo Form::close(); ?>
