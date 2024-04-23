$(function () {
	var mydata1 = {
		labels: labels,
		xBegin: xDataBegin,
		xEnd: xDataEnd,
		datasets: [
			{
				fillColor: "rgba(220,220,220,0.5)",
				strokeColor: "rgba(220,220,220,1)",
				pointColor: "rgba(220,220,220,1)",
				pointStrokeColor: "#fff",
				data: yDataOld,
				xPos: xDataOld,
				title: xTitleOld
			},
			{
				fillColor: "rgba(151,187,205,0.5)",
				strokeColor: "rgba(151,187,205,1)",
				pointColor: "rgba(151,187,205,1)",
				pointStrokeColor: "#fff",
				data: yDataNew,
				xPos: xDataNew,
				title: xTitleNew
			},
		]
	};

	var newopts = chartOptions;

	if (yAxisLabel !== undefined) {
		newopts.yAxisLabel = yAxisLabel;
	}
	if (xAxisLabel !== undefined) {
		newopts.xAxisLabel = xAxisLabel;
	}

	var myLine = new Chart(document.getElementById("canvas_line").getContext("2d")).Line(mydata1, newopts);
});