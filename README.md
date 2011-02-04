# GOOGLE ANALYTICS

## Maintainer Contact
 * Andreas Piening <andreas (at) silverstripe (dot) com>

## Description

The Google Analytics module consists of 2 components that can be employed independently:
The Google Logger injects the google analytics javascript snippet into your source code and logs relevant events (as of now only crawler visits)
The Analyzer adds the GA UI to your CMS

## Requirements

 * SilverStripe 2.3 or newer (for 2.3 use the hardcode activation since SiteConfig doesn't exist in 2.3)

## DB Adapter Support
 * MySQL
 * SQLite
 * Postgres
 * SQL Server
 * Oracle (experimental)

## Installation

1. follow the usual [module installation process](http://doc.silverstripe.org/modules#installation)
2. activate the logger by adding the following to the end of your mysite/_config.php: `GoogleLogger::activate('UA-XXXXX-Y');` (hardcode google code, useful in combination with _ss_environment.php) or `GoogleLogger::activate('SiteConfig');` (use SiteConfig for setup)
3. activate the analyzer by adding the following to the end of your mysite/_config.php: `GoogleAnalyzer::activate('1234567', "my@google.login", "mypassword");`	(hardcode credentials, useful in combination with _ss_environment.php) or `GoogleAnalyzer::activate('SiteConfig');` (use SiteConfig for setup)
4. run dev/build (http://www.mysite.com/dev/build?flush=all)
5. if you're using SiteConfig populate your siteconfig in the CMS.

## Retrieving your credentials from GA

![Screenshot showing where to find your credentials in GA](help.png)

## Setup

### Register additional Crawler:

	GoogleLogger::$web_crawlers['Safari'] = 'safari';

Registers the additional crawler "Safari" when HTTP_USER_AGENT matches the regular expression "safari". Useful for debugging.

### Replace the stock google analytics javascript snippet:

The google analytics javascript snippet is designed as a SilverStripe template, so you can easily customize it by just placing your own version of the snippet in themes/yourthemename/templates/GoogleAnalyticsJSSnippet.ss. If you want to hardcode the snippet into your template you can just omit the google code in the logger and no snippet will be injected.

## Attention

Because the logger is by default only attached to the content controller the google analytics javascript snippet gets only injected on pages. Attach it to your custom controller to cover these calls also.

## Background information for developers

- [Google Analytics Data API - Data Feed](http://code.google.com/apis/analytics/docs/gdata/gdataReferenceDataFeed.html)
- [Google Analytics Data Feed Query Explorer](http://code.google.com/apis/analytics/docs/gdata/gdataExplorer.html)

## Feedback

Please help to improve this module by submitting your feedback/bug reports/support requests/suggestions. Thanks