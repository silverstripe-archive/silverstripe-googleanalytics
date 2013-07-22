<% require css(googleanalytics/css/GooglePerformanceChart.css) %>
<% require javascript(sapphire/thirdparty/jquery-livequery/jquery.livequery.js) %>
<% require javascript(googleanalytics/thirdparty/excanvas/excanvas.js) %>
<% require javascript(googleanalytics/thirdparty/jquery.flot/jquery.flot.js) %>
<% require javascript(googleanalytics/thirdparty/jquery.flot/jquery.flot.selection.js) %>
<% require javascript(googleanalytics/javascript/GooglePerformanceChart.js) %>
<div id="GooglePerformanceChartOptions"><p id="choices"><%t GooglePerformanceChart.SHOW "Show" %>: </p><p id="loading"><%t GooglePerformanceChart.LOADING "loading" %>...</p></div>
<div id="GooglePerformanceChart" rel="$PageID"><%t GooglePerformanceChart.CHOOSEMETRICS "Choose metrics to display" %></div>