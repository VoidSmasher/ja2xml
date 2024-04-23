'use strict';

var $ = require('jquery'),
	WebFont = require('webfontloader'),
	MinimalClass = require('./MinimalClass'),
	ContentFix = require('./ContentFix'),
	ScrollActionSimple = require('./ScrollActionSimple'),
	BGVideos = require('./BGVideos');

// require('smoothscroll-polyfill').polyfill();

module.exports = MinimalClass.extend({
	__className: 'App',
	_resizable: [],
	_scrollable: [],
	_focusable: [],
	_instances: {},
	pre: function(){
		var self = this;

		this.lang = $('html').attr('lang');
		this.fonts_loaded = false;
		this.inited = false;

		this.focused = true;

		this.loading_zone = document.getElementById('LZ');

		if(!this.loading_zone) {
			this.loading_zone = $('<div></div>', { id: 'LZ' }).addClass('LZ').appendTo($(document.body))
		}else{
			this.loading_zone = $(this.loading_zone);
		}

		window.LZ = this.loading_zone;
	},
	create: function () {
		var self = this;

		window.app = this;
		this.update_window_size();

		this.ww = 0;
		this.wh = 0;
		this.page_height = 0;
		this.scrollTop = 0;
		this.previousScrollTop = 0;
		this.scrollDir = true;

		this.screen = $('#screen');
		this.content = $('#content');

		this.cfix = new ContentFix();
		this.sas = new ScrollActionSimple();

		$(window).focus(function(e){ self.focus(true); });
		$(window).blur(function(e){ self.focus(false); });

		$(window).resize(function (e) {
			self.resize(e);
		});

		$(window).scroll(function (e) {
			self.scroll(e);
		});

		$(document).ready(function (e) {
			self.resize(e);
			self.scroll(e);
		});

		this.resize();
		this.scroll();

		this.setup_loader();

		setTimeout(function(){
			self.setup();
		}, 10);
	},
	setup_loader: function(){

	},
	setup: function () {
		var self = this;
		WebFont.load({
			custom: {
				families: ['DinTextComp', 'PT Serif']
			},
			active: function () {
				self.fonts_loaded = true;
				self.resize();
			}
		});

		new BGVideos();
	},
	add_resize: function (instance) {
		for (var i = 0; i < this._resizable.length; i++) {
			if (this._resizable[i] === instance) {
				return;
			}
		}

		this._resizable.push(instance);
		if (this.ww && this.wh) {
			instance.resize(this.ww, this.wh);
		}
	},
	remove_resize: function (instance) {
		var newResizable = [], found = false;
		for (var i = 0; i < this._resizable.length; i++) {
			if (this._resizable[i] === instance) {
				found = true;
			} else {
				newResizable.push(instance);
			}
		}
		if (found) {
			this._resizable = newResizable;
		}
	},
	add_scroll: function (instance) {
		for (var i = 0; i < this._scrollable.length; i++) {
			if (this._scrollable[i] === instance) {
				return;
			}
		}

		this._scrollable.push(instance);
		instance.scroll(this.scrollTop);
	},
	remove_scroll: function (instance) {
		var newScrollable = [], found = false;
		for (var i = 0; i < this._scrollable.length; i++) {
			if (this._scrollable[i] === instance) {
				found = true;
			} else {
				newScrollable.push(instance);
			}
		}
		if (found) {
			this._scrollable = newScrollable;
		}
	},
	add_focus: function (instance) {
		for (var i = 0; i < this._focusable.length; i++) {
			if (this._focusable[i] === instance) {
				return;
			}
		}

		this._focusable.push(instance);
	},
	remove_focus: function (instance) {
		var newFocusable = [], found = false;
		for (var i = 0; i < this._focusable.length; i++) {
			if (this._focusable[i] === instance) {
				found = true;
			} else {
				newFocusable.push(instance);
			}
		}
		if (found) {
			this._focusable = newFocusable;
		}
	},
	update_window_size: function(){
		this.ww = $(window).width();
		this.wh = $(window).height();
	},
	resize: function () {
		this.update_window_size();

		// if (window.innerWidth < 570) {
		// 	window.location.href = '/mobile';
		// }

		for (var i = 0; i < this._resizable.length; i++) {
			this._resizable[i].resize(this.ww, this.wh);
		}

		this.update_page_height(true);
	},
	update_page_height: function(auto){
		this.page_height = this.content.outerHeight(true);
	},
	scroll: function () {
		this.scrollTop = $(window).scrollTop();
		this.scrollDir = this.scrollTop > this.previousScrollTop;
		this.previousScrollTop = this.scrollTop;
		for (var i = 0; i < this._scrollable.length; i++) {
			this._scrollable[i].scroll(this.scrollTop, this.scrollDir);
		}
	},
	focus: function(dir){
		if( this.focused !== dir ) {
			this.focused = dir;
			for (var i = 0; i < this._focusable.length; i++) {
				this._focusable[i].focus(dir);
			}
		}
	},
	load_svg_sprite: function(sprite){
		var self = this,
			xhr = new XMLHttpRequest();
		xhr.open('GET',sprite,true);
		xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
		xhr.onreadystatechange = function(){
			if(xhr.readyState !== 4) return void 0;
			var d = document.createElement('DIV');
			d.innerHTML = xhr.responseText;
			self.loading_zone[0].appendChild(d);
		};
		xhr.send();
	},
	put_instance: function(instance){
		if( typeof this._instances[instance.__className] === 'undefined' ) {
			this._instances[instance.__className] = [];
		}

		for(var i=0,count=this._instances[instance.__className].length;i<count;i++) {
			if( this._instances[instance.__className][i] === instance ) {
				return false;
			}
		}

		var id = this._instances[instance.__className].length;
		this._instances[instance.__className].push(instance);

		return id;
	},
	get_instances: function(className) {
		if( typeof this.classes[className] === 'undefined' ) {
			return false;
		}
		return this._instances[className];
	},
	remove_instance: function(instance) {
		if( typeof this._instances[instance.__className] === 'undefined' ) {
			return false;
		}

		var new_instances = [], found = false;
		for(var i=0,count=this._instances[instance.__className].length;i<count;i++) {
			if( this._instances[instance.__className][i] !== instance ) {
				newInstances.push( this._instances[instance.__className][i] );
			}else{
				found = true;
			}
		}

		if( found ) {
			this._instances[instance.__className] = new_instances;
		}

		return found;
	}
});