/**
 * Define console object so browsers without it won't generate errors
 *
 * @var object
 */
if (console == undefined) {
	var console = {
		log: function() { }
	};
}

/**
 * Define namespace
 *
 * @var object
 */
if (CORE == undefined) {
	var CORE = {};
}

/**
 * Registered updateable divs
 *
 * @var array Array containing an alias object, which contains a div and url
 */
CORE.updateables = [];

/**
 * Wraps Ajax update to use an "updateable" alias
 *
 * If updateable and url are defined, the function will call a regular $.ajax update.
 * Otherwise, it will look for and update a CORE.updateable
 *
 * @param string updateable Either the name of an updateable alias or a div to update.
 * @param string url The url to open if it's a div to update.
 * @see CORE.updateables
 */
CORE.update = function(updateable, url) {
	if (updateable != undefined && url != undefined) {
		$('#'+updateable).load(url);
		return;
	}
	
	if (CORE.updateables[updateable] == undefined && !(updateable == 'none' || updateable == '')) {
		updateable = 'content';
	}
	
	if (updateable != 'none' && updateable != '') {
		// check to see if it's an "updateable"
		for (div in CORE.updateables[updateable]) {
			//console.log(updateable+': updating '+div+' with '+CORE.updateables[updateable][div]); 
			$('#'+div).load(CORE.updateables[updateable][div]);
		}
	}
}

/**
 * Wraps Ajax request
 *
 * @param string url The url to request
 * @param object options Ajax options
 * @param object data Data to POST
 * @return object The Ajax object
 */
CORE.request = function(url, options, data) {
	// use user defined options if defined
	var useOptions = {
		url: url
	};
	
	if (options != undefined) {
		useOptions = $.extend(useOptions, options);
	}
	
	if (useOptions.update) {
		useOptions.complete = function() {
			CORE.update(useOptions.update);
		}
		
		delete useOptions.update;
	}

	if (data != undefined) {
		useOptions.type = 'post';
		useOptions.data = data;
	}
		
	return $.ajax(useOptions);
}

/**
 * Convenience wrapper function for redirecting
 *
 * @param string url The url to redirect to
 */
function redirect(url) {
	window.location.href = url;
}

/**
 * Registers a div as an "updateable"
 *
 * By registering a div as an updateable and linking it to a url,
 * we can call CORE.update(alias) to quickly update that div
 * without having to remember the url or div id. An alias can 
 * also update more than one div (pass the same alias to CORE.register
 * with a new div and url).
 *
 * @param string alias The quick reference alias name
 * @param string div The element div to update
 * @param string url The url to load
 * @return boolean
 * @see CORE.updateables
 */
CORE.register = function(alias, div, url) {
	if (alias == undefined || div == undefined || url == undefined) {
		return false;
	}
	
	if (CORE.updateables[alias] == undefined) {
		CORE.updateables[alias] = [];
	}
	
	// if this exact one exists, don't duplicate
	CORE.updateables[alias][div] = url;
	
	return true;
}

// finally, register content as a global "updateable"
CORE.register('content', 'content', location.href);