<?php


/**
 * Test the class @see AnalyticsSetupDecorator
 * @covers AnalyticsSetupDecorator
 */
class AnalyticsSetupDecoratorTest extends SapphireTest {

	/**
	 * Test the function @see AnalyticsSetupDecorator::onBeforeWrite
	 * @covers AnalyticsSetupDecorator::onBeforeWrite
	 */
	function testOnBeforeWrite() {
		$config = new SiteConfig();
		$config->GoogleAnalyticsScript = <<<ENDSCRIPT
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-6967217-2");
pageTracker._trackPageview();
} catch(err) {}</script>
ENDSCRIPT;
		$config->write();
		$this->assertEquals($config->GoogleAnalyticsCode, 'UA-6967217-2');
		$desiredScript = "var pageTracker = _gat._getTracker(\"UA-6967217-2\");\npageTracker._trackPageview();\n";
		$this->assertEquals($config->GoogleAnalyticsCode, 'UA-6967217-2');
		$this->assertEquals($config->GoogleAnalyticsScript, $desiredScript);
	}

}



?>
