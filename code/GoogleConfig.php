<?php

class GoogleConfig extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				'GoogleAnalyticsCode' => 'Varchar',
				'GoogleAnalyticsProfileId' => 'Varchar',
				'GoogleAnalyticsEmail' => 'Varchar',
				'GoogleAnalyticsPassword' => 'Varchar',
			),
		);
	}

	public function updateCMSFields(FieldSet $fields) {

		$fields->addFieldToTab("Root", new Tab('GoogleAnalytics'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsCode', 'Google Analytics Code (UA-XXXXXX-X)'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsProfileId', 'Google Analytics Profile ID (hidden in the URL parameter "id" of the "View Report" link inside Google Analytics)'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsEmail', 'GoogleAnalyticsEmail (the email address of the Google Analytics account to use)'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new PasswordField('GoogleAnalyticsPassword', 'Google Analytics Password (the password for the above account)'));
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
	public static function get_google_config($key) {
		if(class_exists('SiteConfig') && Object::has_extension('SiteConfig', 'GoogleConfig')) {
			$config = SiteConfig::current_site_config();
		}
		switch($key) {
			case 'code': 		return !empty($config) && $config->GoogleAnalyticsCode 		? $config->GoogleAnalyticsCode 		: GoogleLogger::$google_analytics_code;
			case 'profile': 	return !empty($config) && $config->GoogleAnalyticsProfileId	? $config->GoogleAnalyticsProfileId	: GoogleAnalyzer::$profile_id;
			case 'email': 		return !empty($config) && $config->GoogleAnalyticsEmail 	? $config->GoogleAnalyticsEmail 	: GoogleAnalyzer::$email;
			case 'password': 	return !empty($config) && $config->GoogleAnalyticsPassword 	? $config->GoogleAnalyticsPassword 	: GoogleAnalyzer::$password;
		}
	}
	
}