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
				data: yDataOld2H,
				xPos: xDataOld2H,
				title: "old 2H"
			},
			{
				fillColor: "rgba(180,180,180,0.5)",
				strokeColor: "rgba(180,180,180,1)",
				pointColor: "rgba(180,180,180,1)",
				pointStrokeColor: "#fff",
				data: yDataOld1H,
				xPos: xDataOld1H,
				title: "old 1H"
			},
			{
				fillColor: "rgba(151,187,205,0.5)",
				strokeColor: "rgba(151,187,205,1)",
				pointColor: "rgba(151,187,205,1)",
				pointStrokeColor: "#fff",
				data: yDataNew2H,
				xPos: xDataNew2H,
				title: "new data"
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