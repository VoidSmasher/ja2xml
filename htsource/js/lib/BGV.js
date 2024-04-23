'use strict';

var $ = require('jquery'),
	MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
	pre: function(){
		this.tmr = false;
		this.loading = false;
		this.loaded = false;
		this.playing = false;
		this.videoPreloadPrc = 0;
	},
	create: function(){
		var self = this;

		if( this.element[0].tagName === 'VIDEO' ) {
			this.$v = this.element;
		}else{
			this.$v = this.element.find('video');
		}

		this.v = this.$v[0];

		this.tid = this.v.getAttribute('tid');
		this.vw = parseInt(this.v.getAttribute('data-width'));
		this.vh = parseInt(this.v.getAttribute('data-height'));

		this.siv = false;
		this.siving = parseInt(this.v.getAttribute('data-siv'));
		this.shouldPlay = parseInt(this.v.getAttribute('data-autoplay'));
		this.autoload = parseInt(this.v.getAttribute('data-autoload'));

		if(!this.siving && this.autoload) this.preload();
	},
	preload: function(cb)
	{
		if(this.loading) return;
		this.loading = true;

		// this.log('Preload:',this.tid);

		if( this.v.getAttribute('preload-by-poster') )
		{
			var self = this,
				file = this.v.getAttribute('poster').replace('.jpg','');

			this.$v.bind('loadedmetadata', function(e){ self.handleLoadingProcess(cb); });

			this.addSourceToVideo( this.v, file + ".mp4", "video/mp4");
			this.addSourceToVideo( this.v, file + ".ogv", "video/ogv");
			this.addSourceToVideo( this.v, file + ".webm", "video/webm");
			return;
		}

		this.handleLoadingProcess(cb);
	},
	addSourceToVideo: function(elm,src,type) {
		var s = document.createElement('source');
		s.src = src;
		s.type = type;
		elm.appendChild(s);
	},
	handleLoadingProcess: function(cb)
	{
		var self = this, $v = this.$v, v = this.v,
			func = function(e){
				var preloadPrc = self.progressHandler(e,v,5);
				self.videoPreloadPrc = (preloadPrc > 5) ? 1 : (preloadPrc / 5);

				if(preloadPrc >= 5){
					$v.unbind('progress',func);
					$v.addClass('loaded');
					self.loadingComplete(cb);
				}
			};

		// this.element.bind('progress',func);
		this.tmr = setInterval(func,50);
		this.v.play();
	},
	progressHandler: function(e,v,trg) {
		try {
			if( v.duration )
			{
				var prc = ( v.buffered.end(0) / v.duration ) * 100;
				if( v.currentTime >= 10 ) return 1;
				return prc;
			}
		}catch(e){}
		return 0;
	},
	loadingComplete: function(cb) {
		this.loading = false;
		this.loaded = true;
		this.v.currentTime = 0;

		clearInterval(this.tmr);
		this.tmr = null;

		// this.log('Loading complete:',this.tid);

		if( this.shouldPlay && ( !this.siving || this.siv ) ) {
			this.play();
		}else{
			this.v.pause();
		}

		typeof cb === 'function' && cb();
	},
	setShouldPlay: function(shouldPlay)
	{
		this.shouldPlay = shouldPlay;
		if( shouldPlay ){
			if( ( !this.siving || this.siv ) ) this.play();
		}else this.pause();
	},
	play: function(cb)
	{
		if( !this.shouldPlay ) return;
		if( this.playing ) return;
		if( !this.loaded ){
			return this.preload(cb);
		}else{
			this.v.play();
			this.element.addClass('playing');
			this.playing = true;
			typeof cb === 'function' && cb();
		}

		// this.log('Play:',this.tid);
	},
	pause: function() {
		if( !this.playing ) return;
		if( this.loaded )
		{
			this.v.pause();
			this.playing = false;
			this.element.removeClass('playing');
		}

		// this.log('Paused:',this.tid);
	},
	scroll: function() {
		if(!this.siving) return;
		var wh = window.innerHeight, r = this.v.parentNode.getBoundingClientRect(), earlyStart = -Math.round(wh * .1), laterFinish = -Math.round(wh * .25);
		this.siv = ( r.top - earlyStart <= wh ) && ( r.top + laterFinish >= -r.height );
		if(this.siv) this.play(); else if( this.playing ) this.pause();
	},
	resize: function() {
		var v = this.v,
			r = v.parentNode.getBoundingClientRect(), t, l, w = r.width, h = Math.round( w / this.vw * this.vh );

		if(h < r.height)
			w = Math.round( ( h = r.height ) / this.vh * this.vw );

		t = Math.round( ( r.height - h ) / 2 );
		l = Math.round( ( r.width - w ) / 2 );

		this.$v.css({ width : w, height : h, top : t, left : l });
	}
});