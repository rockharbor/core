if (CORE == undefined) {
	throw 'CORE global.js needs to be imported first!';
}

/**
* Shows validation errors
*
* Use the form parameter if you have more than one model with more than
* one form per page, and just wish to show errors on that form.
*
* @param string form The id of the form to search. If none, it searches all
* @return boolean True for passing validation, false for not
*/
CORE.showValidationErrors = function(form) {
	// get tabs
	if (form == undefined) {
		form = 'content';
	}
	var form = $('#'+form);
	form.find('.ui-tabs')
		.children()
		.each(function() {
			// check if this tab panel has an error
			if ($(this).find('div.error').length > 0) {
				var id = $(this).prop('id');
				$(this).siblings('.ui-tabs-nav')
					.find('a[href*="'+id+'"], a[title*="'+id+'"]').each(function() {
						if ($(this).children().length == 0) {
							$(this).prepend('<span class="core-icon icon-error"></span>');
						}
					});
			}
		});

	return $('.form-error, div.error', form).length == 0;
}

/**
* Handles any setup before submitting a form
*
* @param object event The event for the click
* @param object XMLHttpRequest The XMLHttpRequest
* @return boolean If the request will continue
*/
CORE.beforeForm = function(event, XMLHttpRequest) {	
	// stop the request if this button has been clicked
	if ($(event.originalTarget).data('disabled')) {
		XMLHttpRequest.abort();
		return false;
	}
	
	CORE.setLoading($(event.currentTarget).closest('[data-core-update-url]'));
	
	XMLHttpRequest.async = false;
	
	$('div.error-message').each(function(i) {$(this).fadeOut()});
	
	$('.tabs a').each(function(i) {$(this).removeClass('error');});
	
	$(event.originalTarget).addClass('loading');
	$(event.originalTarget).data('disabled', true);

	return true;
}

/**
* Handles actions after a form has been submitted
* 
* @param object event The event for the click
* @param object XMLHttpRequest The XMLHttpRequest
* @param object textStatus The textStatus
*/
CORE.completeForm = function(event, XMLHttpRequest, textStatus) {
	// scroll to top
	$('html, body').animate({scrollTop:0}, 'slow');
	
	// allow submit to be clicked again
	$(event.originalTarget).data('disabled', false);
	$(event.originalTarget).removeClass('loading');
}

/**
* Handles actions after successful form submission
*
* #### Options:
*		- function success Callback for a successful (validated) form (Default none)
*		- function failure Callback for an unsuccessful form (Default none)
*		- boolean autoUpdate Whether to update the content (Default true)
*		- boolean closeModals Whether to close the modal (Default false)
*		- boolean showFlash Whether or not to show flash message if modal is closed
*
* @param object event The event for the click
* @param object data The returned data
* @param object textStatus The ajax options
* @param object options Success options
*/
CORE.successForm = function(event, data, textStatus, options) {
	var _defaultOptions = {
		success: false,
		failure: false,
		autoUpdate: true,
		closeModals: false,
		showFlash: false
	};
	
	options = $.extend(_defaultOptions, options);

	// check to see if it validates if content depends on it
	$('#wrapper').append('<div id="temp"></div>');
	$('#temp').hide().html(data);
	var validates = CORE.showValidationErrors('temp');
	$('#temp').remove();

	if (!$(event.currentTarget).closest('form').prop('id')) {
		$(event.currentTarget).closest('form').prop('id', unique('form-'));
	}
	
	var id = $(event.currentTarget).closest('form').prop('id');
	
	// update the content
	switch (options.autoUpdate) {
		case 'failure':		
			if (!validates) {
				CORE.update(event.currentTarget, data)
				CORE.showValidationErrors(id);
			}
		break;
		case 'success':
			if (validates) {
				CORE.update(event.currentTarget, data)
				CORE.showValidationErrors(id);
			}
		break;
		default:
			CORE.update(event.currentTarget, data)
			CORE.showValidationErrors(id);
		break;
	}
	
	if (validates) {
		if (options.success != false) {
			options.success();
		} 
		if (options.closeModals) {
			CORE.closeModals();
			if (options.showFlash) {
				CORE.showFlash(data);
			}
		}
	} else {
		if (options.failure != false) {
			options.failure();
		}
	}
}

