'use strict';

var $ = require('jquery'),
	VimeoPlayer = require('@vimeo/player'),
	Loader = require('./Loader'),
	MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'VimeoPlayer',
	
	_players: [],
	_pause_other_players: function()
	{
		var self = this;
		this._players.forEach(function(elm,i){
			if( elm != self ) {
				elm.pause();
			}
		});
	},

	pre: function(opt)
	{
		this.id = 0;

		this.player_options = { byline: false, portrait: false, title: false, loop: false };

		this.onOpen = false;
		this.onClose = false;
		this.onPlay = false;
		this.onEnded = false;
		this.onReady = false;
		this.onLoaded = false;

		this.width = 0;
		this.height = 0;
		this.video_width = 0;
		this.video_height = 0;
		this.resizeMode = 'fs';

		this.ready = false;
		this.playing = false;
		this.opened = false;
		this.is_loaded = false;

		this.autoplay = true;
		this.autoopen = false;

		this.css_class = false;

		this.closeBtn = false;

	},
	create: function()
	{
		var self = this;
		this._players.push(this);

		if( !this.element ) {
			this.element = $('<div>').addClass('vimeo-player');
			if(this.id){ this.element.appendTo(document.body); }

			this.closeBtn = $('<div>').addClass('arr close js-close');
			this.closeBtn.append( '<svg class="circle"><use xlink:href="#circle"></use></svg>' );
			this.closeBtn.append( '<svg class="icon"><use xlink:href="#close"></use></svg>' );
			this.closeBtn.appendTo(this.element);

			this.loader = new Loader({
				_target: this.element,
				_css: 'white',
				_big: ( typeof this.loader_text != 'undefined' ),
				_text: this.loader_text
			});
		}else{
			this.closeBtn = this.find('.js-close');
			if( !this.closeBtn.length ) this.closeBtn = false;
			if(!this.id) {
				this.id = this.element.attr('vimeo-id') || 0;
			}
		}

		if(this.id) {
			this.player_options.id = this.id;
		}else{
			this.log('Unable to create vimeo player: no id');
			return;
		}

		if(this.closeBtn) {
			this.closeBtn.click(function(e){
				self.close(e);
			});
		}

		this.player_box = $('<div>').addClass('player-box').appendTo(this.element);

		if(this.css_class) {
			this.element.addClass(this.css_class);
		}

		if( this.auto_create_player ) {
			this.create_player();
		}
	    
	    this._create();

	    window.app.add_resize(this);

		if(this.scalable_circle_css_class) {
			this.closeBtn.addClass(this.scalable_circle_css_class);
		}
	},
	create_player: function()
	{
		var self = this;

		this.player = new VimeoPlayer(this.player_box[0], this.player_options);
		this.player.setLoop(false);

	    this.player.on('loaded', function() {
	        self.player.getVideoWidth().then(function(width){ self.video_width = width; self.resize(); }).catch(function(error){});
	        self.player.getVideoHeight().then(function(height){ self.video_height = height; self.resize(); }).catch(function(error){});
	        self.is_loaded = true;
	        self.loaded();
	    });
	    
	    this.player.on('play', function() {
	        self.playing = true;
	        if( typeof self.onPlay === 'function' ) { self.onPlay(this); }
	    });

	    this.player.on('pause', function() {
	        self.playing = false;
	        if( typeof self.onPause === 'function' ) { self.onPause(this); }
	    });

	    this.player.on('ended', function() {
	        self.playing = false;
	        if( typeof self.onEnded === 'function' ) { self.onEnded(this); }
	    });

	    this.element.bind('click',function(e){
	    	if( e.target != this ) return;
	    	self.close();
	    });
	},
	_create: function(){},
	resize: function()
	{
		var ww = this.element.width(),
			wh = this.element.height();

		switch(this.resizeMode){
			case 'fs':
				this.width = ww;
				this.height = wh;
				break;
			default:
				if( !this.video_width || !this.video_height ) {
					return false;
				}

				this.width = Math.round(ww * .8);
				this.height = Math.round(this.width / this.video_width * this.video_height);

				if( this.height > wh * .8 ) {
					this.height = Math.round(wh * .8);
					this.width = Math.round( this.height / this.video_height * this.video_width );
				}
				break;
		}


		this.player_box.find('iframe').css({
			width: 960,
			height: 540
		});

		if( !this.ready ) {
			this.ready = true;
			this.element.trigger('ready', [ this ]);
	        if( typeof self.onReady === 'function' ) { self.onReady(this); }
		}
	},
	loaded: function()
	{
		this.resize();
		if(this.autoopen){
			this.open();
		}else if(this.autoplay && this.opened){
			this.play();
			this.element.addClass('show');
		}

		if( typeof this.onLoaded === 'function' ) { this.onLoaded(this); }
	},
	play: function()
	{
		this._pause_other_players();
		this.loader.hide();

		if(!this.player) {
			return this.create_player();
		}

		if( this.playing ) return;
		this.player.play();
	},
	pause: function()
	{
		if( !this.playing ) return;
		this.player.pause();
	},
	open: function(e)
	{
		this.element.addClass('open show');
		this.opened = true;

		this.openingComplete(e);
	},
	openingComplete: function(e)
	{
		if(this.autoplay) {
			this.play();
		}

		if( typeof this.onOpen === 'function' ) {
			this.onOpen(this,e);
		}
	},
	close: function(e)
	{
		this.element.removeClass('show open');
		this.opened = false;

		this.pause();

		this.closingComplete(e);
	},
	closingComplete: function(e)
	{
		if( typeof this.onClose === 'function' ) {
			this.onClose(this,e);
		}
	},
	remove: function()
	{
		var self = this,
			newPlayers = [];
		this._players.forEach(function(elm,i){
			if( elm !== self ) {
				newPlayers.push(elm);
			}
		});
		this._players = newPlayers;
		this.player.unload();
		this.element.remove();
	}
});