<?php

class GoogleLogEvent extends DataObject {

	static $db = array(
		'Title' => 'Varchar',
		'Description' => 'Text',
	);

	static $has_one = array(
		'Page' => 'SiteTree',
	);
}