/**
 * Js needed for the main navigation area
 *
 * @todo remove default text swap on the search bar when HTML5 comes in to play
 */

CORE.initNavigation = function() {
	$('#nav-campuses .campuses input:radio').change(function() {
		$('#nav-campuses li[id^=campus]').hide();
		$('#nav-campuses li#campus-'+$(this).val()).show();
	});
	$('#nav-campuses .campuses input:radio:first').change();

	$(document).on('mouseenter', '#nav-notifications ul li.notification', function() {
		var name = $(this).prop('id').split('-');
		var id = name[1];
		if ($(this).hasClass('unread')) {
			CORE.readNotification(id, this);
		}
	});
	$(document).on('click', '#nav-notifications ul li.notification .actions a', function(event) {
		event.preventDefault();
		var ele = $(this);
		CORE.request(this, {
			url: this.href,
			success: function() {
				$(ele).closest('.notification').fadeOut('fast');
				CORE.decrementCount();
			}
		});
		return false;
	});

	$('#nav-calendar').one('mouseover', function() {
		var cal = $(this).find('.fc')
		cal.fullCalendar('render');
	});
}

/**
 * Marks a notification as read
 *
 * @param id integer The id of the notification to read
 * @param ele Element The notification element
 */
CORE.readNotification = function(id, ele) {
	CORE.request(ele, {
		url: '/notifications/read/'+id,
		success: function() {
			$(ele).animate({borderLeftColor:'transparent'}, {
				duration: 'slow',
				complete: function() { $(ele).removeClass('unread').addClass('read'); }
			});
			CORE.decrementCount();
		}
	});
}

/**
 * Reduces the notification count by 1
 */
CORE.decrementCount = function() {
	var count = Number($('.notification-count').text()) - 1;
	if (count == 0) {
		$('.notification-counter').fadeOut('fast');
	} else {
		$('.notification-count').fadeOut('fast').text(count).fadeIn('fast');
	}
}

/**
 * Called after the ajax event is complete
 * 
 * Aggregates all events into a single area and attaches tooltips to event dates
 *
 * @param ele Element The id attribute of the calendar
 */
CORE.eventAfterLoad = function(ele) {
	// aggregate all events into one area
	$('#'+ele+' .fc-event').each(function() {
		var classes = $(this).prop('class').split(/\s+/);
		for (var c in classes) {
			if (classes[c].match(/(\d{4})-(\d{1,2})-(\d{1,2})/)) {
				// get html for the event and remove all fc-specific classes
				var html = $('<p>').append($(this).eq(0).clone().removeClass().removeAttr('style'));
				html.find('.fc-event-skin').each(function() {
					$(this).removeClass();
				});
				html = html.html();
				$('#'+ele+' .event:not(.fc-other-month).'+classes[c]+' .fc-day-content').filter(function() {
					// don't add duplicate events
					return $(this).html().indexOf(html) == -1;
				}).append(html);
			}
		}
	});
	// turn it into a tooltip
	$('#'+ele+' .event:not(.fc-other-month)').each(function() {
		var td = $(this);
		var content = td.find('.fc-day-content').html();
		td.find('.fc-day-content').html('<div style="position:relative"></div>');
		CORE.tooltip(td, content, {
			detachAfter: false,
			container: $('#'+ele)
		});
	});
}

/**
 * Called after an event is rendered. Makes days with events easier to find by 
 * adding an `event` and `yyyy-mm-dd` class to the event within the calendar, and
 * keeps track of them for removing special classes later in `CORE.eventLoading()`
 *
 * @param ele Element The id attribute of the calendar
 */
CORE.eventRender = function(cal, event, element, view) {
	var currentDate = event.start;
	var dayClass = currentDate.getFullYear() + '-' + (currentDate.getMonth()+1) + '-' + currentDate.getDate();
	var dates = $('#'+cal).data('dates') || [];
	
	// ignore events that won't show up anyway
	if (currentDate.getMonth() !== view.start.getMonth()) {
		return;
	}
	
	// mark the calendar date as having events and empty out the ones 
	// `$.fullcalendar()` generates
	$('#'+cal+' .fc-widget-content:not(.fc-other-month) .fc-day-number')
		.filter(function() {
			return $(this).text() == currentDate.getDate()
		})
			.closest('td')
			.addClass('event')
			.addClass(dayClass)
			.find('.fc-day-content')
				.html('<div style="position:relative"></div>')
	
	// remember which days have events so we can remove extra classes
	dates.push(dayClass);
	element.addClass(dayClass);
	$('#'+cal).data('dates', dates);
}

/**
 * Called when an ajax event gets called to load events
 *
 * Clears event information added by CORE.eventRender()
 *
 * @param ele Element The id attribute of the calendar
 */
CORE.eventLoading = function(ele) {
	$('#'+ele+' .event').each(function() {
		$(this).qtip('destroy');
		$(this).children('.fc-day-content').children('div').html('<div style="position:relative"></div>');
		$(this).removeClass('event');
		var dates = $(this).data('dates');
		for (var d in dates) {
			$(this).removeClass(dates[d]);
		}
		$(this).removeData('dates');
	});
}