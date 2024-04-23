'use strict';

var lib = require('../index.js'),
	$ = require('jquery'),
	Dots = require('./Dots'),
	MinimalClass = require('../MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'SliderItem',
	pre: function(opt)
	{
		this.img = false;
		this.width = 0;
		this.height = 0;
		this.w = 0;
		this.h = 0;
		this.is_loaded = false;
		this.is_loading = false;
		this.is_error = false;
		this.setup_mode = 'background';
	},
	create: function()
	{
		var self = this;
		this.src = this.element.data('src') || false;
		this.image = this.element.find('.image');
		this.active = this.element.hasClass('active');
	},
	load: function(cb)
	{
		if( this.src ) {
			var self = this;
			this.loading(true);
			this.img = $('<img/>');
			this.img.appendTo(window.LZ).bind('load error', function(e){ self.loaded(cb,e.type); }).attr('src',this.src);
		}else{
			this.loaded(cb);
		}
	},
	loading: function(dir)
	{
		if( this.is_loading == dir ) return;

		if(dir){
			this.element.addClass('loading');
		}else{
			this.element.removeClass('loading');
		}

		this.is_loading = dir;
	},
	loaded: function(cb,type)
	{
		this.loading(false);
		this.is_loaded = true;

		switch(type) {
			case 'load':
				this.width = this.img ? this.img[0].width || 0 : 0;
				this.height = this.img ? this.img[0].height || 0 : 0;
				this.setup();
				break;
			case 'error':
				this.is_error = true;
				break;
			default:
				this.setup();
				break;
		}

		if( this.onLoad === 'function' ){
			this.onLoad(this);
		}

		if( this.delegate ) {
			this.delegate.element.trigger('item_loaded.slider',[this]);
		}
	},
	setup: function()
	{
		switch(this.setup_mode){
			case 'background.image':
				if( !this.src ) return;
				this.image = $('<DIV>').addClass('image').css({ backgroundImage: 'url(' + this.src + ')'}).appendTo(this.element);
				break;
			case 'background':
			default:
				if( !this.src ) return;
				this.element.css({ backgroundImage: 'url(' + this.src + ')' });
				break;
		}
	},
	activate: function(dir,from_user)
	{
		if( this.active === dir ) return;

		if( dir ){
			this.element.addClass('active');
		}else{
			this.element.removeClass('active');
		}

		this.active = dir;
	},
	remove: function()
	{
		this.element.remove();
	}
});