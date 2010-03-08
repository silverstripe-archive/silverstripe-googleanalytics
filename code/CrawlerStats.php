<?php
/**
 * CrawlerStats is the class that keeps track of the index status of pages.
 * It is a data object, and encompasses a table contain pages, and when they were
 * last indexed by search engines.
 *
 * Other engines can be added by modifying the spiders property in the form of
 * name => user-agent-string
 *
 * @package googleanalytics
 */
class CrawlerStats extends DataObject {
	static $db = array(
		"Page" => "Varchar",
		"Data" => "Varchar");
	
	static $defaults = array(
		"Data" => "a:0:{}");	
	
	static $spiders = array(
		"Google" => "Googlebot",
		"Yahoo" => "Yahoo",
		"MSN" => "msnbot");
	
	/**
	 * UpdateCrawls should be called on a page load with the user-agent loading that page
	 * if the user agent matches one of the known spiders, then it's crawler entry will be
	 * updated appropriately.
	 */
	public function UpdateCrawls($agent) {
		$data = unserialize($this->Data);
		
		$index = 0;
		$updated = false;
		foreach (self::$spiders as $pattern) {
			if(stristr($agent,$pattern)) {
				$data[$index] = time();
				$updated = true;
			}
			++$index;
		}
		$this->Data = serialize($data);
		
		return $updated;
	}
	
	/**
	 * PrintCrawls will return an html table containing the crawler status of the current
	 * data object.
	 *
	 * @param since the last time the page was updated
	 * @return an html table with a human-readable description of the pages index status
	 */
	public function printCrawls($since) {
		$result = "<h2>This page has been indexed by the following search engines:</h2>";
		$result .= "<table cellpadding=2><tr><th>Engine</th><th>Status</th><th>Last Indexed</th></tr>";
		$data = unserialize($this->Data);
		
		foreach (array_keys(self::$spiders) as $index => $display) {
			if(!isset($data[$index])) {continue;}
			$result .= "<tr><td><img src='googleanalytics/images/$display.png' alt='$display' style='float:left;padding-right:2px;'/>";
			$result .= $display;
			$result .= "</td><td>";
			if (strtotime($since) < $data[$index]) {
				$result .= "<img src = 'googleanalytics/images/tick.png' alt='Current Version' style='float:left;padding-right:2px;'/> Latest Version";
			} elseif ($data[$index]) {
				$result .= "<img src = 'googleanalytics/images/cross.png' alt='Old Version' style='float:left;padding-right:2px;'/> Old Version";
			}
			$result .= "<td>" . date("F j, g:i a",$data[$index]) . "</td>";
			$result .= "</td></tr>";
		}
		
		return $result . "</table>";
	}
	
	/**
	 * Provides a CrawlerStats object for a page
	 * if the page has never been crawled, a new object is created
	 */
	public static function GetFor($page) {
		$try = DataObject::get_one("CrawlerStats","\"Page\" = '$page'");
		if($try)
			return $try;
			
		$new = new CrawlerStats();
		$new->Page = $page;
		return $new;
	}
}

?>
