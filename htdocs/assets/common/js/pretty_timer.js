with(pretty_timer = function(timer_object){
	this.timer_object = timer_object;
	this.draw_time();
}){
	prototype.timer_object = undefined;
	prototype.timer_id = null;
	prototype.elapsed_seconds = 0;
	prototype.start = function() {
		if ((this.timer_object !== undefined) && (this.timer_object.length > 0)) {
			obj = this;
			this.timer_id = setInterval(function() {
				obj.elapsed_seconds = obj.elapsed_seconds + 1;
				obj.draw_time();
			}, 1000);
		}
	};
	prototype.draw_time = function() {
		text = this.get_elapsed_time_string(this.elapsed_seconds);
		this.timer_object.text(text);
	};
	prototype.stop = function() {
		if (this.timer_id !== null) {
			clearInterval(this.timer_id);
			this.elapsed_seconds = 0;
		}
	};
	prototype.get_elapsed_time_string = function(total_seconds) {
		function pretty_time_string(num) {
			return ( num < 10 ? "0" : "" ) + num;
		}

		var hours = Math.floor(total_seconds / 3600);
		total_seconds = total_seconds % 3600;

		var minutes = Math.floor(total_seconds / 60);
		total_seconds = total_seconds % 60;

		var seconds = Math.floor(total_seconds);

		// Pad the minutes and seconds with leading zeros, if required
		hours = pretty_time_string(hours);
		minutes = pretty_time_string(minutes);
		seconds = pretty_time_string(seconds);

		// Compose the string for display
		return hours + ":" + minutes + ":" + seconds;
	};
}