<?php

/*
 * This class ties the module into the main interface.
 * _config.php allows this class to modify the cms fields of all pages
 * and to call a function on every page load.
 */
class GoogleAnalytics extends Object {

	/**
	 * Returns a description of the indexing status of the current page.
	 */
	static function getIndexStatus() {
		$controller = Controller::currentController();
		if(method_exists($controller,"currentPage") && method_exists($controller->currentPage(),"ElementName"))
			$page = $controller->currentPage()->ElementName();
		else
			$page = "unknown";
		
		$indexResults = DataObject::get_one("CrawlerStats","Page = '$page/'");
		
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
	static function getCMSFields($fields) {

		$fields->addFieldToTab("Root.Reports.Index",new LiteralField("IndexStatus",self::getIndexStatus()));

		return $fields;
	}
	
	/**
	 * Run on every page load to inject google analyics code if set, and to record
	 * search engine crawls.
	 */
	static function Initialize() {
		
		/* Record Crawlers */
		$page = Director::currentURLSegment();
		
		$crawlers = CrawlerStats::GetFor($page);
		
		if($crawlers->UpdateCrawls($_SERVER['HTTP_USER_AGENT'])) {
			$crawlers->write();
		}
		
		/* Launch Analytics */
		$urchinid = DataObject::get_one("CrawlerStats","Page = '!!AnalyticsUrchinID'");
		if($urchinid->Data[0]=="U") {
			self::addAnalytics($urchinid->Data);
		}
	}
	
	
	/**
	 * Add google analytics javascript code to a page
	 */
	static function addAnalytics($uid) {
		Requirements::insertHeadTags("<script src='http://www.google-analytics.com/urchin.js' type='text/javascript'></script>");
		$script = <<<END
			if (document.addEventListener) {
				document.addEventListener ("load",function() {urchinTracker();},false);
			} else if (document.attachEvent) {
				document.attachEvent ("onload",function() {urchinTracker();});
			} else {
				oldLoader = document.onload;
				document.onload = function() {oldLoader(); urchinTracker();};
			}
			_uacct = "$uid";
END;
		Requirements::customScript($script, "urchin");
	}
	
	function link($action = null) {
			return "admin/sidereport/GoogleAnalytics/?mode=$action";
	}
	
	/**
	 * Provide the current analytics snippit as google provides it
	 * (The snippit is modifies when we actually include it on the page (the addAnalytics function))
	 * because we are limited to use the Requirements interface, and therefore need to include the code
	 * in the head, rather than at the end of the page.  The modified code makes up for this change.
	 */
	function currentJS() {
		$uid = DataObject::get_one("CrawlerStats", "Page = '!!AnalyticsUrchinID'");
		if($uid->Data[0]=="U") {
			$string = "<script src='http://www.google-analytics.com/urchin.js' type='text/javascript'></script>\n";
			$string .= "<script type='text/javascript'>\n";
			$string .= '_uacct = "' . $uid->Data . '";';
			$string .= "\nurchinTracker();\n";
			$string .= "</script>";
			return $string;
		}
		return "";
	}
	
	/**
	 * getHTML returns the html for setting up a google analytics urchin in the CMS.
	 */
	function getHTML() {
		$mode = $_GET['mode'];
		
		if($mode =='save') {
			$urchin = DataObject::get_one("CrawlerStats", "Page = '!!AnalyticsUrchinID'");
			if(!$urchin) {
				$urchin = new CrawlerStats();
				$urchin->Page = "!!AnalyticsUrchinID";
			}
			$urchin->Data = $_GET['urchin'];
			$urchin->write();
		}

		$response = new Form($this, "AnalyticsForm", new FieldSet(
			new LiteralField("info", "<h2>Paste Google's code below to collect statistics for this site</h2>"),
			new TextareaField("Analytics", "Analytics Tracking", 5, 20, $this->currentJS()),
			new LiteralField("save", "<button type='submit' onclick='return saveGoogleAnalytics(this.form);' value='Save' />Save</button>")
			), new FieldSet());
		return $response->forTemplate();
	}
	
}
?>