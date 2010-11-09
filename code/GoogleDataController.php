<?php

class GoogleDataController extends Controller {

	function performance($request) {
		
		$page = DataObject::get_by_id('SiteTree', (int)$request->param('ID'));
		$metrics = $request->requestVar('metrics');

		$markers = array();
		$events = DataObject::get('GoogleLogEvent', "\"GoogleLogEvent\".\"PageID\" = 0 OR \"GoogleLogEvent\".\"PageID\" = " . (int)$page->ID);
		if($events) foreach($events as $event) $markers[] = array(strtotime($event->Created) * 1000, $event->Title, 'Long descr.');
		foreach($page->allVersions('"WasPublished" = 1 AND ' . DB::getConn()->datetimeDifferenceClause('"LastEdited"', date('Y-m-d 23:59:59', strtotime('-1 Year'))) . ' > 0') as $version) {
			$markers[] = array(strtotime($version->LastEdited) * 1000, 'Updated', 'Long descr.');
		}

		$store = new GoogleDataStore($page->getGoogleConfig('profile'), $page->getGoogleConfig('email'), $page->getGoogleConfig('password'));
		
		$url = trim($page->Linky(), '/');
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