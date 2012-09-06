if (CORE == undefined) {
	throw 'CORE global.js needs to be imported first!';
}

/**
 * Initiates login functions
 */
CORE.initLogin = function() {
	CORE.loadSillyPhrase();
}

/**
 * Loads a phrase to show above the logo
 */
CORE.loadSillyPhrase = function() {
	var models = ['Involvement', 'Ministry'];
	var model = models[Math.floor(Math.random()*models.length)];
	$.ajax({
		url: '/pages/phrase/'+model+'.json',
		success: function(data) {
			CORE.showSillyPhrase(data.phrase);
			setTimeout('CORE.hideSillyPhrase()', 7000);
		},
		dataType: 'json'
	})
}

/**
 * Shows a phrase above the logo
 *
 * @param text string The phrase
 */
CORE.showSillyPhrase = function(text) {
	CORE.tooltip($('#logo img'), text, {
		autoShow:true
	});
}

/**
 * Hides the currently displayed phrase
 */
CORE.hideSillyPhrase = function() {
	var api = $('#logo img').qtip('api');
	$('.qtip').fadeOut(function() {
		api.destroy();
	});
	setTimeout('CORE.loadSillyPhrase()', 3000);
}