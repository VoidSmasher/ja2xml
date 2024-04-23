'use strict';

var $ = require('jquery'),
    MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'ForceSlider',
	pre: function(opt)
	{
		this.loaded = false;
		this.loading = false;
		this.loadedItems = 0;

		this.switching = true;
		this.pos = 0;
		this.item = [];

		this.autoplay = false;
		this.autoplayTimer = false;

		this.stageRect = false;

		this.autoload = true;
		this.loadOnDemand = false;
		this.loadInSequence = false;

		this.isUserInteracted = false;

		this.itemSetupMode = 'background';

		this.clickableStage = true;	
	},
	create: function()
	{
		var self = this;

		this.stage = this.element.find('.stage');
		if(!this.stage.length) return;

		this.stageRect = this.stage[0].getBoundingClientRect();
		this.pos_view = this.element.find('.js-pos');

		this.arrl = this.element.find('.js-prev');
		this.arrr = this.element.find('.js-next');

		this.stage.find('.item').each(function(i,elm){
			var obj = $(elm),
				item = {
					id : i,
					obj : elm,
					src : elm.attr('data-src'),
					active : elm.hasClass('active'),
					width : 0,
					height : 0,
					w : 0,
					h : 0,
					loaded : false,
					loading : false,
					error : false
				};

			if(item.active)
				self.pos = i; // needs to be corrected below if someone crashed by loading error when loadOnDemand = false

			self.item.push( item );
		});

		if(this.autoload) {
			if(this.loadOnDemand) {
			  this.loadItem(this.pos, function(item){
			    	self.prepareSlider();
			    	self.switching = false;
				});
			}else{
				this.load();
			}
		}

		if( typeof this._create === 'function' ) {
			this._create();
		}
	},
	startAutoplay: function()
	{
		if( !this.autoplay ) return;
		this.stopAutoplay();

		var self = this;
		this.autoplayTimer = setTimeout(function(){ self.next(); }, this.autoplay);
	},
	stopAutoplay: function(stopForever)
	{
	  	if( this.autoplayTimer ) {
	    	clearTimeout( this.autoplayTimer );
	    	this.autoplayTimer = false;
		}

		if( stopForever )
			this.autoplay = false;
	},
	setLoading: function(dir)
	{
		if(this.loading == dir) return;
	    ( this.loading = dir ) ? this.element.addClass('loading') : this.element.removeClass('loading');
	},
	load: function(cb)
	{
		if(this.loading) return false;
		if(this.loaded) return this.cb(cb);

		var self = this;
		if(this.loadInSequence)
			this.loadItem(0,cb);
		else
			this.item.forEach(function(el,i){ self.loadItem(i,cb); });

		return true;
	},
	loadItem: function(i,cb)
	{
		if(!item.src) {
			this.itemLoaded({ type: 'skip' },i,cb);
		}

		var self = this,
			item = this.item[i],
			lz = $("#LZ");

		if(!lz.length) {
			lz = $('<DIV>').attr('id',LZ).addClass('LZ').appendTo(document.body);
		}

		item.loading = true;
		item.img = $('<IMG>').appendTo(lz);
		item.img.bind('load',function(e){ self.itemLoaded(e,i,cb); });
		item.img.bind('error',function(e){ self.itemLoaded(e,i,cb); });
		item.img.src = item.src;
	},
	itemLoaded: function(e,i,cb)
	{
		var item = this.item[i];

		switch(e.type) {
			case 'skip':
				break;
			case 'load':
				item.width = item.img.width || 0;
				item.height = item.img.height || 0;
				this.setupItem(item);
				break;
			case 'error':
				item.error = true;
				break;
		}

		this.loadedItems++;
		item.loading = true;
		item.loaded = true;

		if(this.loadOnDemand) {
			this.setLoading(false);
			this.cb(item);
		}else{
			if(this.loadedItems >= this.item.length)
				this.everythigLoaded(cb);
			else
				if( ( i < this.item.length - 1 ) && this.loadInSequence)
					this.loadItem(i+1,cb);
		}
	},
	setupItem: function(item)
	{
		switch(this.itemSetupMode){
			case 'background.image':
				$('<DIV>').addClass('image').css({ backgroundImage : 'url(' + item.src + ')' }).appendTo( item.obj );
				break;
			case 'background':
				item.obj.css({ backgroundImage : 'url(' + item.src + ')' });
				break;
			default:
				break;
		}
	},
	everythigLoaded: function(cb)
	{
		this.loading = false;
		this.loaded = true;

		this.setLoading(false);
		this.switching = false;
		this.loaded = true;

		this.removeItemsWithErrors();
		this.prepareSlider();

		this.startAutoplay();

		this.cb(cb);
	},
	prepareSlider: function()
	{
		var item = this.item[this.pos];
		item.obj.addClass('active');
		item.active = true;
		this.setupEvents();
	},
	setupEvents: function()
	{
		var self = this;
		if(this.arrl) this.arrl.bind('click',function(e){
			self.userInteracted();
			self.prev();

			if( typeof self.onClick === 'function' ) {
				self.onClick(self);
			}
		});

		if(this.arrr) this.arrr.bind('click',function(e){
			self.userInteracted();
			self.next();

			if( typeof self.onClick === 'function' ) {
				self.onClick(self);
			}
		});

		if(this.stage && this.clickableStage && this.item.length > 1) this.stage.bind('click',function(e){
			self.userInteracted();
			var rect = self.stage[0].getBoundingClientRect();
			( e.pageX > rect.left + rect.width / 2 ) ? self.next() : self.prev();

			if( typeof self.onClick === 'function' ) {
				self.onClick(self);
			}
		});
	},
	startTimer: function(){},
	stopTimer: function(){},
	userInteracted: function(){
		this.isUserInteracted = true;
		this.stopAutoplay(true);
		this.stopTimer();
	},
	removeItemsWithErrors: function()
	{
		var good = [], errors = 0, id = 0;
		for(var i=0;i<this.item.length;i++) {
		  if(!this.item[i].error) {
				this.item[i].id = id++;
				good.push( this.item[i] );
			}else{
				this.stage.removeChild( this.item[i].obj );
				errors++;
			}
		}

		if(errors)
		  this.item = good;
	},
	open: function(cb)
	{

	},
	close: function(cb)
	{

	},
	prev: function(cb,quick)
	{
		var pos = this.pos - 1;
		if(pos < 0) pos = this.item.length - 1;
		this.switchSlide(pos,false,cb,quick);
	},
	next: function(cb,quick)
	{
		var pos = this.pos + 1;
		if(pos >= this.item.length) pos = 0;
		this.switchSlide(pos,true,cb,quick);
	},
	switchSlide: function(pos,dir,cb,quick,force,not_user_initiated)
	{
		if(this.loading || ( !force && ( ( this.pos == pos ) || this.switching) ) ) return false;
		if(typeof quick == 'undefined') quick = false;
		if(typeof dir === 'undefined'){ dir = pos > this.pos; }
		this.switching = true;

		var self = this,
			cur = this.item[this.pos],
			nxt = this.item[pos],
			dirExp = ( dir ? 'Next' : 'Prev' ),
			aEvt = this.animationEndEventName(),
			tEvt = this.transitionEndEventName(),
			waitingFor = 2,
			completes = 0,
			onComplete = function() {
				if(!quick) {
					cur.obj.removeClass(['navOut'+dirExp,'flyOut'+dirExp].join(' '));
					nxt.obj.removeClass(['navIn'+dirExp,'flyIn'+dirExp,'fly'+dirExp].join(' '));
				}

				cur.obj.removeClass('active');
				nxt.obj.addClass('active');
				cur.active = false;
				nxt.active = true;

				self.pos = pos;
				if(self.pos_view)
				    self.pos_view.innerHTML = ( pos + 1 ) + ' из ' + self.item.length;

				self.switching = false;

				self.startAutoplay();

				if( typeof self.onSwitch === 'function' ) {
				    self.onSwitch(self, not_user_initiated);
				}
			},
			onTransitionComplete = function(e) {
				this.removeEventListener(tEvt,onTransitionComplete,false);
				if(++completes >= waitingFor) onComplete();
			},
			onAnimationComplete = function(e) {
			  this.removeEventListener(aEvt,onAnimationComplete,false);
			  if(++completes >= waitingFor) onComplete();
			};


			if(this.loadOnDemand && !nxt.loaded) {
				this.loadItem(nxt.id, function(){
				self.switchSlide(pos,dir,cb,quick,true);
			});
			return true;
		}

		if( typeof this.dots !== 'undefined' && typeof this.dots.activate === 'function' ) {
			this.dots.activate(pos);
		}

		if(!quick && aEvt) {
			cur.obj.addEventListener(aEvt, onAnimationComplete, false);
			nxt.obj.addEventListener(aEvt, onAnimationComplete, false);
			cur.obj.addClass( 'navOut' + dirExp );
			nxt.obj.addClass( 'navIn' + dirExp );
		}else if(!quick && tEvt){
			cur.obj.addEventListener(tEvt, onTransitionComplete, false);
			nxt.obj.addEventListener(tEvt, onTransitionComplete, false);
			nxt.obj.addClass('fly' + dirExp );
			setTimeout(function(){
				cur.obj.addClass('flyOut' + dirExp );
				nxt.obj.addClass('flyIn' + dirExp );
			},30);
		}else{
			quick = true;
			onComplete();
		}

		return true;
	},
	cb : function(cb,data) {
	    if( typeof cb != 'function' ) return false;
	    if( typeof data == 'undefined' ) data = false;
	    return cb( data );
	}
});