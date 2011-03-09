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
			$('#'+div).load(CORE.updateables[updateable][div]);
		}
	}
}

/**
 * Wraps Ajax request
 *
 * ### Options:
 * - `update` An updateable to update on success
 * - `updateHtml` A div to update with the returned contents on success
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
	
	if (useOptions.update !== undefined) {
		var update = useOptions.update;
		useOptions.success = function() {
			CORE.update(update);
		}		
		delete useOptions.update;
	}

	if (useOptions.updateHtml !== undefined) {
		var update = useOptions.updateHtml;
		useOptions.success = function(data) {
			$('#'+update).html(data);
		}
		delete useOptions.updateHtml;
	}

	if (data != undefined) {
		useOptions.type = 'post';
		useOptions.data = data;
	}
	return $.ajax(useOptions);
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

/**
 * Unregisters an updateable and returns the updateable that was removed
 *
 * @param alias string The alias for the updateable
 * @return hash The updateable that was removed
 * @see CORE.register
 */
CORE.unregister = function(alias) {
	if (alias == undefined) {
		return false;
	}
	var old = CORE.updateables[alias];
	delete CORE.updateables[alias];
	return old;
}

/**
 * Registers the alias with the `content` updateable's data, so if there's a
 * call to an undefined alias it will load it in content's div instead. Useful
 * for pages that may or may not be loaded in ajax windows.
 *
 * @param alias string The alias to check for
 * @return void;
 */
CORE.fallbackRegister = function(alias) {
	if (CORE.updateables[alias] == undefined) {
		CORE.updateables[alias] = CORE.updateables['content'];
	}
}

/**
 * Inits CORE js
 */
CORE.init = function() {	
	// init ui elements
	CORE.initUI();
	// init navigation
	CORE.initNavigation();
}

/**
 * These are all items that should be initialized on a new page or when a modal
 * opens
 */
CORE.initUI = function() {
	$('.equal-height:visible > div').equalHeights();
	// hide flash message
	$('div[id^=flash]').hide().delay(100).slideDown().delay(5000).slideUp();
	// attach tabbed behavior
	CORE.attachTabbedBehavior();
	// attach modal behavior
	CORE.attachModalBehavior();
	// tooltips
	CORE.attachTooltipBehavior();
	// form elements
	CORE.initFormUI();
}

CORE.register('content', 'content', location.href);