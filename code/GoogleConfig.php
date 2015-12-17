<?php

/**
 * @package googleanalytics
 */
class GoogleConfig extends DataExtension
{

    private static $db = array(
        'GoogleAnalyticsCode' => 'Varchar',
        'GoogleAnalyticsProfileId' => 'Varchar(255)',
        'GoogleAnalyticsEmail' => 'Varchar',
        'GoogleAnalyticsPassword' => 'Varchar',
        'UseGoogleUniversalSnippet' => 'Boolean'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab("Root", new Tab('GoogleAnalytics'));
        $fields->addFieldToTab('Root.GoogleAnalytics', TextField::create("GoogleAnalyticsCode")->setTitle(_t('GoogleConfig.CODE', "Google Analytics Code"))->setRightTitle("(UA-XXXXXX-X)"));
        $fields->addFieldToTab('Root.GoogleAnalytics', TextField::create("GoogleAnalyticsProfileId")->setTitle(_t('GoogleConfig.PROFILEID', "Google Analytics Profile ID"))->setRightTitle(_t('GoogleConfig.PROFILEIDEXPLANATION', 'Hidden in the URL parameter "id" of the "View Report" link inside Google Analytics')));
        $fields->addFieldToTab('Root.GoogleAnalytics', TextField::create("GoogleAnalyticsEmail")->setTitle(_t('GoogleConfig.EMAIL', "Google Analytics Email"))->setRightTitle(_t('GoogleConfig.EMAILEXPLANATION', "The email address of the Google Analytics account to use")));
        $fields->addFieldToTab('Root.GoogleAnalytics', PasswordField::create("GoogleAnalyticsPassword")->setTitle(_t('GoogleConfig.PASSWORD', "Google Analytics Password"))->setRightTitle(_t('GoogleConfig.PASSWORDEXPLANATION', "The password for the above account")));
        $fields->addFieldToTab('Root.GoogleAnalytics',
            CheckboxField::create('UseGoogleUniversalSnippet')
                ->setTitle(_t('GoogleConfig.UNIVERSAL', 'Use Google Universal Snippet'))
        );
    }

    /**
     *	Return various configuration values
     *
     *	@param $key String:
     *		'profile' the Google Analytics profile id or
     *		'email' the Google Analytics account's email address or
     *		'password' the password for the above Google Analytics account
     *	@return String the config value
     **/
    public static function get_google_config($key)
    {
        if (class_exists('SiteConfig') && SiteConfig::has_extension('GoogleConfig')) {
            $config = SiteConfig::current_site_config();
        }

        switch ($key) {
            case 'code':        return !empty($config) && $config->GoogleAnalyticsCode        ? $config->GoogleAnalyticsCode        : GoogleLogger::$google_analytics_code;
            case 'profile':    return !empty($config) && $config->GoogleAnalyticsProfileId    ? $config->GoogleAnalyticsProfileId    : GoogleAnalyzer::$profile_id;
            case 'email':        return !empty($config) && $config->GoogleAnalyticsEmail    ? $config->GoogleAnalyticsEmail    : GoogleAnalyzer::$email;
            case 'password':    return !empty($config) && $config->GoogleAnalyticsPassword    ? $config->GoogleAnalyticsPassword    : GoogleAnalyzer::$password;
            case 'universal':    return !empty($config) && $config->UseGoogleUniversalSnippet ? $config->UseGoogleUniversalSnippet : GoogleAnalyzer::$use_universal_snippet;
        }
    }
}
