<?php

class GoogleDataController extends Controller {

	function performance($request) {
		
		$page = DataObject::get_by_id('SiteTree', (int)$request->param('ID'));
		$metrics = $request->requestVar('metrics');

		$q = substr(GoogleAnalyzer::get_sapphire_version(), 0, 3) == '2.3' ? '`' : '"';
		$markers = array();
		$events = DataObject::get('GoogleLogEvent', "{$q}GoogleLogEvent{$q}.{$q}PageID{$q} = 0 OR {$q}GoogleLogEvent{$q}.{$q}PageID{$q} = " . (int)$page->ID);
		if($events) foreach($events as $event) $markers[] = array(strtotime($event->Created) * 1000, $event->Title, 'Long descr.');
		$allversions = $q == '"' ?
			'"WasPublished" = 1 AND ' . DB::getConn()->datetimeDifferenceClause('"LastEdited"', date('Y-m-d 23:59:59', strtotime('-1 Year'))) . ' > 0' :
			"{$q}WasPublished{$q} = 1 AND {$q}LastEdited{$q} > '" . date('Y-m-d 23:59:59', strtotime('-1 Year')) . "'";
		foreach($page->allVersions($allversions) as $version) {
			$markers[] = array(strtotime($version->LastEdited) * 1000, 'Updated', 'Long descr.');
		}

		$store = new GoogleDataStore($page->getGoogleConfig('profile'), $page->getGoogleConfig('email'), $page->getGoogleConfig('password'));
		
		$url = trim($page->Link(), '/');
		if(!empty($url)) $url .= '/';
		
		$data = $store->fetchPerformance(array(
			'dimensions' => 'ga:date',
			'metrics' => 'ga:visits,ga:pageviews',
			'sort' => '-ga:date',
			'filters' => 'ga:pagePath==/' . $url,
		));

		return json_encode(array('series' => $data, 'markers' => $markers));
	}

}