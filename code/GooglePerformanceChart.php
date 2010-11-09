<?php

class GooglePerformanceChart extends Compositefield {

	protected $page;

	function __construct($page) {
		parent::__construct();
		if(!($page instanceof SiteTree)) $page = DataObject::get_by_id('SiteTree', $page);
		$this->page = $page;
	}

	function FieldHolder() {
		return $this->renderWith('GooglePerformanceChart');
	}
	
	function PageID() {
		return $this->page->ID;
	}
}