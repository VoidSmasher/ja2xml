'use strict';

var $ = require('jquery'),
	MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'UnderScreen',
	pre: function(opt){
		this.ww = 0;
		this.wh = 0;
		this.hide_body_overflow = false;
	},
	create: function(){
		var self = this;

		this.opened = false;

		this.scale = 1;
		this.translateY = 0;

		this.screen = $('#screen');

		this.onCloseMenu = function(e){
			e.preventDefault();
			e.stopPropagation();
			self.toggle(false);
		};

		this.element.find('.js-close').bind('click',this.onCloseMenu);

		window.app.add_resize(this);
	},
	resize: function(ww,wh){
		this.ww = ww;
		this.wh = wh;

		var scale = ( ww - 310 ) / ww;
		if( wh - wh * scale < 170 ) {
			scale = (wh - 170) / wh;
		}

		this.scale = scale;
		this.set_scale();
	},
	set_scale: function(){
		if( this.opened ) {
			this.screen.css({ transform: 'scale('+this.scale+')' });
		}
	},
	toggle: function(dir){
		if( typeof dir === 'undefined' ) dir = !this.opened;
		if( dir === this.opened ) return;
		if( dir ) {
			this.open();
		}else{
			this.close();
		}
	},
	open: function(){
		this.opened = true;
		window.setFixedContent(true,false,this.hide_body_overflow);

		window.page_header && window.page_header.set_absolute(true, window.contentFixedAt);

		this.element.addClass('open');

		this.set_scale();

		var self = this;
		setTimeout(function(){ self.screen.bind('click',self.onCloseMenu); },0);
	},
	close: function(){
		this.opened = false;

		this.screen.css({ transform: '' });
		var self = this;
		setTimeout(function(){
			window.setFixedContent(false);
			window.page_header && window.page_header.set_absolute(false,0);
			self.element.removeClass('open');
		},780);

		this.screen.unbind('click',this.onCloseMenu);
	}
});