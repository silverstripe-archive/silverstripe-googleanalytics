# GOOGLE ANALYTICS

## Maintainer Contact
 * Julian Seidenberg <julian ( at) silverstripe (dot) com>

## Description

The Google Analytics module consists of 2 components that can be employed independently:
The Google Logger injects the google analytics javascript snippet into your source code and logs relevant events (as of now only crawler visits)
The Analyzer adds the GA UI to your CMS

## Tentative Roadmap
If you want to help out and develop some of these improvements please fork this project and submit a pull request (see this guide on how to do this: http://help.github.com/pull-requests/). I greatly appreciate any help for improving the module.

Quick improvements:

- Show time and date, not just date in graph legend
- Change color of events in the graph of pageview from red to something half-transparent and so that events don't make reading the graph so difficult
- Ability to filter different types of events (at the moment only page "save and publish" events) on and off
- New Metric: Show top incoming search keywords for each page
- New Metric: Show top incoming page links for each page
- New Metric: Show time visitors spend on each page
- Do a Google Search Engine submit when a page is "Saved and Published" so the page is re-indexed quicker.
- Hover state to show details of each event in the pageview graph

Bigger improvements:

- Tools that analyses content and suggests synonyms to use for top search keywords. Using synonyms will improve the search performance as people searching for the synonym terms will be able to find the site. 
- Visualize the visitors' navigation paths through the site. I.e. each page has graph of top other pages the visitor goes to from that page.
- Rewrite javascript using entwine for better long-term code maintenance
- Search keyword overlay on content. So search keywords show up in a different color when an admin is browsing the site.
- Add crawler visits as a type of event that can be filtered on/off in the graph
- Overlaying of graphs for comparison of site performs between different time periods / events
- Graph of the amount of time visitors spend on each page. Also show "average time spent on page: 5.54 sec (rank: 2 / 543 pages)".
- Report that identifies the most popular pages
- Summary reports that take in GA stats from a whole subsection of a site (a page and all its children) and displays that as graphs on the parent page


## Requirements

 * SilverStripe 3.1+

## DB Adapter Support
 * MySQL
 * SQLite
 * Postgres
 * SQL Server
 * Oracle (experimental)

## Installation

1. Follow the usual [module installation process](http://doc.silverstripe.org/framework/en/topics/modules#installation)
2. Activate the logger by adding the following to the end of your mysite/_config.php: `GoogleLogger::activate('UA-XXXXX-Y');` (hardcode google code, useful in combination with _ss_environment.php) or `GoogleLogger::activate('SiteConfig');` (use SiteConfig for setup)
3. Activate the analyzer by adding the following to the end of your mysite/_config.php: `GoogleAnalyzer::activate('1234567', "my@google.login", "mypassword");`	(hardcode credentials, useful in combination with _ss_environment.php) or `GoogleAnalyzer::activate('SiteConfig');` (use SiteConfig for setup)
4. If you wish to active the event tracking helper, include `GoogleLogger::set_event_tracking_enabled(true)`
5. Run dev/build (`http://www.mysite.com/dev/build?flush=all`)
6. If you're using SiteConfig populate your siteconfig in the CMS.

## Retrieving your credentials from GA

![Screenshot showing where to find your credentials in GA](https://raw.github.com/silverstripe-labs/silverstripe-googleanalytics/master/docs/help.png)

## Setup

### Register additional Crawler:

	GoogleLogger::$web_crawlers['Safari'] = 'safari';

Registers the additional crawler "Safari" when HTTP_USER_AGENT matches the regular expression "safari". Useful for debugging.

### Replace the stock google analytics javascript snippet:

The google analytics javascript snippet is designed as a SilverStripe template, so you can easily customize it by just placing your own version of the snippet in themes/yourthemename/templates/GoogleAnalyticsJSSnippet.ss. If you want to hardcode the snippet into your template you can just omit the google code in the logger and no snippet will be injected.

## Attention

Because the logger is by default only attached to the content controller the google analytics javascript snippet gets only injected on pages. Attach it to your custom controller to cover these calls also.

## Background information for developers

- [Google Analytics Data API - Data Feed](https://developers.google.com/analytics/devguides/reporting/core/v2/gdataReferenceDataFeed)
- [Google Analytics Data Feed Query Explorer](https://developers.google.com/analytics/devguides/reporting/core/gdataExplorer)

## Feedback

Please help to improve this module by submitting your feedback/bug reports/support requests/suggestions. Thanks