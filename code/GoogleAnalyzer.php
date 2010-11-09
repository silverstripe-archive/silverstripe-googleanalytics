<?php

class GoogleAnalyzer extends DataObjectDecorator {

	// credentials for the Google Analytics API
	static protected $pofile_id;
	static protected $email;
	static protected $password;

	function extraStatics() {
		return array(
			'has_many' => array(
				'Events' => 'GoogleLogEvent',
			),
		);
	}

	/**
	 *	Activate the GoogleAnalyzer
	 *	
	 *	@param $profile String:
	 *		the Google Analytics profile ID or
	 *		'SiteConfig' for using the SiteConfig to configure this value
	 *	@param $email String email address of the account to use (can be left blank if configured with SiteConfig)
	 *	@param $password String password for the above account (can be left blank if configured with SiteConfig)
	 **/
	public static function activate($profile = 'SiteConfig', $email = null, $password = null) {
		
		switch($profile) {
			case 'SiteConfig': Object::add_extension('SiteConfig', 'GoogleConfig'); break;
			default: self::$profile_id = $profile; self::$email = $email; self::$password = $password;
		}
		
		Object::add_extension('SiteTree', 'GoogleAnalyzer');
	}

	/**
	 *	Return various configuration values
	 *	
	 *	@param $key String:
	 *		'profile' the Google Analytics profile id or
	 *		'email' the Google Analytics account's email address or
	 *		'password' the password for the above Google Analytics account
	 *	@return String the config value
	 **/
	public function getGoogleConfig($key) {
		if(Object::has_extension('SiteConfig', 'GoogleConfig')) {
			$config = SiteConfig::current_site_config();
		}
		switch($key) {
			case 'profile': return !empty($config) && $config->GoogleAnalyticsProfileId ? $config->GoogleAnalyticsProfileId : GoogleAnalyzer::$pofile_id;
			case 'email': return !empty($config) && $config->GoogleAnalyticsEmail ? $config->GoogleAnalyticsEmail : GoogleAnalyzer::$email;
			case 'password': return !empty($config) && $config->GoogleAnalyticsPassword ? $config->GoogleAnalyticsPassword : GoogleAnalyzer::$password;
		}
	}
	
	public function updateCMSFields(FieldSet $fields) {

		$fields->addFieldToTab('Root', new Tab('GoogleAnalytics', 'Google Analytics'));
		
		$fields->addFieldToTab("Root.GoogleAnalytics", new TabSet('Stats'));
		$fields->addFieldToTab('Root.GoogleAnalytics.Stats', new Tab('Performance', 'Performance'));
		$fields->addFieldToTab("Root.GoogleAnalytics.Stats.Performance", new GooglePerformanceChart($this->owner));
	}
}