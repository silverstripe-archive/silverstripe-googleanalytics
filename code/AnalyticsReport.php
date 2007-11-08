<?php
/**
 * This class provides the side report in the main cms, and contains the front-end
 * code for the cms side-report items.
 */
class AnalyticsReport extends SideReport {

	/**
	 * Title is called to get the name of the side report,
	 * this will need to happen during the initial loading
	 * of the cms, so is a good time to load the additional
	 * javascript.
	 */
	function title() {
		$script = <<<EOF
		function viewGoogleAnalytics() {
			$('Form_EditForm').innerHTML = "<iframe name='analytics' src='http://analytics.google.com/' border='0' style='width:100%; height:100%;'></iframe>";
		}
		function setupGoogleAnalytics() {
			statusMessage('Loading Form...');
			$('Form_EditForm').loadURLFromServer('admin/sidereport/GoogleAnalytics/?mode=display');
		}
		function saveGoogleAnalytics(form) {
			statusMessage('Saving...');
			var text = form.Analytics.value.split('"');
			var urchin = text[text.length -2];
			
			$('Form_EditForm').loadURLFromServer('admin/sidereport/GoogleAnalytics/?mode=save&urchin='+urchin);
			statusMessage('Saved!',"good");
			return false;
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
		$result .= "<span onclick='viewGoogleAnalytics();'>View Google Analytics</span>\n";
		$result .= "</li><li>\n";
		$result .= "<span onclick='setupGoogleAnalytics();'>Setup Analytics</span>\n";
		$result .= "</li>\n";
		$result .= "</ul>\n";
		return $result;
	}
}

?>