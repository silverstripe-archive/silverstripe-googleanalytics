<?php

class GoogleAnalyticsTest extends SapphireTest
{

    public function testGetSapphireVersion()
    {
        $this->assertEquals('2.4', GoogleAnalyzer::get_sapphire_version());
    }
}
