<?php

class GoogleDataStore extends Object
{

    protected $labels = array(
        'visits' => 'Visits',
        'pageviews' => 'Page Views',
    );
    
    // Credentials to connect to Google Analytics
    protected $setup = array(
        'ids' => null,
        'email' => null,
        'password' => null,
        'url' => 'https://www.google.com/analytics/feeds/accounts/default',
    );
    
    // Default query parameters
    protected $query = array(
        'dimensions' => null,
        'metrics' => null,
        'segment' => null,
        'filters' => null,
        'sort' => null,
        'start-date' => null,
        'end-date' => null,
        'start-index' => 1,
        'max-results' => 1000,
    );

    public function __construct($ids, $email, $password, $url = null)
    {
        parent::__construct();
        $this->setup['ids'] = $ids;
        $this->setup['email'] = $email;
        $this->setup['password'] = $password;
        if ($url) {
            $this->setup['url'] = $url;
        }
        
        $this->setDefaults(array(
            'start-date' => date('Y-m-d', strtotime('-1 Year')),
            'end-date' => date('Y-m-d'),
        ));
    }
    
    public function setDefaults(array $defaults)
    {
        $this->query = array_merge($this->query, $defaults);
    }

    /**
     *	fetchData is just pulling in raw data like it is coming form the API and is supposed to be a data provider
     *	for functions that return certain formats like GoogleDataStore::fetchPerformance()
     *	
     *	@param Query assoc Array containing all necessary query parameters that are not already set, @see GoogleDataStore::$query
     *	@return Query Array containing raw data retrieved from the analytics_api.php
     **/
    public function fetchData($query)
    {
        require_once('../googleanalytics/thirdparty/analytics_api.php');
        
        // prep query params
        $query = array_merge($this->query, $query);
        
        // get cache object
        $q = substr(GoogleAnalyzer::get_sapphire_version(), 0, 3) == '2.3' ? '`' : '"';
        $hash = hash('md5', serialize(array_merge($this->setup, $query)));
        $cache = DataObject::get_one('GoogleCachedQuery', "{$q}GoogleCachedQuery{$q}.{$q}Hash{$q} = '$hash'");
        if (!$cache) {
            $cache = new GoogleCachedQuery(array('Hash' => $hash));
        }

        // poll fresh data if cached query is empty or outdated
        if (date('Y-m-d', strtotime($cache->LastEdited)) != date('Y-m-d')) {
            $api = new analytics_api();
            if ($api->login($this->setup['email'], $this->setup['password'])) {
                // $xml = $api->call($this->setup['url']);
                $data = $api->data(
                    'ga:' . $this->setup['ids'],
                    $query['dimensions'],
                    $query['metrics'],
                    $query['sort'],
                    $query['start-date'],
                    $query['end-date'],
                    $query['max-results'],
                    $query['start-index'],
                    $query['filters']
                );
                $cache->Data = serialize($data);
                $cache->write();
            } else {
                trigger_error('ERROR: failed to connect remote server.');
            }
        } else {
            $data = unserialize($cache->Data);
        }

        return $data;
    }
    
    /**
     *	fetchPerformance is @uses GoogleDataStore::fetchData() and formats the data to be json_encoded and
     *	so it can be used in jquery.flot.js
     *	
     *	@param Query assoc Array containing all necessary query parameters that are not already set, @see GoogleDataStore::$query
     *	@return Query Array propperly structured for the use in @see jquery.flot.js
     **/
    public function fetchPerformance($query)
    {
        $raw = $this->fetchData($query);
        
        foreach ($raw as $date => $numbers) {
            foreach ($numbers as $metric => $number) {
                $metric = substr($metric, 3);
                $out[$metric]['label'] = $this->labels[$metric];
                $out[$metric]['data'][] = array(strtotime(substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2)) * 1000, (float)$number);
            }
        }
        return $out;
    }
}
