'use strict';

var $ = require('jquery'),
	MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
	create: function()
	{
		var self = this;
		this.active = false;
		this.orientation = 'right';
		this.element.addClass(this.orientation);

		window.floating_arrow = this;
	},
	show: function(e)
	{
		this.active = true;
		this.element.addClass('active');
		this.set_position(e);
	},
	hide: function()
	{
		this.element.removeClass('active');
		this.set_position(0,0);
		this.active = false;
	},
	set_orientation: function(orientation)
	{
		if( this.orientation === orientation ) return;

		this.element.removeClass('top left right bottom');

		switch(orientation){
			case 'top':
			case 'left':
			case 'right':
			case 'bottom':
				this.orientation = orientation;
				this.element.addClass(orientation);
				break;
		}
	},
	set_position: function(x,y)
	{
		this.element.css({ left: x, top: y - window.app.scrollTop });
	}
});