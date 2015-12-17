<?php

class GoogleCachedQuery extends DataObject
{

    private static $db = array(
        'Hash' => 'Varchar(255)',
        'Data' => 'Text',
    );
}
