'use strict';
var $ = require('jquery'),
	MinimalClass = require('./MinimalClass'),
	BGV = require('./BGV');

module.exports = MinimalClass.extend({
	create: function() {
		var self = this;
		this.videos = [];
		this.videos_by_tids = {};

		$('.js-bg-video').each(function(i,elm){
			var video = new BGV({ id: i+1, element: elm });
			self.videos.push( video );
			video.tid && ( self.videos_by_tids[video.tid] = video );
		});

		window.app.add_resize(this);
		window.app.add_scroll(this);
	},
	resize: function(ww,wh){
		$(this.videos).each(function(i,video){ video.resize(ww,wh); });
	},
	scroll: function(scrollTop){
		$(this.videos).each(function(i,video){ video.scroll(scrollTop); });
	},
	get_video_by_tid: function(tid){
		return typeof this.videos_by_tids[tid] !== 'undefined' ? this.videos_by_tids[tid] : false;
	}
});