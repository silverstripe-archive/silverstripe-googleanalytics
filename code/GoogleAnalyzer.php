<?php

class GoogleAnalyzer extends DataObjectDecorator {

	static public $sapphire_version;

	// credentials for the Google Analytics API
	static protected $profile_id;
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
	 *	for legacy reasons
	 *	@return String, version number, e.g. 2.4 or 2.3.6
	 */
	public static function get_sapphire_version() {
		if(self::$sapphire_version) return self::$sapphire_version;
		return method_exists('SiteTree', 'nested_urls') ? '2.4' : '2.3';
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
			default:
				self::$profile_id = $profile;
				self::$email = $email;
				self::$password = $password;
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
		if(class_exists('SiteConfig') && Object::has_extension('SiteConfig', 'GoogleConfig')) {
			$config = SiteConfig::current_site_config();
		}
		switch($key) {
			case 'profile': return !empty($config) && $config->GoogleAnalyticsProfileId ? $config->GoogleAnalyticsProfileId : GoogleAnalyzer::$profile_id;
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