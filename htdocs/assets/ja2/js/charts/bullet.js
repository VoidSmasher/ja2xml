$(function () {
	var mydata1 = {
		labels : labels,
		// xBegin: xDataBegin,
		// xEnd: xDataEnd,
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				data : yDataOld2H,
				xPos : xDataOld2H,
				title : "Bullet"
			},
		]
	};

	var newopts = {
		inGraphDataShow : false,
		datasetFill : true,
		pointDot: true,
		scaleLabel: "<%=value%>",
		scaleFontSize : 16,
		canvasBorders : true,
		legend : true,
		annotateDisplay : true,
		dynamicDisplay : true
	};

	var myLine = new Chart(document.getElementById("canvas_line").getContext("2d")).Line(mydata1,newopts);

});