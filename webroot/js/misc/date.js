/**
 * Namespaced object
 */
var CORE_date = {};

CORE_date.setup = function() {
	$('#DateRecurranceType').bind('change', function() {
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

	$('#DateRecurring').bind('change', function() {
		this.checked ? $('#pattern').show() : $('#pattern').hide();
	});

	$('#DatePermanent').bind('change', function() {
		this.checked ? $('#end_date').hide() : $('#end_date').show();
		if (this.checked) {
			$('#DateEndDateYear').val($('#DateStartDateYear').val());
			$('#DateEndDateMonth').val($('#DateStartDateMonth').val());
			$('#DateEndDateDay').val($('#DateStartDateDay').val());
		}
	});

	$('#DateAllDay').bind('change', function() {
		this.checked ? $('#start_time, #end_time').hide() : $('#start_time, #end_time').show();
	});

	$('#DateExemption').bind('change', function() {
		if (this.checked) {
			$('#DateAllDay').attr('checked', 'checked');
			$('#DateAllDay').closest('div').hide();
			$('#DateAllDay').change();
		} else {
			$('#DateAllDay').closest('div').show();
			$('#DateAllDay').change();
		}
	});

	// date validation (forcing end dates to be greater than start)
	$('#DateStartDateMonth').bind('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateStartDateDay').bind('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateStartDateYear').bind('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateEndDateMonth').bind('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateEndDateDay').bind('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });
	$('#DateEndDateYear').bind('change', function() { CORE_date.validateDate('DateStartDate', 'DateEndDate'); });

	// time validation (forcing end times to be greater than start)
	$('#DateStartTimeHour').bind('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateStartTimeMin').bind('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateStartTimeMeridian').bind('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateEndTimeHour').bind('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateEndTimeMin').bind('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });
	$('#DateEndTimeMeridian').bind('change', function() { CORE_date.validateTime('DateStartTime', 'DateEndTime'); });

	// finally, make everything update the humanized version
	$('select, input').bind('change', function() { 
		var hr = CORE_date.makeHumanReadable({
			recurring: $('#DateRecurring').is(':checked'),
			type: $('#DateRecurranceType').val(),
			frequency: $('#DateFrequency').val(),
			allday: $('#DateAllDay').is(':checked'),
			day:$('#DateDay').val(),
			weekday: $('#DateWeekday').val(),
			offset: $('#DateOffset').val(),
			permanent: $('#DatePermanent').is(':checked'),
			startDate: $('#DateStartDateYear').val()+'-'+$('#DateStartDateMonth').val()+'-'+$('#DateStartDateDay').val(),
			endDate: $('#DateEndDateYear').val()+'-'+$('#DateEndDateMonth').val()+'-'+$('#DateEndDateDay').val(),
			startTime: (Number($('#DateStartTimeHour').val()) + ($('#DateStartTimeMeridian').val() == 'pm' ? 12 : 0))+':'+$('#DateStartTimeMin').val(),
			endTime: (Number($('#DateEndTimeHour').val()) + ($('#DateEndTimeMeridian').val() == 'pm' ? 12 : 0))+':'+$('#DateEndTimeMin').val()
		});

		$('#humanized').text(hr);
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
 * Makes a human readable date. Supports recurring, all day
 * permanent, etc. Pretty fancy, eh?
 *
 * #### Settings:
 * - boolean recurring True for recurring date
 * - boolean type Recurrance type (h:hourly, d:daily, w:weekly, m:monthly, y:yearly)
 * - integer frequency Recurrance frequency
 * - integer weekday Day of the week it recurs (for type:w)
 * - integer offset Week offset (for type:mw)
 * - integer day Day it recurs (for type:md)
 * - boolean allday True for all day event
 * - boolean permanent True for a never ending date
 * - string startDate Start date (Y-m-d)
 * - string endDate End date (Y-m-d)
 * - string startTime Start time (h:m)
 * - string endTime End time (h:m)
 *
 * @author Jeremy Harris <jharris@rockharbor.org>
 * @param object settings Recursion and date settings
 * @return string Human readable date string
 */
CORE_date.makeHumanReadable = function(settings) {	
	var months = new Array('','January','February','March','April','May','June','July','August','September','October','November','December');
	var weekdays = new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	var types = {h:'hour', d:'day', w:'week', md:'month', mw:'month', y:'year'};
	
	var readable = '';
	settings.startDate = settings.startDate.split('-');	
	var startDate = months[Number(settings.startDate[1])]+' '+Number(settings.startDate[2])+', '+settings.startDate[0];
	settings.endDate = settings.endDate.split('-');
	var endDate = months[Number(settings.endDate[1])]+' '+Number(settings.endDate[2])+', '+settings.endDate[0];
	settings.startTime = settings.startTime.split(':');
	if (settings.startTime[0] > 12) {
		settings.startTime[0] -= 12;
		settings.startTime[2] = 'p';
	} else {
		settings.startTime[2] = 'a';
	}
	var startTime = settings.startTime[0]+':'+settings.startTime[1]+settings.startTime[2];
	settings.endTime = settings.endTime.split(':');
	if (settings.endTime[0] > 12) {
		settings.endTime[0] -= 12;
		settings.endTime[2] = 'p';
	} else {
		settings.endTime[2] = 'a';
	}
	var endTime = settings.endTime[0]+':'+settings.endTime[1]+settings.endTime[2];
	
	// if not recurring, return simple!
	if (!settings.recurring) {
		if (startDate == endDate && !settings.allday) {
			if (startTime == endTime) {
				readable = startDate+' @ '+startTime;
			} else {
				readable = startDate+' from '+startTime+' to '+endTime;
			}
		} else if (settings.allday) {
			if (startDate == endDate) {
				readable = startDate+' all day';
			} else {
				readable = startDate+' to '+endDate;
			}
		} else {
			readable = startDate+' @ '+startTime+' to '+endDate+' @ '+endTime;
		}
		
		return readable;
	}	
	
	var type = types[settings.type];
	
	if (settings.frequency > 1) {
		type += 's';
	} else {
		settings.frequency = '';
	}
	
	var on = '';
	if (settings.type == 'w') {
		on = weekdays[Number(settings.weekday)];
	} else if (settings.type == 'mw') {
		on = Number(settings.offset);
		
		var sfx = ["th","st","nd","rd"];
		var val = on%100;
		on += (sfx[(val-20)%10] || sfx[val] || sfx[0]);
		
		on = 'the '+on+' '+weekdays[Number(settings.weekday)];
	} else if (type.indexOf('month') != -1) {
		on = Number(settings.day);
		
		var sfx = ["th","st","nd","rd"];
		var val = on%100;
		on += (sfx[(val-20)%10] || sfx[val] || sfx[0]);
		
		on = 'the '+on; 
	}
	
	if (settings.recurring) {
		readable = 'Recurs every '+settings.frequency+' '+type;	
		
		if (on != '') {
			readable += ' on '+on;
		}
		
		if (!settings.allday && type.indexOf('hour') == -1) {
			readable += ' from '+startTime+' to '+endTime;
		}
		
		if (type.indexOf('year') == -1) {
			readable += ' starting';
		} else {
			readable += ' on';
		}
	}
		
	readable += ' '+startDate;
	
	var fromorat = '';
	(startDate !== endDate && startTime !== endTime && !settings.permanent) ? fromorat = 'from' : fromorat = '@';
	
	if (!settings.allday && (!settings.recurring || type.indexOf('hour') != -1)) {
		readable += ' '+fromorat+' '+startTime;
	}
	
	if (startDate != endDate) {
		readable += ' until '+endDate;
	}
	
	if (!settings.allday) {	
		if (fromorat == 'from') {
			fromorat = ' to';
		}
		
		if (!settings.allday && (!settings.recurring || type.indexOf('hour') != -1)) {
			readable += ' '+fromorat+' '+endTime;
		}
	} else {
		readable += ' all day';
	}

	return readable;
};


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