<?php

class GoogleReport extends SS_Report {
	
	function title() {
		return _t('GoogleReport.REPORTTITLE',"Google Analytics");
	}

	function description() {
		return "add description";
	}

	function getCMSFields() {
		$fields = new FieldList();

		$chart = new GooglePerformanceChart();

		$fields->push(new LiteralField('ReportTitle', "<h3>{" . _t('GoogleReport.REPORTTITLE',"Google Analytics") ."}</h3>"));
		$fields->push(new LiteralField('ReportDescription', $chart->renderWith('GooglePerformanceChart')));

		return $fields;
	}
}
