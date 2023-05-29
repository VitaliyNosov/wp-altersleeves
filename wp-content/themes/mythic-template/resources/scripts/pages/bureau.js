$(document).ready(function ($) {
	let mcChartElement = $('.mc_pie_chart');
	if (!mcChartElement.length) return;

	let mcChartParent = mcChartElement.closest('.mc_pie_chart_container');
	if (!mcChartParent.length) return;

	let mcChartLabels = mcChartParent.find('.mc_pie_chart_data');
	if (!mcChartLabels.length) return;

	let chartLabels = [];
	let chartDataVals = [];

	mcChartLabels.each(function () {
		let currentElement = $(this);
		let dataVal = currentElement.attr('data-mc_pie_chart_data_value');
		if (typeof (dataVal) == 'undefined') return;

		chartLabels.push(currentElement.text());
		chartDataVals.push(dataVal)
	});

	let chartData = {
		labels: chartLabels,
		datasets: [{
			data: chartDataVals,
			backgroundColor: [
				'rgb(255, 99, 132)',
				'rgb(54, 162, 235)',
				'rgb(255, 205, 86)'
			],
			hoverOffset: 4
		}]
	};

	let Chart = require('../../../node_modules/chart.js/dist/chart.js');

	new Chart(mcChartElement, {
		type: 'doughnut',
		data: chartData,
		options: {
			plugins: {
				legend: {
					display: false,
				},
				tooltip: {
					callbacks: {
						label: function (context) {
							var label = context.label || '';
							if (label) {
								label += ': ';
							}
							if (context.parsed !== null) {
								label += context.parsed + '%'
							}

							return label;
						}
					}
				}
			}
		}
	});
});
