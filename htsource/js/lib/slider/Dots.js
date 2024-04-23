'use strict';

var $ = require('jquery'),
	SliderDot = require('./Dot'),
	MinimalClass = require('../MinimalClass');

module.exports = MinimalClass.extend({
	__className: 'SliderDots',
	pre: function(opt)
	{
		this.timer_dot = false;
		this.timers_enabled = false;
	},
	create: function()
	{
		var self = this;
		this.item = [];

		this.cur = false;
		this.pos = -1;

		this.create_dots();

		if( this.delegate ) {
			this.delegate.element.bind('items_switching',function(event,slider,pos,prev_pos,item,prev_item){
				self.switch_to(pos);
			});

			this.element.bind('mouse_click.dot',function(event,dot,e,from_user){
				self.delegate.switch_item(dot.id);
			});
		}
	},
	create_dots: function(id,elm)
	{
		var self = this,
			onClick = function(e,dot,evt,from_user){
				alert(1);
				self.switch_to(dot.id,null,true);
			};

		this.element.find('.dot').each(function(id,elm){
			var dot = self.create_dot({
				id: id,
				element: elm,
				delegate: self,
				onClick: onClick
			});

			if( dot.active ) {
				self.cur = dot;
				self.pos = dot.id;
			}

			self.item.push( dot );
		});
	},
	create_dot: function(data)
	{
		return new SliderDot(data);
	},
	prev: function()
	{
		var pos = this.pos - 1;
		if( pos < 0 ) { pos = this.item.length - 1; }
		this.switch_to(pos);
	},
	next: function()
	{
		var pos = this.pos + 1;
		if( pos >= this.item.length) { pos = 0; }
		this.switch_to(pos);
	},
	switch_to: function(pos,opt,from_user)
	{
		var cur = this.cur,
			nxt = ( typeof this.item[pos] !== 'undefined' ) ? this.item[pos] : false;

		if( from_user )
		{
			if(!this.onChange(pos,opt,from_user))
			{
				return;
			}
		}

		if( cur && nxt && ( cur.id === nxt.id ) )
		{
			return;
		}

		if(cur)
		{
			cur.activate(false);
			this.pos = -1;
			this.cur = false;
		}

		if(nxt)
		{
			nxt.activate(true,{ start_timer: this.timers_enabled });
			this.pos = nxt.id;
			this.cur = nxt;
		}
	}
});