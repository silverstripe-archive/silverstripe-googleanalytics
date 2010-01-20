<?php


/**
 * Test the class @see CrawlerStatsDecorator
 * @covers CrawlerStatsDecorator
 */
class CrawlerStatsDecoratorTest extends FunctionalTest {
	static $fixture_file = 'googleanalytics/tests/CrawlerStatsDecoratorTest.yml';

	// This saves having to publish all the fixture pages first
	static $use_draft_site = true;

	/**
	 * Test the function @see CrawlerStatsDecorator::onAfterInit
	 * @covers CrawlerStatsDecorator::onAfterInit
	 */
	function testOnAfterInit() {
		$page = $this->objFromFixture('Page', 'testpage1');
		$stats = DataObject::get_one('CrawlerStats', "Page = '{$page->URLSegment}'");
		$this->assertEquals($stats, false, "Obtained CrawlerStats when fixture should have none");

		// Fake the user agent
		$_SERVER['HTTP_USER_AGENT'] = 'Googlebot';
		$response = $this->get("{$page->URLSegment}");
		$this->assertEquals($response->getStatusCode(), 200, "Failed to retrieve the URL  '{$page->URLSegment}'");

		$stats = DataObject::get_one('CrawlerStats', "Page = '{$page->URLSegment}'");
		if ($stats) {
			$data = unserialize($stats->Data);
			$this->assertTrue(is_array($data) && isset($data[0]), "No relevant stats were found after page visit");
		} else {
			$this->assertFalse(true, "Failed to obtain CrawlerStats after visiting page");
		}
	}


}



?>
