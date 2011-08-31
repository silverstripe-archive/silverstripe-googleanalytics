# Google Analytics Module

## Features

### Event Tracking API

Included in the module is a simple jQuery plugin which wraps Google Analytics Event
based tracking and provides several shortcuts to including event tracking in your
project.

To enable event tracking, first you will need to enable the Google Logger then include
`GoogleLogger::set_event_tracking_enabled(true)` in your mysite/_config.php file:

	GoogleLogger::activate('UA-XXXX-XX');
	GoogleLogger::set_event_tracking_enabled(true);
	
After including the event tracking API you can call `analyticsTrackEvent(1)` from your
own code whenever you need to track an interaction event.

#### analyticsTrackEvent(1)

`analyticsTrackEvent` takes an object with a number of properties. You can define 
which ever properties you need to, otherwise the plugin will revert to some common
default values. Each of the provided properties in the object can either be a literal
value or a function which returns a literal value. This allows you to build complex
event labels and categories based on the entire DOM of a page and not just limited to
a single event object.

	$(this).analyticsTrackEvent({
		category: ..
		label: ..
		action: ...
		value: ..
	})


Property      | Default Value   | Optional?
------------- | --------------- | -----
Category      | window.location | No
Action        | 'click'         | No
Label         | $(this).text()  | No
Value         | NULL            | Yes


#### Examples

Tracks any link with a class of *track*. Takes the default options of `analyticsTrackEvent(1)`
as shown in the table above

	$("a.track").click(function() {
		$(this).analyticsTrackEvent();
	});

Tracks users focusing into a given text box. Sends a custom label and action label.

	$("input.track").focus(function() {
		$(this).trackEvent({
			label: 'Entered Text into box',
			action: 'focus'
		});
	});

Tracks when a user leaves an input box, what was the value that they entered into
the box. Demonstrates how you can use a function to return a dynamic value for any
of the properties.
	
	$("input.track").blur(function() {
		$(this).trackEvent({
			category: function(elem) { return $(elem).parents("form").attr("name"); },
			label: function(elem) { return $(elem).val(); },
			action: 'leave text field'
		})
	});

## FAQ

### I'm not seeing any event information

Google takes up to 48 hours to process event tracking information in many cases,
so you may have to wait several hours to see your results.

If you still don't see any information appearing, try using the official 
`ga_debug.js` extension to Google Chrome.

http://code.google.com/apis/analytics/docs/tracking/gaTrackingTroubleshooting.html#gaDebug