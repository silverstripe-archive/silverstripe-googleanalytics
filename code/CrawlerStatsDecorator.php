<?php


/**
 * This decorates the ContentController (see ../_config.php)
 * and tracks page views, by User Agent.
 */
class CrawlerStatsDecorator extends Extension {


	/**
	 * Run on every page load to inject google analyics code if set, and to record
	 * search engine crawls.
	 */
	public function onAfterInit() {
		/* Record Crawlers */
		$page = $this->owner->URLSegment;
		
		$crawlers = CrawlerStats::GetFor($page);
		
		if($crawlers->UpdateCrawls($_SERVER['HTTP_USER_AGENT'])) {
			$crawlers->write();
		}
		
		/* Launch Analytics */
		GoogleAnalytics::addAnalytics();
	}
}

?>
