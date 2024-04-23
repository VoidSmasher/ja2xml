'use strict';

var $ = require('jquery'),
	ForceScroll = require('./ForceScroll'),
	MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'Overlay',
	pre: function(){
		this.popup = false;
		this.content = false;
	},
	create: function(){
		var self = this;

		this.mode = this.element.data('mode') || 'default';

		this.opened = false;
		this.content_fixed_by_this_overlay = false;

		if(!this.popup) {
			this.popup = this.element.find('.js-popup');
		}

		if(!this.content) {
			this.content = this.element.find('.js-content');
		}

		this.scroll = false;
		if( typeof ForceScroll !== 'undefined' ) {
			this.scrolling_text = this.popup.find('.js-scrolling-text');
			if( this.scrolling_text.length ) {
				this.scroll = new ForceScroll({ element: this.scrolling_text, delegate: self });
			}
		}

		this.element.find('.js-close').click(function(e){
			self.close();
		});

		this.element.click(function(e){
			if(!self.opened) { return; }
			if( e.target === this ) {
				self.close();
			}
		});

		window.app.add_resize(this);
	},
	open: function(cb){
		var self = this;

		this.content_fixed_by_this_overlay = window.setFixedContent(true);

		this.element.addClass('resizable');
		this.resize(window.app.ww,window.app.wh);
		this.opened = true;
		setTimeout(function(){
			self.on_open(cb);
		},25);
	},
	on_open: function(cb){
		this.element.addClass('open');
		if( typeof cb === 'function' ) {
			cb(this);
		}
		if( typeof this.onOpen === 'function' ) {
			this.onOpen(this);
		}
	},
	close: function(cb){
		var self = this;
		this.element.removeClass('open');
		setTimeout(function(){
			self.on_close(cb);
		},500);
	},
	on_close: function(cb){
		this.element.removeClass('resizable');
		if( typeof cb === 'function' ) {
			cb(this);
		}
		if( typeof this.onClose === 'function' ) {
			this.onClose(this);
		}
		if( this.content_fixed_by_this_overlay ) {
			window.setFixedContent(false);
		}
		this.opened = false;
	},
	resize: function(ww,wh){
		this.popup.css({ position: 'absolute' });

		if( this.scroll ) {
			this.scroll.resize();
		}

		this.width = this.popup.outerWidth(true);
		this.height = this.popup.outerHeight(true);

		if( this.height < ( $(window).height() - 120 ) ){
			var top = Math.round( ( wh - this.height ) / 2 ),
				left = Math.round( ( ww - this.width ) / 2 );
			this.popup.css({ position: 'absolute', top: top, left: left });
		}else{
			this.popup.css({ position: '', top: '', left: '' });
		}
	},
	set_content: function(html,cb){
		this.content.empty().html(html);
		if( typeof cb === 'function' ) {
			cb(this.content);
		}
		return this
	}
});