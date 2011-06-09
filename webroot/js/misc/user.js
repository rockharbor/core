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
}

/**
 * Adds an additional household member element to the form
 * 
 * The element should be created in the view, passing "COUNT" as the count so
 * that this function can replace it with the current count
 */
CORE_user.addAdditionalMember = function() {
	$("#members").append(CORE_user.element.replace(/COUNT/g, CORE_user.member));
	CORE_user.member++;
}