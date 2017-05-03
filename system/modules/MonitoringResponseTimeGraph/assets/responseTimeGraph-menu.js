/**
 * Move the response time graph a given percentage to left or right
 * @param {Number} percentage   For example 0.1 (left) or -0.1 (right)
 */
function moveResponseTimeGraph (percentage)
{
	var range = responseTimeGraph.getWindow();
	var interval = range.end - range.start;

	responseTimeGraph.setWindow(
	{
		start: range.start.valueOf() - interval * percentage,
		end:   range.end.valueOf()   - interval * percentage
	});
}

/**
 * Zoom the response time graph a given percentage in or out
 * @param {Number} percentage   For example 0.1 (zoom out) or -0.1 (zoom in)
 */
function zoomResponseTimeGraph (percentage)
{
	var range = responseTimeGraph.getWindow();
	var interval = range.end - range.start;

	responseTimeGraph.setWindow(
	{
		start: range.start.valueOf() - interval * percentage,
		end:   range.end.valueOf()   + interval * percentage
	});
}

// attach events to the navigation buttons
document.getElementById('zoomInResponseTimeGraph').onclick    = function () { zoomResponseTimeGraph(-0.2); };
document.getElementById('zoomOutResponseTimeGraph').onclick   = function () { zoomResponseTimeGraph( 0.2); };
document.getElementById('moveLeftResponseTimeGraph').onclick  = function () { moveResponseTimeGraph( 0.2); };
document.getElementById('moveRightResponseTimeGraph').onclick = function () { moveResponseTimeGraph(-0.2); };