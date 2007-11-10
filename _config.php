<?php
DataObject::add_extension('SiteTree', 'GoogleAnalytics');

/**
 * Register a callback function for all page views.
 */
Director::add_callback(array("GoogleAnalytics","initialize"));

?>