<?php
/**
 * This class ties the module into the main interface.
 * _config.php allows this class to modify the cms fields of all pages
 * and to call a function on every page load.
 *
 * @package googleanalytics
 */
class GoogleAnalytics extends DataObjectDecorator {

	/**
	 * Returns a description of the indexing status of the current page.
	 */
	static function getIndexStatus() {
		$controller = Controller::curr();
		if(method_exists($controller,"currentPage") && method_exists($controller->currentPage(),"ElementName"))
			$page = $controller->currentPage()->ElementName();
		else
			$page = "unknown";
		
		$indexResults = DataObject::get_one("CrawlerStats","\"Page\" = '$page'");
		
		$lastVersion = strtotime("now");
		
		if($page != "unknown") {
			$lastVersion = $controller->currentPage()->LastEdited;
		}
		
		if(!$indexResults)
			return "This Page has not been crawled by any search engines";
			
		return $indexResults->printCrawls($lastVersion);
	}
	
	/**
	 * Modifies the main cms for all pages to include a description of their status in search engines
	 */
	public function updateCMSFields($fields) {
		if (!$fields->fieldByName('Root.Reports')) {
			$fields->addFieldToTab("Root",new TabSet('Reports'));
		}
		$fields->addFieldToTab("Root.Reports.Index",new LiteralField("IndexStatus",self::getIndexStatus()));

		return $fields;
	}
	
	/**
	 * Add google analytics javascript code to a page
	 */
	static function addAnalytics() {
		$config = DataObject::get_one('SiteConfig');
		if (!$config) {
			return;
		}
		$script = $config->GoogleAnalyticsScript;
		if (empty($script)) {
			return;
		}
		Requirements::insertHeadTags("<script src='http://www.google-analytics.com/ga.js' type='text/javascript'></script>", "GA");
		Requirements::customScript($script, "ga");
	}
	
	function link($action = null) {
			return "admin/sidereport/GoogleAnalytics/?mode=$action";
	}


}
?>