/**
* Only allows one checkbox per value to be checked.
*
* This will iterate through all radio and checkboxes and automatically
* set `change` events to detect if the checkbox/radio is checked 
* and disable similar checkboxes (useful when selecting multiple users
* when they may appear more than once on screen)
* 
* @param string fieldset ID of the fieldset to 
*/
CORE.noDuplicateCheckboxes = function(fieldset) {
	$('input:checkbox, input:radio', $('#'+fieldset)).each(function() {
		$(this).on('change', function() {
			var matching = $('input[value='+this.value+']:checkbox, input[value='+this.value+']:radio', $('#'+fieldset));
			if (this.checked) {
				matching.prop('disabled', true).prop('checked', false).parent('.core-checkbox, .core-radio').addClass('disabled').removeClass('selected');
				$(this).prop('disabled', false).prop('checked', true).parent('.core-checkbox, .core-radio').removeClass('disabled');
			} else {
				matching.prop('disabled', false).parent('.core-checkbox, .core-radio').removeClass('disabled');
			}			
		});
	});
}

/**
* Initializes special form elements
*/
CORE.initFormUI = function() {
	// create buttons on proper elements
	$('button, input:submit, a.button, span.button').button();
	$('button.disabled, input:submit.disabled, a.button.disabled, span.button.disabled').button({disabled:true}).removeAttr('href');
	$('input.toggle').button();
	$('span.toggle input, div.toggle input').button();
	$('.toggleset').buttonset();

	// plain checkboxes and radios are a little more complicated
	$('input:checkbox:not(.ui-helper-hidden-accessible, .core-checkbox-hidden)').each(function() {
		$(this).addClass('core-checkbox-hidden');
		$(this).wrap(function() {
			var disabled = $(this).prop('disabled') ? ' disabled': '';
			var selected = $(this).prop('checked') ? ' selected': '';
			return '<span class="core-checkbox'+selected+disabled+'" />';
		});
	}).change(function () {
		this.checked ?	$(this).parent().addClass('selected') : $(this).parent().removeClass('selected');
	});
	$('input:radio:not(.ui-helper-hidden-accessible, .core-radio-hidden)').each(function() {
		$(this).addClass('core-radio-hidden');
		$(this).wrap(function() {
			var disabled = $(this).prop('disabled') ? ' disabled': '';
			var selected = $(this).prop('checked') ? ' selected': '';
			return '<span class="core-radio'+selected+disabled+'" />';
		});
	}).change(function () {
		$('.core-radio input[name="'+$(this).prop('name')+'"]').each(function() { $(this).parent().removeClass('selected') });
		this.checked ?	$(this).parent().addClass('selected') : $(this).parent().removeClass('selected');
	});

	// show/hide default text (like type=search in html5)
	var showhide = function(event) {
		if (event.type == 'focus') { $(this).siblings('label').hide();	return; }
		$(this).val() == '' ? $(this).siblings('label').show() : $(this).siblings('label').hide();
	};
	$('.input.text.showhide label, .input.textarea.showhide label, .input.password.showhide label')
		.css({
			position: 'absolute',
			cursor: 'text'
		})
		.siblings('input,textarea')
			.focus(showhide)
			.blur(showhide)
			.change(showhide)
			.blur()
		.siblings('label').each(function() {
			var offsetPx = $(this).siblings('input, textarea').outerHeight() - $(this).outerHeight();
			offsetPx /= 2;
			$(this).css({
				top: offsetPx,
				left: offsetPx
			});
		});
			

	// set up filter forms
	$('.core-filter-form').each(function() {
		if (!$(this).prop('id')) {
			$(this).prop('id', unique('form-'));
		}
		if ($(this).data('configured') == true) {
			return;
		}
		$(this).data('configured', true);
		$('input:submit', this).hide();
		var form = $(this);
		$('input, select', form).change(function() {
			CORE.request(form, {
				url: form.prop('action'),
				update: true,
				data: form.serialize()
			});
		});
	});

	$('.form-error').one('focus', function() { $(this).removeClass('.form-error'); });
	
	// search forms
	$('.search-form input[type="text"]').each(function() {
		var id = $(this).prop('id');
		CORE.autoComplete(id, $(this).closest('form').prop('action')+'.json', function(item) {
			redirect(item.action);
		});
		$(this)
			.data('defaultSearchText', $(this).val())
			.focus(function() {
				var self = $(this);
				if (self.val() == self.data('defaultSearchText')) {
					self.val('');
					self.prop('class', 'search-over');
				}
			})
			.blur(function() {
				var self = $(this);
				if (self.val() == '') {
					self.val(self.data('defaultSearchText'));
					self.prop('class', 'search-out');
				}
			});
	});
}