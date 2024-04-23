'use strict';

var $ = require('jquery'),
    Class = require('class.extend');

module.exports = Class.extend({
    __className: 'MinimalClass',
    __nativeMode: false,
    init: function (opt) {
        this.delegate = false;
        this.element = false;
        this.opt = {};
        this.pre(opt);
        this.setOptions(opt);

        if( this.element.length )
        	$(this.element).attr('classname', this.__className);

        this.create();
    },
    create: function () {},
    pre: function (opt) {},
    log: function () {
        if (typeof window.console !== 'undefined' && typeof window.console.log === 'function') {
            window.console.log.apply(window.console,arguments);
        } else {
            alert(arguments.join("\n"));
        }
    },
	log_buffer: [],
	log_log_buffer: function(){
		for(var i=0;i<this.log_buffer.length;i++) {
			window.console.log.apply(this, this.log_buffer[i]);
		}
		this.log_buffer = [];
		return 'End of log buffer.';
	},
    setOptions: function (opt) {
        if (typeof opt === 'undefined') {
            return;
        }
        for (var key in opt) {
            this.setOption(key, opt[key]);
        }
    },
    setOption: function (key, val) {
	    if (key === 'element') {
		    this.element = this.__nativeMode ? val : $(val);
		    return;
	    } else if (key === 'delegate') {
		    this.delegate = val;
		    return;
	    } else if (key.substr(0, 1) === '_') {
		    key = key.substr(1);
		    this.opt[key] = val;
		    return;
	    }

	    this[key] = val;
    },
    mouseWheelLocked: false,
    onMouseWheelLock: function(e){
        e.preventDefault();
    },
    toggleMouseWheelLock: function(dir){
        if( dir !== this.mouseWheelLocked ) {
            dir ? $(document).bind('mousewheel', this.onMouseWheelLock) : $(document).unbind('mousewheel', this.onMouseWheelLock);
            this.mouseWheelLocked = dir;
        }
        return this.mouseWheelLocked;
    },
    in_array: function (needle, haystack) {
        var length = haystack.length;
        for (var i = 0; i < length; i++) {
            if (haystack[i] == needle) {
                return true;
            }
        }
        return false;
    },
    is_touch_device: function(){
      return 'ontouchstart' in window        // works on most browsers 
          || window.navigator.maxTouchPoints;       // works on IE10/11 and Surface
    },
    setCaretPosition: function(elem, caretPos) {
        if(elem != null) {
            if(elem.createTextRange) {
                var range = elem.createTextRange();
                range.move('character', caretPos);
                range.select();
            }
            else {
                if(elem.selectionStart) {
                    elem.focus();
                    elem.setSelectionRange(caretPos, caretPos);
                }
                else
                    elem.focus();
            }
        }
    },
    transitionEndEventName: function () {
      var i,
        undefined,
        el = document.createElement('div'),
        eventNames = {
          'transition':'transitionend',
          'OTransition':'otransitionend',
          'MozTransition':'transitionend',
          'WebkitTransition':'webkitTransitionEnd',
          'msTransition' : 'MSTransitionEnd'
        };

      for (i in eventNames) {
        if (eventNames.hasOwnProperty(i) && el.style[i] !== undefined) {
          return eventNames[i];
        }
      }
    },
    animationEndEventName: function  () {
      var i,
        undefined,
        el = document.createElement('div'),
        eventNames = {
          'animation':'animationend',
          'OAnimation':'oAnimationEnd',
          'WebkitAnimation':'webkitAnimationEnd',
          'MozAnimation':'mozAnimationRnd',
          'msAnimation':'MSAnimationEnd'
        };

      for (i in eventNames) {
        if (eventNames.hasOwnProperty(i) && el.style[i] !== undefined) {
          return eventNames[i];
        }
      }
    },
    xlink: function( xlink, className ) {
      var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      svg.innerHTML = '<use xlink:href="#' + xlink + '"></use>';
      if(className) svg.setAttribute('class',className);
      return svg;
    },
    calculate_animated_value: function(current,target,speed){
        var dir = target > current,
            diff = dir ? target - current : current - target,
            step = ( dir ? 1 : -1 ) * Math.max(0.001, diff / speed ),
            value = dir ? Math.min(target,current + step) : Math.max(target,current + step);
        return value;
    },
    lz: function(obj){
        var lz = document.getElementById('LZ');
        if( !lz ) {
            lz = document.createElement('DIV');
            lz.id = 'LZ';lz.className = 'LZ';
            document.body.appendChild(lz);
        }
        lz.appendChild(obj);
    },
    preload_image: function(url,cb) {
        var image = new Image();
        image.src = url;
        image.onload = function(){ if( typeof cb === 'function' ){ cb(image); } };
        this.lz(image);
    },
    fullscreen: function(needState) {
        var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
            (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
            (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
            (document.msFullscreenElement && document.msFullscreenElement !== null);

        var docElm = document.documentElement;

        isInFullScreen = isInFullScreen || false;

        if(isInFullScreen == needState) {
            return false;
        }

        if (!isInFullScreen) {
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            } else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            } else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            } else if (docElm.msRequestFullscreen) {
                docElm.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }

        return true;
    },
    calc_element_scroll_prc: function(element)
    {
        if( typeof element === 'undefined' || typeof element.getBoundingClientRect !== 'function' ) return 0;

        var rect = element.getBoundingClientRect();
        var prc = 0, total = window.app.wh + rect.height;

        if( rect.top > window.app.wh ) prc = 0;
        else if( rect.top < -rect.height ) prc = 1;
        else prc = 1 - ( ( rect.top + rect.height ) / total );

        return prc;
    },
    animate: function(speed, easing, callback) {
        var startTime = Date.now(),
            ease = bezierEasing(easing[0], easing[1], easing[2], easing[3]),
            raf;

        (function loop() {
            var now = Date.now(),
                diff = now - startTime,
                percent = diff / speed;

            if (percent > 1) {
                callback(1);
                cancelAnimationFrame(raf);
            } else {
                callback(ease(percent));
                raf = requestAnimationFrame(loop.bind(this));
            }
        })();
    },
    measure_distance: function(a,b){
        var distance;
        if( ( a >= 0 && b >= 0 ) || ( a <= 0 && b <= 0 ) ) {
            distance = ( a < b ) ? a - b : b - a;
        }else{
            distance = ( Math.abs(b) + Math.abs(a) ) * ( b < 0 ? -1 : 0 );
        }
        return distance;
    },
	setTouchEvent: function(touchOptions) {
		var touchSurface = touchOptions.touchSurface;

		$(touchSurface).bind('touchstart', function(e){
			var touchEvent = e.originalEvent;

			touchOptions.distanceX = 0;
			touchOptions.distanceY = 0;

			touchOptions.locked = false;

			if (touchEvent.touches.length != 1) return false;

			if(touchOptions.prevent || touchOptions.preventStart || Math.abs(touchOptions.movedX) < Math.abs(touchOptions.movedY)) {
				touchEvent.preventDefault();
				touchEvent.stopPropagation();
			}

			var windowWidthHalf = Math.round( $(window).width() / 2 );

			var currentTouchPosition, startTouchPosition,
				startTime = (new Date()).getTime();

			currentTouchPosition = startTouchPosition = { top: touchEvent.touches[0].clientY, left: touchEvent.touches[0].clientX };

			if( typeof touchOptions.onStart == 'function' ) {
				touchOptions.onStart(touchOptions, touchEvent);
			}

			$(touchSurface).bind('touchmove',function(e){
				touchEvent = e.originalEvent;
				if (touchEvent.touches.length != 1) return false;

				if(touchOptions.prevent || touchOptions.preventMove || Math.abs(touchOptions.movedX) < Math.abs(touchOptions.movedY)) {
					touchEvent.preventDefault();
					touchEvent.stopPropagation();
				}

				currentTouchPosition = { top: touchEvent.touches[0].clientY, left: touchEvent.touches[0].clientX };

				touchOptions.prevDistanceX = touchOptions.distanceX;
				touchOptions.prevDistanceY = touchOptions.distanceY;
				touchOptions.distanceX = currentTouchPosition.left - startTouchPosition.left;
				touchOptions.distanceY = currentTouchPosition.top - startTouchPosition.top;
				touchOptions.movedX = touchOptions.distanceX - touchOptions.prevDistanceX;
				touchOptions.movedY = touchOptions.distanceY - touchOptions.prevDistanceY;

				if(touchOptions.onMove)
					touchOptions.onMove(touchOptions, touchEvent);
			});


			$(touchSurface).bind('touchend',function(e){
				touchEvent = e.originalEvent;

				if( touchOptions.prevent || touchOptions.preventEnd || Math.abs(touchOptions.movedX) < Math.abs(touchOptions.movedY)) {
					touchEvent.preventDefault();
					touchEvent.stopPropagation();
				}

				var newTime = (new Date()).getTime();
				if(touchOptions.onEnd)
				{
					touchOptions.dTime = newTime - startTime;
					touchOptions.prevDistanceX = touchOptions.distanceX || 0;
					touchOptions.prevDistanceY = touchOptions.distanceY || 0;
					touchOptions.distanceX = currentTouchPosition.left - startTouchPosition.left;
					touchOptions.distanceY = currentTouchPosition.top - startTouchPosition.top;
					touchOptions.movedX = touchOptions.distanceX - touchOptions.prevDistanceX;
					touchOptions.movedY = touchOptions.distanceY - touchOptions.prevDistanceY;

					touchOptions.maxDTime = touchOptions.maxDTime || 1200;
					touchOptions.minDistanceX = touchOptions.minDistanceX || 100;
					touchOptions.minDistanceY = touchOptions.minDistanceY || 100;

					touchOptions.moved = false;
					touchOptions.click = false;
					touchOptions.clickWH = false;

					if(touchOptions.dTime<touchOptions.maxDTime){
						if(touchOptions.distanceX<-touchOptions.minDistanceX){
							touchOptions.moved = 'left';
						}else if(touchOptions.distanceX>touchOptions.minDistanceX){
							touchOptions.moved = 'right';
						}else if(touchOptions.distanceY<-touchOptions.minDistanceY){
							touchOptions.moved = 'up';
						}else if(touchOptions.distanceY>touchOptions.minDistanceY){
							touchOptions.moved = 'down';
						}else if(Math.abs(touchOptions.distanceY)<touchOptions.minDistanceY && Math.abs(touchOptions.distanceX)<touchOptions.minDistanceX){
							touchOptions.clickWH = (startTouchPosition.left > windowWidthHalf ) ? 1 : -1;
						}
					}

					if( touchOptions.preventEndIfMoved && touchOptions.moved ) {
						touchEvent.preventDefault();
						touchEvent.stopPropagation();
					}

					touchOptions.onEnd(touchOptions, touchEvent);
				}

				$(touchSurface).unbind('touchmove');
				$(touchSurface).unbind('touchend');

				touchOptions.distanceX = 0;
				touchOptions.distanceY = 0;
			});

		});
	},
	trigger: function(elm,evt,data){
    	elm.trigger(evt,data);
	}
});