'use strict';

var $ = require('jquery'),
	ForceValidable = require('./ForceValidable');

module.exports = ForceValidable.extend({
	__className: 'ForceSelect',
	create: function() {
		var self = this,
			input = this.element.find('input[type=hidden]');

		if(!input.length) {
			this.name = this.inp = this.input = false;
		}else{
			this.inp = input[0];
			this.input = $(this.inp);
			this.name = this.inp.name;
			this.validator = this.get_validator(this.inp);
			this.css_target = this.element;
			this.input.attr('data-evt',1);
		}

		this.selected = this.element.find('div.select-val');
		this.selected_span = this.selected.find('span');
		this.options = this.element.find('ul.select-list');

		this.value = this.selected.attr('data-value') || '';
		this.selected_item = false;
		this.mouseover = false;
		this.opened = false;

		if( this.int ) {
			this.value = parseInt(this.value);
		}

		this.items = [];

		this.options.find('li').each(function(i,elm){
			var obj = $(elm),
				item = {
					obj : $(elm),
					value : obj.attr('data-value') || '',
					text : obj.text(),
					active : false,
					activate: function(dir) {
						if( this.active === dir ) { return; }
						if(dir) {
							this.obj.css({ display : 'none' });
						}else{
							this.obj.css({ display : 'block' });
						}
						this.active = dir;
						return this;
					}
				};

			if( self.int ) {
				item.value = parseInt(item.value);
			}

			if( self.value === item.value ) {
				self.selected_item = item.activate(true);
			}

			self.items.push(item);

			item.obj.click(function(e){
				self.pick(i,true);
			});
		});

		this.element.bind('mouseenter mouseleave',function(e){
			self.mouseover = (e.type === 'mouseenter');
		});

		this.element.click(function(e){
			e.preventDefault();
			e.stopPropagation();
			self.toggle();
		});

		$(window).click(function(e){
			if( !self.mouseover && self.opened ) {
				self.toggle(false);
			}
		});
	},
	toggle: function(dir) {

		if( typeof dir === 'undefined' ) {
			dir = !this.opened;
		}else if(dir === this.opened){
			return;
		}

		var self = this;
		if (dir){
			$('.js-select.open').removeClass('open').find('ul').slideUp();
			this.element.addClass('open');
			this.options.slideDown();
		} else {
			this.options.slideUp();
			setTimeout(function(){ self.element.removeClass('open'); },300);
		}

		this.opened = dir;
	},
	pick: function(i,from_user){
		var item = this.items[i];

		if( item.value === this.value ) {
			return;
		}

		var previous_value = this.value;

		this.selected_span.empty().text( item.text );
		this.selected.attr('data-value', item.value);
		if(this.input){ this.input.val( item.value ); }
		this.value = item.value;

		this.value ? this.element.addClass('valued') : this.element.removeClass('valued');

		this.validate('change');

		if(this.selected_item) { this.selected_item.activate(false); }
		this.selected_item = item.activate(true);

		if( from_user && typeof this.onChange === 'function' ) {
			this.onChange(this,previous_value);
		}
	},
	set_value: function(value) {
		if( this.int ) { value = parseInt(value); }
		for(var i=0;i<this.items.length;i++) {
			if(this.items[i].value === value.toString()) {
				this.pick(i);
			}
		}
	}
});