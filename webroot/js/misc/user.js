/**
 * Namespaced object
 */
var CORE_user = {};

/**
 * The current number of household member elements added
 *
 * @var int
 */
CORE_user.member = 0;

/**
 * The element for additional household members (set in view)
 *
 * @var string
 */
CORE_user.element = '';

/**
 * Initializes functionality needed for user registration
 * 
 * @param string id The id of the tab div
 */
CORE_user.init = function(id) {
	var submit = $('#'+id).find(':submit');
	if (submit.attr('id') == '') {
		submit.attr('id', unique('submit-'));
	}
	
	CORE.tabs(id, {cookie:false}, {
		next: "next_button",
		previous: "previous_button",
		submit: submit.attr('id'),
		alwaysAllowSubmit: true
	});
	
	// only show child info tab and info if the person being added is actually a child
	if ($('#ProfileBirthDateMonth').length > 0) {
		$('#ProfileBirthDateMonth, #ProfileBirthDateDay, #ProfileBirthDateYear').change(function() {
			$('#childinfo, #childinfo-tab').hide();
			var month, day, year, birthdate, childdate;
			month = Number($('#ProfileBirthDateMonth').val());
			day = Number($('#ProfileBirthDateDay').val());
			year = Number($('#ProfileBirthDateYear').val());
			birthdate = new Date(year, month-1, day);
			childdate = new Date();
			childdate.setFullYear(childdate.getFullYear()-18);
			if (birthdate > childdate) {
				$('#childinfo, #childinfo-tab').show();
			}
		});
		
		$('#ProfileBirthDateMonth').change();
	}
}

/**
 * Adds an additional household member element to the form
 * 
 * The element should be created in the view, passing "COUNT" as the count so
 * that this function can replace it with the current count
 */
CORE_user.addAdditionalMember = function() {
	$("#members").append(CORE_user.element.toString().replace(/COUNT/g, CORE_user.member));
	CORE_user.member++;
}

/**
 * Cancels a household addition
 * 
 * @param int number The member number to cancel
 */
CORE_user.cancelAddMember = function(number) {
	$('#member'+number).remove();
}
