'use strict';

var $ = require('jquery'),
	VimeoPlayer = require('./VimeoPlayer');

module.exports = VimeoPlayer.extend({
	__className: 'VimeoPlayerPopper',
	_create: function()
	{
		var self = this;
		this.shouldUnfixContent = false;
		this.vimeo_player_loaded = false;

		this.element.addClass('popper');
		this.scalable_circle = $('<div>').addClass('scalable-circle').appendTo(this.element);
		if(this.scalable_circle_css_class) {
			this.scalable_circle.addClass(this.scalable_circle_css_class);
		}

		this.loading_timer = false;

		this.onEnded = function(){
			if( self.fullscreen(false) ) {
				self.close(null,true);
			}else{
				self.close();
			}
		};

		this.pageX = 0;
		this.pageY = 0;

		if( this.autoopen ) {
			this.open();
		}
	},
	loaded: function()
	{
		this.vimeo_player_loaded = true;
		if( this.loading_timer ) {
			return;
		}

		this.resize();
		this.element.removeClass('loading');
		
		if(this.autoplay && this.opened){
			this.play();
			this.element.addClass('show');
		}

		if( typeof this.onLoaded === 'function' ) { this.onLoaded(this); }
	},
	delay_loaded: function(){
		clearTimeout(this.loading_timer);
		this.loading_timer = false;

		if( this.vimeo_player_loaded ) {
			this.loaded();
		}
	},
	open: function(e)
	{
		this.element.addClass('open');
		this.opened = true;

		var self = this,
			pos = this.trigger[0].getBoundingClientRect(),
			width = Math.max(pos.width,60),
			height = Math.max(pos.height,60),
			top = pos.top,
			left = pos.left;

		if( width < 60 || height < 60 ) {
			height = width = 60;
			top = Math.round(top - (height - pos.height)/2);
			left = Math.round(left - (width - pos.width)/2);
		}

		if( typeof e !== 'undefined' ) {
			this.pageX = e.pageX;
			this.pageY = e.pageY;
		}

		this.scalable_circle.css({
			top: top,
			left : left,
			width: width,
			height: height,
			transform: 'scale(0)'
		}).animate({ scale: 40 },{
			duration: 1000,
			easing: 'swing',
			step: function(now, fx){
				self.scalable_circle.css({ transform: 'scale('+now+')' });
			},
			complete: function(){
				self.loader.show();
				self.element.addClass('loading');

				self.shouldUnfixContent = window.setFixedContent(true);

				if( self.player ) {
					self.player.setCurrentTime(0);
					self.play();
					self.element.addClass('show');
				}else{
					if( typeof self.loader_text !== 'undefined' && self.loader_text.length ) {
						self.loading_timer = setTimeout(function(){ self.delay_loaded(); }, 3000);
					}
					self.create_player();
				}

				if( typeof self.onOpen === 'function' ) {
					self.onOpen(this,e);
				}
			}
		});
	},
	close: function(e,quick)
	{
		var self = this,
			pos = this.trigger[0].getBoundingClientRect(),
			width = Math.max(pos.width,60),
			height = Math.max(pos.height,60),
			top = pos.top,
			left = pos.left;

		if( width < 60 || height < 60 ) {
			height = width = 60;
			top = Math.round(top - (height - pos.height)/2);
			left = Math.round(left - (width - pos.width)/2);
		}

		if( typeof e == 'undefined' ) {
			e = { pageX: this.pageX, pageY: this.pageY };
		}

		this.pause();

		this.element.removeClass('loading show');
		this.loader.hide();
		this.opened = false;

		if( this.shouldUnfixContent ) {
			window.setFixedContent(false);
		}

		if( quick ) {
			this.scalable_circle.css({ transform: 'scale(0)' });
			this.element.removeClass('open');

			if( typeof this.onClose === 'function' ) {
				this.onClose(this,e);
			}
		}else{
			this.scalable_circle.css({
				top: pos.top,
				left : pos.left,
				width: Math.max(60,pos.width),
				height: Math.max(60,pos.height)
			}).animate({ scale: 0 },{
				duration: 1000,
				easing: 'swing',
				step: function(now, fx){
					self.scalable_circle.css({ transform: 'scale('+now+')' });
				},
				complete: function(){
					self.element.removeClass('open');

					if( typeof self.onClose === 'function' ) {
						self.onClose(self,e);
					}
				}
			});
		}
	}
});