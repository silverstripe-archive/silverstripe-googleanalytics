<?php

class GooglePerformanceChart extends Compositefield {

	protected $page;

	function __construct($page = null) {
		parent::__construct();
		switch(true) {
			case $page instanceof SiteTree: $this->page = $page; break;
			case is_numeric($page): $page = DataObject::get_by_id('SiteTree', (int)$page); break;
		}
	}

	function FieldHolder() {
		return $this->renderWith('GooglePerformanceChart');
	}
	
	function PageID() {
		if($this->page) return $this->page->ID;
	}
}