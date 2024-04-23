'use strict';

var $ = require('jquery'),
	MinimalClass = require('./MinimalClass'),
	ForceField = require('./ForceField'),
	ForceSelect = require('./ForceSelect'),
	ForceRadio = require('./ForceRadio'),
	ForceCheckbox = require('./ForceCheckbox'),
	ForceMultipleCheckboxes = require('./ForceMultipleCheckboxes');

module.exports = MinimalClass.extend({
	__className: 'ForceForm',
	_enableComagic: true,
	create: function() {
		var self = this;

		this.submit_button = false;
		this.mode = this.element.attr('data-mode');

		if( this.element[0].tagName === 'FORM' ) {
			this.form = this.element;
		}else{
			this.form = this.element.find('form');
		}

		this.fields = [];
		this.is_ajax = true;
		this.ajax_busy = false;

		this.is_valid = false;
		this.valid_fields = [];
		this.invalid_fields = [];
		this._validation_skip_fields = [];

		this.form.submit(function (e) {
			if( self.is_ajax ) {
				e.preventDefault();
				e.stopPropagation();
			}
			self.submit(e);
		});

		this._create();

		this.setup_fields();

		this.validate('init');
		this.field_validity_changed();
	},
	_create: function(){},
	setup_fields: function(){
		var self = this;

		this.element.find('.js-select').each(function(i,elm){
			var select = new ForceSelect({ element: elm, delegate : self });
			if( select.name ) { self.fields[select.name] = select; }
		});

		this.element.find('.js-radio').each(function(i,elm){
			var radio = new ForceRadio({ element: elm, delegate : self });
			if( radio.name ) { self.fields[radio.name] = radio; }
		});

		this.element.find('.js-checkbox').each(function(i,elm){
			var checkbox = new ForceCheckbox({ element: elm, delegate : self });
			if( checkbox.name ) { self.fields[checkbox.name] = checkbox; }
		});

		this.element.find('.js-multiple-checkboxes').each(function(i,elm){
			var checkbox = new ForceMultipleCheckboxes({ element: elm, delegate : self });
			if( checkbox.name ) { self.fields[checkbox.name] = checkbox; }
		});

		this.element.find('input, textarea').each(function (i, inp) {
			if (inp.type === 'checkbox' || inp.type === 'radio' || inp.type === 'submit' || !inp.name.length || parseInt(inp.getAttribute('data-evt')||0)) {
				return;
			}
			self.fields[inp.name] = new ForceField({ element: inp, delegate : self });
		});
	},
	get_url: function(){ return this.url || this.form.attr('action'); },
	get_method: function(){ return this.method || this.form.attr('method'); },
	gather_data: function(){
		var data = {},
			form = this.form[0];

		for(var k in this.fields) {
			data[this.fields[k].name] = this.fields[k].value;
		}

		if(typeof random_value !== 'undefined') {
			data.random_value = random_value;
		}

		if( typeof form['agree'] !== 'undefined' ) {
			data.agree = form['agree'].checked ? 1 : 0;
		}

		return this._gather_data(data);
	},
	_gather_data: function(data){
		return data;
	},
	validation_skip_fields: function(fields) {
		this._validation_skip_fields = fields ? fields : [];
	},
	field_validity_changed: function(field){
		this.validate('revalidate');

		this.form.attr('data-valid', this.is_valid ? 1 : 0);
		this.is_valid ? this.form.removeClass('invalid-form') : this.form.addClass('invalid-form');
	},
	count_valid_fields: function(fields,target) {
		var left = fields.length;
		for(var k in fields) {
			if( this.in_array(fields[k],this.valid_fields) ) {
				left--;
			}
		}

		target.html(left);
	},
	validate: function(type){
		if( typeof type === 'undefined' ) {
			type = 'check';
		}
		var total = 0, valid = 0, is_valid, valid_field;
		this.invalid_fields = [];
		this.valid_fields = [];
		for(var k in this.fields) {
			if( this.in_array(k,this._validation_skip_fields) ) {
				continue;
			}

			valid_field = this.fields[k].validate(type);
			if( valid_field ){
				valid++;
				this.valid_fields.push(k);
			}else{
				this.invalid_fields.push(k);
			}

			total++;
		}

		is_valid = valid >= total;
		this.is_valid = this._validate(is_valid,type);
		return this.is_valid;
	},
	_validate: function(is_valid,type) {
		return is_valid;
	},
	reset: function() {
		for(var k in this.fields) {
			if( typeof this.fields[k].reset !== 'function' ) {
				this.log_error('Field ' + k + ' has no reset() function.');
				continue;
			}
			this.fields[k].reset();
		}
		this._reset();
	},
	_reset: function(){},
	submit: function (e) {
		if(!this.validate('check')) {
			this.log('Invalid data');
			return false;
		}

		return this.ajax();
	},
	ajax: function(cb){
		if( this.ajax_busy || ( this.submit_button && this.submit_button.busy ) ) { return false; }

		var data = this.gather_data();

		if(!data) { return false; }

		this.ajax_busy = true;

		$.ajax({
			url : this.get_url(),
			type : this.get_method(),
			dataType : 'json',
			data : data,
			context : this,
			beforeSend: function(){
				this.on_send(data);
			}
		})
			.done(function(resp){
				if( resp.error ) {
					this.on_error(resp);
					return;
				}

				this.on_success(resp);

				var comagic_data = {};

				if( data.name ) { comagic_data.name = data.name; }
				if( data.email ) { comagic_data.email = data.email; }
				if( data.phone ) { comagic_data.phone = data.phone; }
				if( data.message ) { comagic_data.message = data.message; }

				if( this._enableComagic ) {
					try {
						if( typeof window.Comagic !== 'undefined' && typeof window.Comagic.addOfflineRequest === 'function' ) {
							this.log('Comagic: ', comagic_data);
							window.Comagic.addOfflineRequest(comagic_data);
						}
					}catch(e) {
						this.log(e);
					}
				}

				if( typeof cb === 'function' ) {
					cb(resp,this);
				}
			})
			.fail(function(){
				this.on_fail();
			})
			.always(function(){
				this.ajax_busy = false;
				this.on_complete();
			});

		return true;
	},
	on_send: function(data){},
	on_error: function(resp) { alert(resp.message); },
	on_fail: function(){ this.log(this.fail_message); },
	on_success: function(data){},
	on_complete: function(){}
});