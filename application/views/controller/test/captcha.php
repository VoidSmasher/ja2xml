<?php defined('SYSPATH') or die('Access denied.');

/**
 * User: legion
 * Date: 04.06.12
 * Time: 17:42
 */
?>
<section class="shape size-full b-user-block">
	<div class="shape-top">
		<h3>Проверка Captcha</h3>
	</div>
	<div class="shape-text">
		<div class="user-profile">
			<div class="user-profile-description user-forms">
				<?php echo Form::open('/test/public/captcha.json', array('id' => 'captcha_test')); ?>
				<p>
					<?php echo $captcha; ?>
				</p>
				<p>
					<?php echo Form::label('captcha', 'Введите текст с картинки (5 символов)'); ?>
					<?php echo Form::input('captcha', NULL, array('id' => 'captcha')); ?>
				</p>
				<div class="form-item">
					<button type="submit" class="btn btn-default">Проверить</button>
				</div>
				<p class="guest">
					Все буквы - латинские, регистр не имеет значения. Чтобы пройти тест заново - обновите страницу (кнопка F5).<br/>
					Если Вы уверены, что ввели текст правильно, но тест выдал результат "Не верно", то сделайте, пожалуйста, cледующее: еще раз нажмите кнопку "Проверить", если ответ все еще "Не верно" - сделайте ALt+PrintScreen, получившийся снимок экрана через Paint (или другую программу) сохраните в виде png-файла и передайте его разработчикам.
				</p>
				<br/>
                <p id="test_result_true" style="color:green; display:none; font-size: 150%;">ВЕРНО</p>
				<p id="test_result_false" style="color:red; display:none;">Не верно, если Вы уверены в своем ответе - сделайте снимок экрана!</p>
				<?php echo Form::close(); ?>
			</div>
		</div>
	</div>
</section>

