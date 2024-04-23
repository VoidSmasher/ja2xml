'use strict';

var lib = require('../index.js'),
    $ = require('jquery'),
	SliderDots = require('./Dots'),
	SliderItem = require('./Item'),
    MinimalClass = require('../MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'libSlider',
	pre: function(opt) {
		this.tid = false;
		this.autoplay = false;

		this.autoload = true;
		this.load_on_demand = false;
		this.load_in_sequence = false;

		this.item_setup_mode = false;
		this.resize_mode = false;
		this.switch_mode = false;
	},
	create: function() {
		var self = this;

		this.is_loading = false;
		this.is_loaded = false;
		this.loaded_items = 0;

		this.stage_rect = false;

		this.is_switching = true;
		this.is_user_interacted = false;

		this.autoplay_timer = false;

		this.item = [];
		this.pos = 0;

		if(!this.tid) {
			this.tid = this.element.attr('tid') || '';
		}

		if(!this.item_setup_mode) {
			this.item_setup_mode = this.element.data('setup-mode') || 'background.image';
		}

		if(!this.resize_mode) {
			this.resize_mode = this.element.data('resize-mode') || false;
		}

		if(!this.switch_mode) {
			this.switch_mode = this.element.data('switch-mode') || false;
		}

		this.stage = this.element.find('.stage');
		if(!this.stage.length) return;

		this.with_floating_arrow = parseInt(this.element.data('with-floating-arrow'));
		this.clickable_slider = parseInt(this.element.data('clickable') || 0);
		this.clickable_stage = parseInt(this.stage.data('clickable') || 0);

		this.stage_rect = this.stage[0].getBoundingClientRect();
		this.pos_view = this.element.find('.js-pos');

		this.arrl = this.element.find('.js-prev');
		this.arrr = this.element.find('.js-next');

		this.floating_arrow = this.element.find('.js-floating-arrow');

		this.create_items();
		this.create_dots();

		if(this.autoload) {
			if(this.load_on_demand) {
				this.item[this.pos].load(function(item){
					self.prepare_slider();
					self.is_switching = false;
				});
			}else{
				this.load();
			}
		}

		window.app.add_resize(this);
	},
	resize: function(ww,wh){
		this.stage_rect = this.stage[0].getBoundingClientRect();
	},
	create_dots: function(){
		var self = this;
		this.element.find('.js-dots').each(function(i,elm){
			new SliderDots({ element: elm, delegate: self });
		});
	},
	create_items: function(){
		var self = this,
			count = 0;

		this.element.bind('item_loaded.slider',function(e,item,cb){ self.on_item_loaded(item,cb); });

		this.stage.find('.slide').each(function(i,elm){
			var item = self.create_item({
				id: i,
				element: elm,
				delegate: self,
				setup_mode: self.item_setup_mode
			});

			if(item.active){
				self.pos = item.id;
			}

			self.item.push( item );
			count++;
		});

		return count;
	},
	create_item: function(data){
		return new SliderItem(data);
	},
	remove_items_with_errors: function()
	{
		var good = [], errors = 0, id = 0;
		for(var i=0;i<this.item.length;i++) {
			if(!this.item[i].is_error) {
				this.item[i].id = id++;
				good.push( this.item[i] );
			}else{
				this.item[i].remove();
				errors++;
			}
		}

		if(errors){
			this.item = good;
		}
	},
	set_loading: function(dir)
	{
		if(this.is_loading == dir) return;
		dir ? this.element.addClass('loading') : this.element.removeClass('loading');
		this.is_loading = dir;
	},
	load: function(cb)
	{
		if(this.is_loading) return false;
		if(this.is_loaded) return ( typeof cb === 'function' ? cb() : false );

		var self = this;
		if(this.load_in_sequence){
			this.item[0].load(cb);
		}else{
			$(this.item).each(function(i,item){ item.load(cb); });
		}

		return true;
	},
	on_item_loaded: function(item,cb)
	{
		var next = item.id + 1;

		this.loaded_items++;
		if(this.load_on_demand) {
			this.set_loading(false);
			( typeof cb === 'function' ) && cb();
		}else{
			if(this.loaded_items >= this.item.length){
				this.everything_loaded(cb);
			}else{
				if( this.load_in_sequence && ( next < this.item.length - 1 )){
					this.item[next].load(cb);
				}
			}
		}
	},
	everything_loaded: function(cb)
	{
		this.loading = false;
		this.loaded = true;

		this.set_loading(false);
		this.is_switching = false;
		this.loaded = true;

		this.remove_items_with_errors();
		this.prepare_slider();

		this.element.addClass('loaded');

		this.start_autoplay();

		( typeof cb === 'function' ) && cb();
	},
	prepare_slider: function()
	{
		this.item[this.pos].activate(true);
		this.setup_events();
	},
	setup_events: function()
	{
		var self = this;

		if(this.arrl){
			this.arrl.bind('click',function(e){
				self.set_user_interacted();
				self.prev();

				( typeof self.onClick === 'function' ) && self.onClick(self);
			});
		}

		if(this.arrr){
			this.arrr.bind('click',function(e){
				self.set_user_interacted();
				self.next();

				( typeof self.onClick === 'function' ) && self.onClick(self);
			});
		}

		if(this.stage && this.clickable_stage && this.item.length > 1){
			this.stage.bind('click',function(e){
				self.set_user_interacted();

				var rect = self.stage[0].getBoundingClientRect();
				( e.pageX > rect.left + rect.width / 2 ) ? self.next() : self.prev();

				( typeof self.onClick === 'function' ) && self.onClick(self);
			});
		}

		if( this.clickable_slider && this.item.length > 1){
			this.element.bind('click',function(e){
				self.set_user_interacted();

				var rect = self.element[0].getBoundingClientRect();
				( e.pageX > rect.left + rect.width / 2 ) ? self.next() : self.prev();

				( typeof self.onClick === 'function' ) && self.onClick(self);
			});
		}

		if( this.with_floating_arrow && typeof window.floating_arrow != 'undefined' ) {
			this.element.css({ cursor: 'none' }).bind('mouseenter mousemove mouseleave',function(e){ self.mouse_floating_arrow(e); });
		}

		this.setup_touch_events();
	},
	mouse_floating_arrow: function(e){
		switch(e.type){
			case 'mouseleave':
				window.floating_arrow.hide();
				break;
			case 'mouseenter':
				window.floating_arrow.show(e);
			case 'mousemove':
				var rect = this.element[0].getBoundingClientRect(),
					orientation = ( e.pageX > rect.left + rect.width / 2 ) ? 'right' : 'left';
				window.floating_arrow.set_orientation(orientation);
				window.floating_arrow.set_position(e.pageX,e.pageY);
				break;
		}
	},
	prev: function(cb,quick)
	{
		var pos = this.pos - 1;
		if(pos < 0) pos = this.item.length - 1;
		this.switch_item(pos,false,cb,quick);
	},
	next: function(cb,quick)
	{
		var pos = this.pos + 1;
		if(pos >= this.item.length) pos = 0;
		this.switch_item(pos,true,cb,quick);
	},
	switch_item: function(pos,dir,cb,quick,force,not_user_initiated)
	{
		if(this.is_loading || ( !force && ( ( this.pos === pos ) || this.is_switching) ) ) return false;
		if(typeof dir === 'undefined'){ dir = pos > this.pos; }
		if(typeof quick === 'undefined'){ quick = false; }
		this.is_switching = true;

		var self = this,
			prev_pos = this.pos,
			cur = this.item[prev_pos],
			nxt = this.item[pos],
			dirExp = ( dir ? 'Next' : 'Prev' ),
			aEvt = this.animationEndEventName(),
			tEvt = this.transitionEndEventName(),
			waitingFor = 2,
			completes = 0,
			onComplete = function() {
				if(!quick) {
					cur.element.removeClass(['navOut'+dirExp,'flyOut'+dirExp].join(' '));
					nxt.element.removeClass(['navIn'+dirExp,'flyIn'+dirExp,'fly'+dirExp].join(' '));
				}

				nxt.activate(true);

				self.pos = pos;
				self.update_pos_viewer(pos);

				self.is_switching = false;

				self.start_autoplay();

				if( typeof self.onSwitch === 'function' ) {
				    self.onSwitch(self, not_user_initiated);
				}

				self.trigger(self.element,'items_switched',[self,pos,nxt,prev_pos,cur]);
			},
			onTransitionComplete = function(e) {
				if($(this).hasClass('active')){ cur.activate(false); }
				this.removeEventListener(tEvt,onTransitionComplete,false);
				if(++completes >= waitingFor) onComplete();
			},
			onAnimationComplete = function(e) {
				if($(this).hasClass('active')){ cur.activate(false); }
				this.removeEventListener(aEvt,onAnimationComplete,false);
				if(++completes >= waitingFor) onComplete();
			};

		if( this.load_on_demand && !nxt.is_loaded ) {
			nxt.load(function(){
				self.switch_item(pos, dir, cb, quick, true);
			});
			return true;
		}

		self.trigger(self.element,'items_switching',[this,pos,nxt,prev_pos,cur]);

		if( ( typeof this.dots !== 'undefined' ) && ( typeof this.dots.activate === 'function' ) ) {
			this.dots.activate(pos);
		}

		// aEvt = false;

		if(!quick && aEvt) {
			cur.element[0].addEventListener(aEvt, onAnimationComplete, false);
			nxt.element[0].addEventListener(aEvt, onAnimationComplete, false);
			cur.element.addClass('navOut' + dirExp);
			nxt.element.addClass('navIn' + dirExp)
		}else if(!quick && tEvt){
			cur.element[0].addEventListener(tEvt, onTransitionComplete, false);
			nxt.element[0].addEventListener(tEvt, onTransitionComplete, false);
			nxt.element.addClass('fly' + dirExp);
			setTimeout(function(){
				cur.element.addClass('flyOut' + dirExp );
				nxt.element.addClass('flyIn' + dirExp );
			},10);
		}else{
			quick = true;
			onComplete();
		}

		return true;
	},
	update_pos_viewer: function(pos){
		if(this.pos_view.length) {
			var string = ( pos + 1 ) + ' / ' + this.item.length;
			this.pos_view.html(string);
		}
	},
	setup_touch_events: function(){
		// var cur, prv, nxt, curpos, prvpos, nxtpos;
		// this.setTouchEvent({
		// 	touchSurface: this.stage,
		// 	onStart: function(options,touchEvent){
		// 		self.stop_autoplay();
		// 		self.stop_timer();
		//
		// 		curpos = self.pos;
		// 		prvpos = self.pos - 1;
		// 		nxtpos = self.pos + 1;
		//
		// 		if( prvpos < 0 ){ prvpos = self.item.length - 1; }
		// 		if( nxtpos > self.item.length - 1 ){ nxtpos = 0; }
		//
		// 		cur = self.item[curpos];
		// 		prv = self.item[prvpos];
		// 		nxt = self.item[nxtpos];
		// 	},
		// 	onMove: function(options,touchEvent){
		// 		var distance = options.distanceX,
		// 			negative_distance = -1 * options.distanceX;
		//
		// 		if( distance <= 0 ) {
		// 			prv.obj.style.transform = 'translateX(-100%)';
		// 			prv.image.style.transform = 'translateX(100%)';
		//
		// 			nxt.obj.style.transform = 'translateX(' + ( self.stage_rect.width + distance ) + 'px)';
		// 			nxt.image.style.transform = 'translateX(' + ( negative_distance - self.stage_rect.width ) + 'px)';
		// 		}
		//
		// 		if( distance >= 0 ) {
		// 			nxt.obj.style.transform = 'translateX(100%)';
		// 			nxt.image.style.transform = 'translateX(-100%)';
		//
		// 			prv.obj.style.transform = 'translateX(' + ( distance - self.stage_rect.width ) + 'px)';
		// 			prv.image.style.transform = 'translateX(' + ( negative_distance + self.stage_rect.width ) + 'px)';
		// 		}
		//
		// 		cur.obj.style.transform = 'translateX(' + ( distance ) + 'px)';
		// 		cur.image.style.transform = 'translateX(' + ( negative_distance ) + 'px)';
		// 	},
		// 	onEnd: function(options,touchEvent){
		// 		var distance = options.distanceX,
		// 			negative_distance = -1 * options.distanceX;
		//
		// 		if( Math.abs(distance) > ( self.stage_rect.width / 2 ) ) {
		// 			var dir;
		// 			if( distance < 0 ) {
		// 				// next
		// 				dir = true;
		// 				nxt.obj.style.transition = 'transform .2s ease';
		// 				nxt.image.style.transition = 'transform .2s ease';
		//
		// 				nxt.obj.style.transform = 'translateX(0)';
		// 				nxt.image.style.transform = 'translateX(0)';
		// 			}else{
		// 				// prev
		// 				dir = false;
		// 				prv.obj.style.transition = 'transform .2s ease';
		// 				prv.image.style.transition = 'transform .2s ease';
		//
		// 				prv.obj.style.transform = 'translateX(0)';
		// 				prv.image.style.transform = 'translateX(0)';
		// 			}
		//
		// 			cur.obj.style.transition = 'transform .2s ease';
		// 			cur.image.style.transition = 'transform .2s ease';
		//
		// 			cur.obj.style.transform = 'translateX(' + ( dir ? -100 : 100 ) + '%)';
		// 			cur.image.style.transform = 'translateX(' + ( dir ? 100 : -100 ) + '%)';
		//
		// 			self.pos = dir ? nxtpos : prvpos;
		//
		// 			setTimeout(function(){
		// 				nxt.obj.style.transition = '';
		// 				nxt.image.style.transition = '';
		// 				nxt.obj.style.transform = '';
		// 				nxt.image.style.transform = '';
		// 				prv.obj.style.transition = '';
		// 				prv.image.style.transition = '';
		// 				prv.obj.style.transform = '';
		// 				prv.image.style.transform = '';
		// 				cur.obj.style.transition = '';
		// 				cur.image.style.transition = '';
		// 				cur.obj.style.transform = '';
		// 				cur.image.style.transform = '';
		//
		// 				if( dir ) {
		// 					nxt.obj.className = 'item fxRollX active';
		// 					prv.obj.className = 'item fxRollX';
		// 				}else{
		// 					prv.obj.className = 'item fxRollX active';
		// 					nxt.obj.className = 'item fxRollX';
		// 				}
		//
		// 				cur.obj.className = 'item fxRollX';
		//
		// 			},200);
		//
		// 		}else{
		// 			prv.obj.style.transform = '';
		// 			prv.image.style.transform = '';
		// 			nxt.obj.style.transform = '';
		// 			nxt.image.style.transform = '';
		// 			cur.obj.style.transform = '';
		// 			cur.image.style.transform = '';
		// 		}
		// 	}
		// });
	},
	start_autoplay: function()
	{
		if( !this.autoplay ) return;
		this.stop_autoplay();

		var self = this;
		this.autoplay_timer = setTimeout(function(){ self.next(); }, this.autoplay);
	},
	stop_autoplay: function(stop_forever)
	{
		if( this.autoplay_timer ) {
			clearTimeout( this.autoplay_timer );
			this.autoplay_timer = false;
		}

		if( stop_forever ){
			this.autoplay = false;
		}
	},
	start_timer: function(){},
	stop_timer: function(){},
	set_user_interacted: function(){
		this.is_user_interacted = true;
		this.stop_autoplay(true);
		this.stop_timer();
	}
});