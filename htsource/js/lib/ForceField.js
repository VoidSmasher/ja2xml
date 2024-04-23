'use strict';

var $ = require('jquery'),
	ForceValidable = require('./ForceValidable');

module.exports = ForceValidable.extend({
	__className: 'ForceField',
	create: function(){
		var self = this;

		this.inp = this.element[0];
		this.name = this.inp.name;
		this.value = this.element.val();
		this.label = this.element.parent();
		this.is_phone = ( this.inp.name === 'phone' || this.inp.type === 'tel' );
		this.focused = false;
		this.is_valid = false;
		this.css_target = this.label;
		
		this.placeholder = this.label.find('.placeholder');
		this.placeholder.click(function(e){ self.element.trigger('focus'); });

		this.validator = this.get_validator(this.inp);

		if( this.inp.value.length > 0 ) {
			this.label.addClass('focused');
			this.label.addClass('full');
		}

		this.element.bind('change keyup keydown focus blur', function(e){ self.handle(e); });
		this.element.attr('data-evt', 1);

		this.autofocused = true;
		this.autofocus_interval = false;
		if( this.inp.name == 'login' || this.inp.type == 'password' ) {
			this.autofocused = false;
			this.autofocus_interval = setInterval(function(){
				if( self.inp.value.length > 0 ) {
					self.handle({ type : 'focus', keyCode : 0, shiftKey : false, ctrlKey : false });
					self.autofocused = true;
					clearInterval(self.autofocus_interval);
					self.autofocus_interval = null;
				}
			},1000);
		}
	},
	reset: function(){
		if(this.inp.type === 'hidden'){ return; }

		this.value = this.inp.value = '';
		this.label.removeClass('full success');
		this.focus(false);
		this.validate('init');
	},
	focus: function(dir) {
		if (dir !== this.focused) {
			( this.focused = dir ) ? this.label.addClass('focused') : this.label.removeClass('focused');
		}
	},
	handle: function(event){
		var type = event.type,
			should_focus = ( this.inp === document.activeElement ) || ( type === 'focus' ) || ( this.inp.value.length > 0 );

		if (type === 'blur' && this.is_phone && ( this.inp.value.length < 2 ) ) {
			this.label.removeClass('focus');
			return this.reset();
		}
		if(type === 'focus'){
			this.label.addClass('focus');
		}
		if(type === 'change' || type === 'keydown' || type === 'keyup'){
			if(this.inp.value.length > 0){
				this.label.addClass('full');
			}
			else{
				this.label.removeClass('full');
			}
		}
		if(type === 'blur'){
			this.label.removeClass('focus');
			if(this.inp.value.length < 1){
				this.label.removeClass('full');
			}
		}
		this.validate(type,event);

		var is_empty = !this.inp.value.length;

		if(!this.focused && type !== 'focus' && is_empty) {
			should_focus = false;
		}

		this.focus(should_focus);

		if(!this.is_valid && ( type === 'keydown' || is_empty ) ) {
			this.label.removeClass('has-error');
		}

		this.value = this.inp.value;
	}
});