<?php

class GoogleLogger extends Extension {

	// the Google Analytics code to be used in the JS snippet or
	public static $google_analytics_code;

	// supported web crawlers, keys for nice names and values for signature regexes
	public static $web_crawlers = array(
		'Google' => 'googlebot',
		'Yahoo!' => 'yahoo! slurp|yahooseeker',
		'Bing' => 'msnbot',
		'Ask' => 'teoma',
	);

	/**
	 *	Activate the GoogleLogger
	 *	
	 *	@param $code mixed:
	 *		String the Google Analytics code to be used in the JS snippet or
	 *		String 'SiteConfig' for using the SiteConfig to configure this value or
	 *		Null if you hardcode the JS snippet into your template. The JS snippet will not be included through Requirements
	 *
	 **/
	public static function activate($code = null) {
		
		switch($code) {
			case null: self::$google_analytics_code = null; break;
			case 'SiteConfig': Object::add_extension('SiteConfig', 'GoogleConfig'); break;
			default: self::$google_analytics_code = $code;
		}

		Object::add_extension('ContentController', 'GoogleLogger');

		if(substr(GoogleAnalyzer::get_sapphire_version(), 0, 3) == '2.3') Director::add_callback(array("GoogleLogger","onAfterInit23"));
	}

	public function onAfterInit23() {
		if(Controller::curr() instanceof ContentController) Controller::curr()->onAfterInit();
	}

	public function onAfterInit() {

		// include the JS snippet into the frontend page markup
		if(GoogleConfig::get_google_config('code')) {
			$googleanalyticsjssnippet = new ArrayData(array('GoogleAnalyticsCode' => GoogleConfig::get_google_config('code')));
			Requirements::customScript($googleanalyticsjssnippet->renderWith('GoogleAnalyticsJSSnippet'));
		}

		// if this request comes from a web crawler, leave a trace
		if(isset($_SERVER['HTTP_USER_AGENT']) && $this->owner->data() instanceof SiteTree && $this->owner->data()->ID) {
			foreach(GoogleLogger::$web_crawlers as $name => $signature) {
				if(preg_match('/' . str_replace('/', "\\/", $signature) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
					$trace = new GoogleLogEvent();
					$trace->Title = $name;
					$trace->PageID = $this->owner->data()->ID;
					$trace->write();
				}
			}
		}
	}
}