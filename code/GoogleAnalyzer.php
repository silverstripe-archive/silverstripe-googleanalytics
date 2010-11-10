<?php

class GoogleAnalyzer extends DataObjectDecorator {

	static public $sapphire_version;

	// credentials for the Google Analytics API
	public static $profile_id;
	public static $email;
	public static $password;

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
		SS_Report::register("ReportAdmin", "GoogleReport");
		Object::add_extension('SiteTree', 'GoogleAnalyzer');
	}

	public function updateCMSFields(FieldSet $fields) {

		$fields->addFieldToTab('Root', new Tab('GoogleAnalytics', 'Google Analytics'));
		
		$fields->addFieldToTab("Root.GoogleAnalytics", new TabSet('Stats'));
		$fields->addFieldToTab('Root.GoogleAnalytics.Stats', new Tab('Performance', 'Performance'));
		$fields->addFieldToTab("Root.GoogleAnalytics.Stats.Performance", new GooglePerformanceChart($this->owner));
	}
}