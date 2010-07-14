<?php
/**
 * This class provides the side report in the main cms, and contains the front-end
 * code for the cms side-report items.
 *
 * @package googleanalytics
 */
class AnalyticsReport extends SS_Report {

	function description() {
		return 'Google analytics report';
	}

	/**
	 * Title is called to get the name of the side report,
	 * this will need to happen during the initial loading
	 * of the cms, so is a good time to load the additional
	 * javascript.
	 */
	function title() {

		$siteconfiglink = BASE_URL . '/admin/show/root#Root_GoogleAnalytics';
		$t1 = _t('AnalyticsReport.LOADINGFORM','Loading Form...');
		$t2 = _t('AnalyticsReport.SAVING','Saving...');
		$t3 = _t('AnalyticsReport.SAVED','Saved!');
		$script = <<<EOF
		function viewGoogleAnalytics() {
			$('Form_EditForm').innerHTML = "<iframe name='analytics' src='http://analytics.google.com/' border='0' style='width:100%; height:100%;'></iframe>";
		}
		function setupGoogleAnalytics() {
			statusMessage('{$t1}');
			window.location = '$siteconfiglink';
		}
EOF;
		Requirements::customScript($script,"Analytics");

		return "Google Analytics";
	}
	
	function getCMSFields() {
		$fields = new FieldSet(
			new LiteralField(
				'ReportTitle', 
				 "<h3>{$this->title()}</h3>"
			),
			new LiteralField('ReportDescription', $this->description()),
			new LiteralField('ReportContent', $this->getHTML())
		);
		return $fields;
	}

	function getHTML() {
		$viewAnalytics = _t('AnalyticsReport.VIEWANALYTICS','View Google Analytics');
		$setupAnalytics = _t('AnalyticsReport.SETUPANALYTICS','Setup Google Analytics');
		$result = <<<ENDRESULT
			<ul class="{$this->class}"
			<li><a href="#" onclick='viewGoogleAnalytics();'>{$viewAnalytics}</a></li>
			<li><a href="#" onclick='setupGoogleAnalytics();'>{$setupAnalytics}</a></li>
			</ul>
ENDRESULT;
		return $result;
	}
}

?>
