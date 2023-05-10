/*
 * jQuery Info Balloon 1.0
 * http://BARMAZ.ru/
 * Copyright (c) 2011 BARMAZ Group
 * Dual licensed under the MIT and GPL licenses.
 * 
 * Based on jQuery Hoverbox 1.0
 * http://koteako.com/hoverbox/
 * Copyright (c) 2009 Eugeniy Kalinin
 * Dual licensed under the MIT and GPL licenses.
 * http://koteako.com/hoverbox/license/ 
 */
jQuery.fn.infoballoon = function(options) {
    var settings = jQuery.extend({ id: 'balloon', follow: true, top: 0, left: 15 }, options);
    var handle;
    function balloon(event) {
        if (settings.follow) {	
            if ( ! handle) {
                // Create an empty div to hold the tooltip
                handle = $('<div style="position:absolute" id="'+settings.id+'"><\/div>').appendTo(document.body).hide();
            }
	        if (event) {
	            // Make the tooltip follow a cursor
	            handle.css({
	                top: (event.pageY - settings.top) + "px",
	                left: (event.pageX + settings.left) + "px"
	            });
	        }
        } else {
            if ( ! handle) {
                // Create an empty div to hold the tooltip
                handle = $('<div style="position:absolute" id="'+settings.id+'"><\/div>').appendTo(document.body).hide();
                handle.click(function(e) { balloon().toggle('fast'); });
            }
        	
        }
        return handle;
    }

    this.each(function() {
        if (settings.follow) {
	        $(this).hover(
	            function(e) {
	                if (this.title) {
	                    // Remove default browser tooltips
	                    this.t = this.title;
	                    this.title = '';
	                    this.alt = '';
	                    balloon(e).html(this.t).fadeIn('fast');
	                }
	            },
	            function() {
	                if (this.t) {
	                    this.title = this.t;
		                balloon().hide();
	                }
	            }
	        );
        } else {
        	if (this.title) {
        		this.t = this.title;
        		this.title = '';
        		this.alt = '';
        	}
        	$(this).click(
	            function(e) {
	            	if (this.t) {
	            		balloon().hide();
	                    balloon(e).html(this.t).fadeIn('slow');
                    	handle.css({ top: (e.pageY - settings.top) + "px", left: (e.pageX + settings.left) + "px" });
	                }
	            }
	        );
        }

        $(this).mousemove(balloon);
    });
};