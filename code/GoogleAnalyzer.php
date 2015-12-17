<?php

/**
 * @package googleanalytics
 */
class GoogleAnalyzer extends DataExtension
{

    public static $sapphire_version;

    // credentials for the Google Analytics API
    public static $profile_id;
    public static $email;
    public static $password;
    public static $use_universal_snippet = false;

    private static $has_many = array(
        'Events' => 'GoogleLogEvent',
    );

    /**
     *	for legacy reasons
     *	@return String, version number, e.g. 2.4 or 2.3.6
     */
    public static function get_sapphire_version()
    {
        if (self::$sapphire_version) {
            return self::$sapphire_version;
        }

        if (class_exists('SiteTree')) {
            return method_exists('SiteTree', 'nested_urls') ? '2.4' : '2.3';
        }
    }

    /**
     *	Activate the GoogleAnalyzer
     *
     *	@param $profile String:
     *		the Google Analytics profile ID or
     *		'SiteConfig' for using the SiteConfig to configure this value
     *	@param $email String email address of the account to use (can be left blank if configured with SiteConfig)
     *	@param $password String password for the above account (can be left blank if configured with SiteConfig)
     **/
    public static function activate($profile = 'SiteConfig', $email = null, $password = null)
    {
        switch ($profile) {
            case 'SiteConfig': SiteConfig::add_extension('GoogleConfig'); break;
            default:
                self::$profile_id = $profile;
                self::$email = $email;
                self::$password = $password;
        }

        if (class_exists('SiteTree')) {
            SiteTree::add_extension('GoogleAnalyzer');
        }
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab('Root', Tab::create("GoogleAnalytics", _t('GoogleAnalyzer.TABTITLE', "Google Analytics")));
        $fields->addFieldToTab("Root.GoogleAnalytics", TabSet::create("Stats", _t('GoogleAnalyzer.STATS', "Stats")));
        $fields->addFieldToTab('Root.GoogleAnalytics.Stats', Tab::create("Performance", _t('GoogleAnalyzer.PERFORMANCE', "Performance")));
        $fields->addFieldToTab("Root.GoogleAnalytics.Stats.Performance", new GooglePerformanceChart($this->owner));
    }
}
