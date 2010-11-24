/**
 * Js needed for the main navigation area
 *
 * @todo remove default text swap on the search bar when HTML5 comes in to play
 */

CORE.initNavigation = function() {
	// attach auto complete
	CORE.autoComplete("SearchQuery", $('#nav-search form').attr('action')+'.json', function(item) {
		redirect(item.action);
	});
	CORE.defaultSearchText = $("#SearchQuery").val();
	$("#SearchQuery").focus(function() {if ($(this).val() == CORE.defaultSearchText) {
		$(this).val("");
		$(this).attr('class', 'search-over');
	}});
	$("#SearchQuery").blur(function() {if ($(this).val() == "") {
		$(this).val(CORE.defaultSearchText);
		$(this).attr('class', 'search-out');
	}});

	$('#nav-ministries .campuses input:radio').change(function() {
		$('#nav-ministries li[id^=campus]').hide();
		$('#nav-ministries li#campus-'+$(this).val()).show();
	});
	$('#nav-ministries .campuses input:radio:first').change();

	$('#nav-notifications ul li.notification').mouseenter(function() {
		var name = $(this).attr('id').split('-');
		var id = name[1];
		if ($(this).children('p').hasClass('unread')) {
			CORE.readNotification(id, this);
		}
		$(this).children('a.delete').show();
	});
	$('#nav-notifications ul li.notification').mouseleave(function() {
		$(this).children('a.delete').hide();
	});
	$('#nav-notifications ul li.notification a.delete').click(function(event) {
		event.preventDefault();
		CORE.request(this.href);
		$(this).parent().fadeOut('fast');
		return false;
	});
}

/**
 * Marks a notification as read
 *
 * @param id integer The id of the notification to read
 * @param ele Element The notification element
 */
CORE.readNotification = function(id, ele) {
	CORE.request('/notifications/read/'+id);
	$(ele).children('p').animate({borderLeftColor:'#fff'}, {
		duration: 'slow',
		complete: function() { $(this).removeClass('unread').addClass('read'); }
	});
	var count = Number($('.notification-count').text()) - 1;
	if (count == 0) {
		$('.notification-count').fadeOut('fast');
	} else {
		$('.notification-count').fadeOut('fast').text(count).fadeIn('fast');
	}
}