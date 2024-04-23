'use strict';

var $ = require('jquery'),
	ActiveElement = require('../util/ActiveElement');

module.exports = ActiveElement.extend({
	__className: 'SliderDot',
	pre: function(){
		this._super();
		this.event_namespace = 'dot';
	},
	create: function(){
		this._super();

	}
});