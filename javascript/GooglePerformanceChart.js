(function($) {

	$(document).ready(function() {

		var status;
		var datasets;
		var markers;
		var previousPoint;
		var options;
		var i;

		function initPlotter() {
			status = 'uninitialized';

			previousPoint = null;
			
			markers = [];

			datasets = {
				"visits": { label: "Visits" },
				"pageviews": { label: "Page Views" }
		    };

			options = {
				xaxis: { mode: "time", timeformat: "%d %b" },
				yaxis: { min: 0 },
				selection: { mode: "x" },
				grid: { markings: weekendAreas, hoverable: true, autoHighlight: true }
			};

			i = 0;
			$.each(datasets, function(key, val) {
				val.color = i;
				++i;
			});

			choiceContainer = $("#choices");
			$.each(datasets, function(key, val) {
				choiceContainer.append('<input type="checkbox" name="' + key +
					'" id="id' + key + '" /> ' +
					'<label for="id' + key + '">'
					+ val.label + '</label> ');
			});
			choiceContainer.find("input").click(function(){
				if(status == 'polling') return false;
				if(status == 'uninitialized') {
					status = 'polling';
					var pageid = $('#GooglePerformanceChart').attr('rel');
					$('#loading').show();
					$.ajax({
						url: 'GoogleDataController/performance/' + pageid,
						async: false,
						dataType: 'json',
						error: function(XMLHttpRequest, textStatus){
							status = 'error';
							alert(textStatus);
							$('#loading').hide();
						},
						success: function(data){
							status = 'initialized';
							datasets = data.series;
							markers = data.markers;
							$('#loading').hide();
						}
					});
				}

				plotAccordingToChoices();
			});
			
			$("#GooglePerformanceChart").bind("plothover", function (event, pos, item) {

				var mark;
				for(i in markers) {
					var diff = Math.abs(markers[i][0] - pos.x);
//					console.log(diff);
					if(diff < 5) if(!mark || Math.abs(mark[0] - pos.x) > diff) mark = markers[i];
				}

				if(1) {
					if(item) {
						if (previousPoint != item.datapoint) {
							previousPoint = item.datapoint;

							$("#tooltip").remove();
							var x = item.datapoint[0].toFixed(2),
								y = item.datapoint[1].toFixed(2);

							var xd = new Date(parseInt(x));
							showTooltip(item.pageX, item.pageY,
								item.series.label + " on " + xd.toLocaleDateString() + " = " + parseInt(y));
						}
					} else if(mark) {
						// console.log(mark);
						// console.log(pos.x);
						// console.log(pos.y);
						if (previousPoint != mark) {
							previousPoint = mark;

							$("#tooltip").remove();
							var x = new Date(parseInt(mark[0]));
							showTooltip(pos.x, pos.y, mark[1] + " on " + x.toLocaleDateString());
						}
					} else {
						$("#tooltip").remove();
						previousPoint = null;            
					}
				}
			});

			$("#GooglePerformanceChart").bind("plotclick", function (event, pos, item) {
				console.log(item);
			});
		}
		
		function showTooltip(x, y, contents) {
			$('<div id="tooltip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#fee',
				opacity: 0.80,
				'z-index': 10000
			}).appendTo("body").fadeIn(200);
		}

		function weekendAreas(axes) {
			var markings = [];
			var d = new Date(axes.xaxis.min);
			// go to the first Saturday
			d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
			d.setUTCSeconds(0);
			d.setUTCMinutes(0);
			d.setUTCHours(0);
			var i = d.getTime();
			do {
				// when we don't set yaxis, the rectangle automatically
				// extends to infinity upwards and downwards
				markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
				i += 7 * 24 * 60 * 60 * 1000;
			} while (i < axes.xaxis.max);

			for(ind in markers) if(axes.xaxis.min < markers[ind][0] && markers[ind][0] < axes.xaxis.max) markings.push({ xaxis: { from: markers[ind][0], to: markers[ind][0] }, color: "#ff0000" });

			return markings;
		}

		function plotAccordingToChoices() {
			var data = [];

			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key])
					data.push(datasets[key]);
			});

			if(data.length > 0) {
				var plot = $.plot($("#GooglePerformanceChart"), data, options);
				var overview = $.plot($("#GooglePerformanceChartOverview"), data, {
					series: {
						lines: { show: true, lineWidth: 1 },
						shadowSize: 0
					},
					xaxis: { ticks: [], mode: "time" },
					yaxis: { ticks: [], min: 0, autoscaleMargin: 0.1 },
					selection: { mode: "x" },
					legend: { show: false }
				});
				$("#GooglePerformanceChart").bind("plotselected", function (event, ranges) {
					// do the zooming
					plot = $.plot($("#GooglePerformanceChart"), data,
						$.extend(true, {}, options, {
							xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
						}));

					// don't fire event on the overview to prevent eternal loop
					overview.setSelection(ranges, true);
				});

				$("#GooglePerformanceChartOverview").bind("plotselected", function (event, ranges) {
					plot.setSelection(ranges);
				});
			}
		}
		
		function maxPaneSize() {
			var avail = new Array(500, 640);    //size of the chart to use
			var offset = new Array($('#GooglePerformanceChartOverview').outerHeight(), $('#GooglePerformanceChartOverview').outerWidth());
			var max = new Array(avail[0] - offset[0] - 40, avail[1] - 20);
			var current = new Array($('#GooglePerformanceChart').height(), $('#GooglePerformanceChart').width());
			if(max[0] != current[0]) $('#GooglePerformanceChart').height(max[0]);
			if(max[1] != current[1]) $('#GooglePerformanceChart').width(max[1]);
		}

		$("#GooglePerformanceChart").entwine({
			onmatch: function() {
				initPlotter();
				plotAccordingToChoices();

				// maximize the graph
				setInterval(maxPaneSize, 2000);
			}
		});
	});

})(jQuery);