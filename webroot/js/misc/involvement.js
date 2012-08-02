/**
 * Namespaced object
 */
var CORE_involvement = {};

/**
 * Sets up the paginated dots for dates
 */
CORE_involvement.setup = function() {
	$('#involvement-dates .pagination a').click(function(e) {
		$('#involvement-dates .pagination a span').removeClass('selected');
		$(this).children('span').addClass('selected');
		$('#involvement-dates .date').hide();
		var n = Number($(this).prop('target'));
		$('#involvement-dates .date:nth-child('+(n+1)+')').show();
		e.preventDefault();
		return false;
	});
	$('#involvement-dates .pagination a:first-child').click();
	var dateInterval = setInterval('CORE_involvement.cycleDates()', 5000);
	$('#involvement-dates .pagination a').mousedown(function() {
		clearInterval(dateInterval);
	});
}

/**
 * Cycles the events automatically
 * 
 * @return void
 */
CORE_involvement.cycleDates = function() {
	var next = $('#involvement-dates .pagination a span.selected').parent().next();
	if (next.length == 0) {
		next = $('#involvement-dates .pagination a:first-child');
	}
	next.click();
}