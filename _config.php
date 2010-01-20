<?php

DataObject::add_extension('SiteTree', 'GoogleAnalytics');

/**
 * Register a callback function for all page views.
 */
Object::add_extension('ContentController', 'CrawlerStatsDecorator');
Object::add_extension('SiteConfig', 'AnalyticsSetupDecorator');

?>