<?php

/**
 * This class decorates SiteConfig (see ../_config.php)
 * and provides storage and UI for the GoogleAnalyticsCode value.
 */
class AnalyticsSetupDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				'GoogleAnalyticsCode'	=>	'Varchar',
				'GoogleAnalyticsScript'	=>	'Text'
			)
		);
	}

	function updateEditFormFields($fields) {
		$fields->addFieldToTab('Root.GoogleAnalytics', 
				new HeaderField("info", "Paste Google's code below to collect statistics for this site", 2));
		$fields->addFieldToTab('Root.GoogleAnalytics', 
				new TextareaField("GoogleAnalyticsScript", "Analytics Tracking", 20, 5)
				);
		$codefield = new TextField("GoogleAnalyticsCode", "Analytics Tracking ID (will be calculated automatically)");
		$codefield->setReadonly(true);
		$codefield = $codefield->performReadonlyTransformation();
		$fields->addFieldToTab('Root.GoogleAnalytics', $codefield);
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		$script = $this->owner->GoogleAnalyticsScript;
		$matches = array();
		$pattern = '/_gat\._getTracker \( " (UA[^"]+) "/x';
		if (preg_match($pattern, $script, &$matches)) {
			$this->owner->GoogleAnalyticsCode = $matches[1];
			$string = "<script type='text/javascript'>\n";
			$string .= 'var pageTracker = _gat._getTracker("' . $matches[1] . '");';
			$string .= "\npageTracker._trackPageview();\n";
			$string .= "</script>";
			$this->owner->GoogleAnalyticsScript = $string;
		} else {
			Debug::log("couldn't match $pattern  against $script");
		}
	}
	
}
