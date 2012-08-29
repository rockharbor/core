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

	// date validation (forcing end dates to be greater than start)
	$('#DateStartDateMonth').on('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateStartDateDay').on('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateStartDateYear').on('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateEndDateMonth').on('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateEndDateDay').on('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateEndDateYear').on('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });

	// time validation (forcing end times to be greater than start)
	$('#DateStartTimeHour').on('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateStartTimeMin').on('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateStartTimeMeridian').on('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateEndTimeHour').on('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateEndTimeMin').on('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateEndTimeMeridian').on('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });

	// finally, make everything update the humanized version
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
	$('#DateStartDateMonth').change();
	$('#DateStartTimeHour').change();
	$('#DateExemption').change();
}

/**
 * Forces an end date to be greater or equal to it's start date
 *
 * @param string startDateModel The id prefix for the Cake model's start date, i.e., DateStartDate ([Date][start_date])
 * @param string endDateModel The id prefix for the associated end date, i.e., DateEndDate ([Date][end_date])
 */
CORE_date.validateDate = function(startDateModel, endDateModel) {
	// get the start date
	var startDate = new Date($('#'+startDateModel+'Year').val(),$('#'+startDateModel+'Month').val()-1,$('#'+startDateModel+'Day').val());
	var endDate = new Date($('#'+endDateModel+'Year').val(),$('#'+endDateModel+'Month').val()-1,$('#'+endDateModel+'Day').val());

	if (endDate < startDate) {
		$('#'+endDateModel+'Year').val($('#'+startDateModel+'Year').val());
		$('#'+endDateModel+'Month').val($('#'+startDateModel+'Month').val());
		$('#'+endDateModel+'Day').val($('#'+startDateModel+'Day').val());
	}
}

/**
 * Forces an end time to be greater or equal to it's start time
 *
 * @param string startTimeModel The id prefix for the Cake model's start time, i.e., DateStartTime ([Date][start_time])
 * @param string endTimeModel The id prefix for the associated end time, i.e., DateEndTime ([Date][end_time])
 */
CORE_date.validateTime = function(startTimeModel, endTimeModel) {
	var startTime = new Date();
	var startHours = $('#'+startTimeModel+'Hour').val();
	if ($('#'+startTimeModel+'Meridian').val() == 'pm' && startHours < 12) {
		startHours += 12;
	}
	startTime.setHours(startHours);
	startTime.setMinutes($('#'+startTimeModel+'Min').val());
	
	var endTime = new Date();
	var endHours =  $('#'+endTimeModel+'Hour').val();
	if ($('#'+endTimeModel+'Meridian').val() == 'pm' && endHours < 12) {
		endHours += 12;
	}
	endTime.setHours(endHours);
	endTime.setMinutes($('#'+endTimeModel+'Min').val());
	
	if (endTime < startTime) {
		$('#'+endTimeModel+'Hour').val($('#'+startTimeModel+'Hour').val());
		$('#'+endTimeModel+'Min').val($('#'+startTimeModel+'Min').val());
		$('#'+endTimeModel+'Meridian').val($('#'+startTimeModel+'Meridian').val());
	}
}