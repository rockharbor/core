/**
 * Namespaced object
 */
var CORE_date = {};

CORE_date.setup = function() {
	$('#DateRecurranceType').on('change', function() {
		var label = $('#frequency_label');

		$('#day').hide();
		$('#weekday').hide();
		$('#offset').hide();

		switch($(this).val()) {
			case 'h':
			label.text('hour(s)');
			break;
			case 'd':
			label.text('day(s)');
			break;
			case 'w':
			label.text('week(s)');
			$('#weekday').show();
			break;
			case 'md':
			label.text('month(s)');
			$('#day').show();
			break;
			case 'mw':
			label.text('month(s)');
			$('#offset').show();
			$('#weekday').show();
			break;
			case 'y':
			label.text('year(s)');
			break;
		}
	});

	$('#DateRecurring').on('change', function() {
		this.checked ? $('#pattern').show() : $('#pattern').hide();
	});

	$('#DatePermanent').on('change', function() {
		this.checked ? $('#end_date').hide() : $('#end_date').show();
		if (this.checked) {
			$('#DateEndDateYear').val($('#DateStartDateYear').val());
			$('#DateEndDateMonth').val($('#DateStartDateMonth').val());
			$('#DateEndDateDay').val($('#DateStartDateDay').val());
		}
	});

	$('#DateAllDay').on('change', function() {
		this.checked ? $('#start_time, #end_time').hide() : $('#start_time, #end_time').show();
	});

	$('#DateExemption').on('change', function() {
		if (this.checked) {
			$('#DateAllDay').prop('checked', true);
			$('#DateAllDay').closest('div').hide();
			$('#DateAllDay').change();
		} else {
			$('#DateAllDay').closest('div').show();
			$('#DateAllDay').change();
		}
	});

	// make everything update the humanized version
	$('select, input').on('change', function() {
		// only post one request at a time
		if (CORE_date.xhr) {
			CORE_date.xhr.abort();
		}
		CORE_date.xhr = $.ajax({
			type: 'POST',
			url: '/dates/readable.json',
			data: $('#DateStartDateMonth').closest('form').serialize(),
			dataType: 'json',
			success: function(data) {
				if (data.readable) {
					$('#humanized').text(data.readable);
				}
			}
		});
	});

	// initial setup
	$('#DateRecurranceType').change();
	$('#DateRecurring').change();
	$('#DatePermanent').change();
	$('#DateAllDay').change();
	$('#DateExemption').change();
}
