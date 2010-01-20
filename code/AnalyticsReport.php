<?php
/**
 * This class provides the side report in the main cms, and contains the front-end
 * code for the cms side-report items.
 *
 * @package googleanalytics
 */
class AnalyticsReport extends SideReport {

	/**
	 * Title is called to get the name of the side report,
	 * this will need to happen during the initial loading
	 * of the cms, so is a good time to load the additional
	 * javascript.
	 */
	function title() {
		$t1 = _t('AnalyticsReport.LOADINGFORM','Loading Form...');
		$t2 = _t('AnalyticsReport.SAVING','Saving...');
		$t3 = _t('AnalyticsReport.SAVED','Saved!');
		$script = <<<EOF
		function viewGoogleAnalytics() {
			$('Form_EditForm').innerHTML = "<iframe name='analytics' src='http://analytics.google.com/' border='0' style='width:100%; height:100%;'></iframe>";
		}
		function setupGoogleAnalytics() {
			statusMessage('{$t1}');
			window.location = '/admin/0';
		}
EOF;
		Requirements::customScript($script,"Analytics");
		return "Analytics";
	}
	
	function records() {
		return array();
	}
	
	function fieldsToShow() {
		return array();
	}
	
	function getHTML() {
		$result = "<ul class=\"$this->class\">\n";
		$result .= "<li>\n";
		$result .= "<span onclick='viewGoogleAnalytics();' style=\"cursor:pointer\">" . _t('AnalyticsReport.VIEWANALYTICS','View Google Analytics') . "</span>\n";
		$result .= "</li><li>\n";
		$result .= "<span onclick='setupGoogleAnalytics();' style=\"cursor:pointer\">" . _t('AnalyticsReport.SETUPANALYTICS','Setup Analytics') . "</span>\n";
		$result .= "</li>\n";
		$result .= "</ul>\n";
		return $result;
	}
}

?>