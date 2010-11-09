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
		$fields->addFieldToTab('Root', new Tab('GoogleAnalytics', 'Google Analytics'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsCode', 'Google Analytics Code (UA-XXXXXX-X)'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsProfileId', 'Google Analytics Profile ID (hidden in the URL parameter "id" of the "View Report" link inside Google Analytics)'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsEmail', 'GoogleAnalyticsEmail (the email address of the Google Analytics account to use)'));
		$fields->addFieldToTab('Root.GoogleAnalytics', new TextField('GoogleAnalyticsPassword', 'Google Analytics Password (the password for the above account)'));
	}
}