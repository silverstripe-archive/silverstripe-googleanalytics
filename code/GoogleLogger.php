<?php

class GoogleLogger extends Extension {

	// the Google Analytics code to be used in the JS snippet or
	protected static $google_analytics_code;

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
	}

	/**
	 *	Return various configuration values
	 *	
	 *	@param $key set:
	 *		String 'code' the Google Analytics code to be used in the JS snippet or
	 *	@return String the config value
	 *
	 **/
	protected function getGoogleConfig($key) {
		if(Object::has_extension('SiteConfig', 'GoogleConfig')) {
			$config = SiteConfig::current_site_config();
		}
		switch($key) {
			case 'code': return !empty($config) && $config->GoogleAnalyticsCode ? $config->GoogleAnalyticsCode : GoogleLogger::$google_analytics_code;
		}
	}

	public function onAfterInit() {

		// include the JS snippet into the frontend page markup
		if($this->getGoogleConfig('code')) {
			$googleanalyticsjssnippet = new ArrayData(array('GoogleAnalyticsCode' => $this->getGoogleConfig('code')));
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