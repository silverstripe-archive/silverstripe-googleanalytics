<?php

class GoogleDataController extends Controller
{

    private static $allowed_actions = array('performance');

    public function performance($request)
    {
        $markers = array();

        $q = substr(GoogleAnalyzer::get_sapphire_version(), 0, 3) == '2.3' ? '`' : '"';

        $metrics = $request->requestVar('metrics');

        $filters = null;
        $eventfiltersql = "{$q}GoogleLogEvent{$q}.{$q}PageID{$q} = 0";
        $page = SiteTree::get()->byID((int)$request->param('ID'));
        if ($page) {
            $url = trim($page->Link(), '/');
            if (!empty($url)) {
                $url .= '/';
            }
            $filters = 'ga:pagePath==/' . $url;
            $eventfiltersql .= " OR {$q}GoogleLogEvent{$q}.{$q}PageID{$q} = " . (int)$page->ID;
            $allversions = $q == '"' ?
                '"WasPublished" = 1 AND ' . DB::getConn()->datetimeDifferenceClause('"LastEdited"', date('Y-m-d 23:59:59', strtotime('-1 Year'))) . ' > 0' :
                "{$q}WasPublished{$q} = 1 AND {$q}LastEdited{$q} > '" . date('Y-m-d 23:59:59', strtotime('-1 Year')) . "'";
            foreach ($page->allVersions() as $version) {
                $markers[] = array(strtotime($version->LastEdited) * 1000, 'Updated', 'Long descr.');
            }
        }

        $events = DataObject::get('GoogleLogEvent', $eventfiltersql);
        if ($events) {
            foreach ($events as $event) {
                $markers[] = array(strtotime($event->Created) * 1000, $event->Title, 'Long descr.');
            }
        }

        $store = new GoogleDataStore(GoogleConfig::get_google_config('profile'), GoogleConfig::get_google_config('email'), GoogleConfig::get_google_config('password'));


        $data = $store->fetchPerformance(array(
            'dimensions' => 'ga:date',
            'metrics' => 'ga:visits,ga:pageviews',
            'sort' => '-ga:date',
            'filters' => $filters,
        ));

        return json_encode(array('series' => $data, 'markers' => $markers));
    }
}
