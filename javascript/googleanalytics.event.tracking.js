;(function($) {
	/**
	 * A simple helper function for wrapping Google Analytic event tracking 
	 * into sites through jQuery and HTML5 data attributes.
	 *
	 * See the provided documentation folder for instructions and examples
	 * on how to add event tracking.
	 */	
	$.fn.analyticsTrackEvent = function(args) {
		_gaq = _gaq || [];
	
		/**
		 * Evaluates an argument. If a non terminal (i.e a function) return
		 * the result of that function. Otherwise returns the terminal value.
		 */
		var e = function(f) { 
			if($.isFunction(f)) return f(self); return f; 
		};
		
		var category, action, label, value, self = $(this[0]);
		
		var args = $.extend({
			category: function(ele) { 
				category = $(ele).data('category'); 
				if(!category) category = window.location.toString();

				return category;
			},
			action: function(ele) { 
				action = $(ele).data('action');
				if(!action) action = 'click';

				return action;
			},
			label: function(ele) { 
				// optional but recommened field
				label = $(ele).data('label'); 
				if(!label) label = $(ele).text();

				return label;
			},
			value: function(ele) { 
				return $(ele).data('value'); 
			},
		}, args);


		var track = ['_trackEvent', e(args.category), e(args.action)]; 
		
		label = e(args.label);
		value = parseInt(e(args.value));

		if(label) track.push(label);
		if(value) track.push(value);

		try {
			_gaq.push(track);
		} catch(err) {
			if(window.console != undefined) {
				window.console.log(err);
			}
		}
	}
})(jQuery);