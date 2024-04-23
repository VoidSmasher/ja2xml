/**
 * User: legion
 * Date: 10/22/11
 * Time: 4:20 AM
 */
$(document).ready(function () {

	if (migration !== undefined) {
		with (migration_class = function (migration) {
			this.set_percent(0);
			this.update_migration_result(0);
			this.start_path = migration.start_path;
			this.process_path = migration.process_path;
			this.finish_path = migration.finish_path;
			this.total_steps = migration.total_steps;
			this.message_before_start = migration.message_before_start;
			this.message_before_finish = migration.message_before_finish;
			this.message_start = migration.message_start;
			this.message_success = migration.message_success;
			this.message_fail = migration.message_fail;
			this.button_start = migration.button_start;
		}) {

			prototype.process_path = '';
			prototype.start_path = '';
			prototype.finish_path = '';

			prototype.message_before_start = '';
			prototype.message_before_finish = '';
			prototype.message_start = '';
			prototype.message_success = '';
			prototype.message_fail = '';

			prototype.button_start = 'Start';

			prototype.progress_bar = $('#migration_progress');
			prototype.migration_button = $('#start_migration');
			prototype.migration_messages = $('#migration_messages');
			prototype.migration_timer = new pretty_timer($('#migration_timer'));
			prototype.migration_count = $('#migration_count');
			prototype.migration_result = $('#migration_result');
			prototype.migration_result_value = 0;

			prototype.total_steps = 0;
			prototype.step = 1;

			prototype.on_pause = false;
			prototype.in_progress = false;

			prototype.proceed = function () {
				if (this.in_progress && !this.on_pause) {
					var obj = this;
					$.ajax({
						type: 'POST',
						url: obj.process_path,
						dataType: 'json',
						data: {ajax: true, mode: 'process', step: obj.step},
						success: function (data) {
							if (data.changes_count) {
								obj.update_migration_result(data.changes_count);
								obj.update_percent();
							}
							obj.show_messages(data.messages);
							if (data.stop) {
								obj.stop(data.error);
							} else {
								obj.step = obj.step + data.changes_count;
								if (obj.step > obj.total_steps) {
									if (obj.finish_path.length > 0) {
										obj.after();
									} else {
										obj.stop(data.error);
									}
								} else {
									obj.proceed();
								}
							}
						}
					});
				}
			};

			prototype.show_message = function (message, type) {
				if (message) {
					if (!type) {
						type = 'default';
					}
					date = new Date();
					date = date.toLocaleString();

					date = $('<span/>', {
						class: 'label label-' + type,
						text: date
					});

					var item = $('<div/>', {
						class: 'list-group-item list-group-item-' + type,
						text: ' ' + message
					}).hide().prepend(date);

					this.migration_messages.append(item);
					this.migration_messages.find('div:last-child').fadeIn();
					this.migration_messages.scrollTop(this.migration_messages.children().length * 1000);
				}
			};
			prototype.show_messages = function (messages) {
				var obj = this;
				$.each(messages, function (index, data) {
					obj.show_message(data.message, data.type);
				});
			};

			prototype.before = function () {
				var obj = this;
				obj.show_message(obj.message_before_start, 'warning');
				$.ajax({
					type: 'POST',
					url: obj.start_path,
					dataType: 'json',
					data: {ajax: true, mode: 'start', step: obj.step},
					success: function (data) {
						obj.show_messages(data.messages);
						if (data.total_steps) {
							obj.update_migration_count(data.total_steps);
						}
						if (data.stop || obj.total_steps <= 0) {
							obj.stop(data.error);
						} else {
							obj.show_message(obj.message_start, 'warning');
							obj.proceed();
						}
					}
				});
			};
			prototype.after = function () {
				this.migration_button.attr('disabled', 'disabled');
				var obj = this;
				obj.show_message(obj.message_before_finish, 'warning');
				$.ajax({
					type: 'POST',
					url: obj.finish_path,
					dataType: 'json',
					data: {ajax: true, mode: 'finish', step: obj.step},
					success: function (data) {
						obj.show_messages(data.messages);
						obj.stop(data.error)
					}
				});
			};

			prototype.start = function () {
				this.update_migration_result(0);
				this.update_migration_count(0);
				this.set_percent(0);
				this.in_progress = true;
				this.migration_messages.find('div').remove();
				this.migration_timer.start();
				if (this.start_path.length > 0) {
					this.before();
				}
			};
			prototype.stop = function (error) {
				this.set_percent(100);
				this.in_progress = false;
				this.migration_timer.stop();
				this.migration_button.removeAttr('disabled');
				this.migration_button.val(this.button_start);
				if (error) {
					this.show_message(this.message_fail, 'danger');
				} else {
					this.show_message(this.message_success, 'success');
				}
			};

			prototype.set_percent = function (percent) {
				this.progress_bar.css('width', percent + '%');
			};
			prototype.update_percent = function () {
				percent = Math.round(this.step * 100 / this.total_steps);
				if (percent > 100) {
					percent = 100;
				}
				if (percent < 0) {
					percent = 0;
				}
				this.set_percent(percent);
			};

			prototype.update_migration_result = function (value) {
				value = parseInt(value);
				if (value < 0) {
					value = 0;
				}
				this.migration_result_value = this.migration_result_value + value;
				this.migration_result.html(this.migration_result_value);
			};

			prototype.update_migration_count = function (value) {
				value = parseInt(value);
				if (value < 0) {
					value = 0;
				}
				this.total_steps = value;
				this.migration_count.html(this.total_steps);
			};
		}

		var migration_object = new migration_class(migration);

		$('#start_migration').click(function () {
			var button_pause = 'Pause';
			if (migration.button_pause.length > 0) {
				button_pause = migration.button_pause;
			}
			var button_continue = 'Continue';
			if (migration.button_continue.length > 0) {
				button_continue = migration.button_continue;
			}
			if (migration_object.in_progress) {
				if (migration_object.on_pause) {
					$(this).val(button_pause);
					migration_object.on_pause = false;
					migration_object.proceed();
				} else {
					migration_object.on_pause = true;
					$(this).val(button_continue);
				}
			} else {
				$(this).val(button_pause);
				migration_object = new migration_class(migration);
				migration_object.start();
			}
		});
	}

});