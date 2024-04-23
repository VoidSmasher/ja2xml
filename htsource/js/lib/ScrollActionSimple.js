'use strict';

var $ = require('jquery'),
    MinimalClass = require('./MinimalClass');

module.exports = MinimalClass.extend({
    __className: 'ScrollActionSimple',
    create: function(){
        var self = this;

        this.onScrollFunctions = [];
        
        window.addSimpleScrollAction = function(elm,callback,trigger_prc){
            self.addElement(elm,callback,trigger_prc);
        };

        $(window).scroll(function(){
            self.scroll();
        });

        $('.js-scroll-action').each(function(elm,i){
            this.addElement(elm);
        });
    },
    addElement: function(elm,callback,trigger_prc){
        if( typeof callback !== 'function' ){ callback = false; }
        if( typeof trigger_prc === 'undefined' ){ trigger_prc = .1; }

        obj = $(elm);
        elm = obj[0];

        var obj = $(elm),
            activated = false,
            onScroll = function() {
                if(activated) return 0;

                var rect = elm.getBoundingClientRect();
                var prc = 0, total = window.innerHeight + rect.height;

                if( rect.top > window.innerHeight ) prc = 0;
                else if( rect.top < -rect.height ) prc = 1;
                else prc = 1 - ( ( rect.top + rect.height ) / total );

                if(prc > trigger_prc) {
                    if( callback ) {
                        callback();
                    }else{
                        obj.addClass('atscroll');
                    }
                    activated = true;
                }
            };

        this.onScrollFunctions.push(onScroll);
    },
    scroll: function(){
        this.onScrollFunctions.forEach(function(func,i){
            func();
        });
    }
});