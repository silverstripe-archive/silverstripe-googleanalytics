<?php

class GoogleLogger extends Extension
{

    // the Google Analytics code to be used in the JS snippet or
    public static $google_analytics_code;

    /**
     * @var bool
     */
    public static $include_event_tracking = false;
    
    // supported web crawlers, keys for nice names and values for signature regexes
    public static $web_crawlers = array(
        'Google' => 'googlebot',
        'Yahoo!' => 'yahoo! slurp|yahooseeker',
        'Bing' => 'msnbot',
        'Ask' => 'teoma',
    );

    /**
     *	Activate the GoogleLogger
     *	
     *	@param $code mixed:
     *		String the Google Analytics code to be used in the JS snippet or
     *		String 'SiteConfig' for using the SiteConfig to configure this value or
     *		Null if you hardcode the JS snippet into your template. The JS snippet will not be included through Requirements
     *
     **/
    public static function activate($code = null)
    {
        switch ($code) {
            case null: self::$google_analytics_code = null; break;
            case 'SiteConfig': SiteConfig::add_extension('GoogleConfig'); break;
            default: self::$google_analytics_code = $code;
        }

        Controller::add_extension('GoogleLogger');

        if (substr(GoogleAnalyzer::get_sapphire_version(), 0, 3) == '2.3') {
            Director::add_callback(array("GoogleLogger", "onAfterInit23"));
        }
    }

    public function onAfterInit23()
    {
        if (Controller::curr() instanceof ContentController) {
            Controller::curr()->onAfterInit();
        }
    }

    public function onAfterInit()
    {
        if (
            $this->owner instanceof DevelopmentAdmin ||
            $this->owner instanceof DatabaseAdmin ||
            (class_exists('DevBuildController') && $this->owner instanceof DevBuildController)
        ) {
            return;
        }

        // include the JS snippet into the frontend page markup
        if (GoogleConfig::get_google_config('code')) {
            $snippet = new ArrayData(array(
                'GoogleAnalyticsCode' => GoogleConfig::get_google_config('code'),
                'UseGoogleUniversalSnippet' => GoogleConfig::get_google_config('universal')
            ));

            Requirements::customScript($snippet->renderWith('GoogleAnalyticsJSSnippet'));
        }

        // if this request comes from a web crawler, leave a trace
        if (isset($_SERVER['HTTP_USER_AGENT']) && $this->owner instanceof ContentController && $this->owner->data()->ID) {
            foreach (GoogleLogger::$web_crawlers as $name => $signature) {
                if (preg_match('/' . str_replace('/', "\\/", $signature) . '/i', $_SERVER['HTTP_USER_AGENT'])) {
                    $trace = new GoogleLogEvent();
                    $trace->Title = $name;
                    $trace->PageID = $this->owner->data()->ID;
                    $trace->write();
                }
            }
        }
        
        // include event tracking api if required, jQuery 1.5 is required for automatic data attributes
        if (self::event_tracking_enabled()) {
            Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
            Requirements::javascript('googleanalytics/javascript/googleanalytics.event.tracking.js');
        }
    }
    
    /**
     * @param bool
     */
    public static function set_event_tracking_enabled($bool)
    {
        self::$include_event_tracking = (bool) $bool;
    }
    
    /**
     * @return bool
     */
    public static function event_tracking_enabled()
    {
        return self::$include_event_tracking;
    }
}
