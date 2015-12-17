<?php

class GoogleLogEvent extends DataObject
{

    private static $db = array(
        'Title' => 'Varchar',
        'Description' => 'Text',
    );

    private static $has_one = array(
        'Page' => 'SiteTree',
    );
}
