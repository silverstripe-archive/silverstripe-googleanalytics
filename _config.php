<?php
/**
 * Register a callback function for the cms of all descendents of Page.
 */
Page::ExtendCMS(array("GoogleAnalytics","getCMSFields"));

/**
 * Register a callback function for all page views.
 */
Director::ExtendSite(array("GoogleAnalytics","Initialize"));

?>
