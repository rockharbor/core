if (CORE == undefined) {
	throw 'CORE global.js needs to be imported first!';
}

/**
 * Attaches modal behavior.
 *
 * Modal will not open until the link is clicked.
 *
 * ### Extra options:
 * - `update` Boolean. Whether or not to auto update when the modal is closed
 *
 * @param string id The id of the element
 * @param object options Options for creating the Modal Window.
 * @see jQuery UI dialog options
 * @return mixed Modal response
 */
CORE.modal = function(id, options) {
	if ($('.core-modal').length == 0) {
		$('#wrapper').append('<div id="modal" class="container_12 core-modal"></div>');
	}

	var _defaultOptions = {
		modal: true,
		width: 700,
		height: 'auto',
		buttons: {},
		update: true,
		title: 'Loading'
	}

	// use user defined options if defined
	var useOptions = $.extend(_defaultOptions, options || {});

	$('#'+id).click(function(event) {
		// stop link
		event.preventDefault();

		// get options
		var options = $(this).data('core-modal-options');

		// close callback
		options.close = function(event, ui) {
			if ($('#modal').dialog('option', 'update')) {
				var originator = $('#modal').data('core-modal-originator');
				CORE.update(originator);
			}
			$('#modal').empty();
			// stop the request if closed prematurely
			var xhr = $('#modal').data('xhr');
			if (xhr) {
				xhr.onreadystatechange = function() {};
				xhr.abort();
			}
		};

		// open modal
		$('#modal').dialog(options);

		// remember where the modal originated from
		$('#modal').data('core-modal-originator', $('#'+id));

		// load the link into the modal
		$("#modal").parent().position({
			my: 'center',
			at: 'center',
			of: window
		});
		var xhr = $.ajax({
			url: this.href,
			success: function(data, textStatus, xhr) {
				$("#modal").html(data);
				if ($('#modal').height() < $(window).height()-50) {
					$("#modal").parent().position({
						my: 'center',
						at: 'center',
						of: window
					});
				} else {
					$("#modal").parent().css({top: '10px'})
				}
			},
			context: $("#modal")
		});
		$("#modal").data('xhr', xhr);

		return false;
	});

	$('#'+id).data('core-modal-options', useOptions);
}

/**
 * Takes the contents of a div and creates a tooltip on the previous element.
 *
 * Looks for the class `core-tooltip` and removes it from the DOM after adding
 * the tooltip to the previous element.
 *
 * @param ele Element The element to attach it to. By default, it uses the
 *		element previous to the class
 */
CORE.attachTooltipBehavior = function() {
	$('.core-tooltip').each(function() {
		CORE.tooltip($(this).prev(), this)
	});
}

/**
 * Adds a tooltip to an element
 *
 * If `content` is not a string, it will be considered an element and the html
 * content will be pulled as the tooltip content
 *
 * ### Options:
 * - `detachAfter` boolean Remove the content div after creating the tooltip
 * - `container` Element The container. Default is document body
 *
 * @param ele Element The element to turn into a tooltip link
 * @param content mixed The content to use
 * @param options hash List of options
 */
CORE.tooltip = function(ele, content, options) {
	var _default = {
		detachAfter: true,
		container: $(document.body),
		color: '#343434',
		autoShow: false
	}
	var useOptions;
	if (options != undefined) {
		useOptions = $.extend(_default, options);
	} else {
		useOptions = _default;
	}

	var _content = content;
	if (typeof content != 'string') {
		_content = $(content).clone(true).removeClass('core-tooltip').removeAttr('id');
	}

	$(ele).qtip({
		content: {
			text: _content
		},
		position: {
			container: useOptions.container,
			adjust: {
				mouse: false,
				y: -3
			},
			my: "bottom center",
			at: "center",
			target: $(ele),
			viewport: $(window)
		},
		show: {
			delay: 50,
			solo: true,
			ready: useOptions.autoShow
		},
		hide: {
			delay: 50,
			fixed: true
		},
		style: {
			tip: {
				corner: true
			}
		}
	});

	if (useOptions.detachAfter && typeof content != 'string') {
		$(content).detach();
	}
}

/**
 * Attaches tab behavior to appropriate elements
 *
 * Makes everything with the `.core-tabs` class a tabbed list. Pulls attached
 * jQuery data 'cookie' for cookie option
 *
 * @return boolean True
 * @see CORE.tabs()
 */
CORE.attachTabbedBehavior = function() {
	$('.core-tabs').each(function() {
		if ($(this).data('hasTabs') == undefined) {
			var options = {};
			if ($(this).data('cookie') != undefined) {
				options.cookie = $(this).data('cookie');
			}
			if (!$(this).prop('id')) {
				$(this).prop('id', unique('link-'));
			}
			CORE.tabs($(this).prop('id'), options);
			$(this).data('hasTabs', true);
		}
	});

	return true;
}

/**
 * Attaches modal behaviors to appropriate elements
 *
 * Makes everything with a the data attribute `data-core-modal` a modal. If the
 * data attribute is a json object, those options will be passed to `CORE.modal`
 *
 * {{{
 * // when clicked, the link opens in a modal and *does not* update its
 * // originating container on close
 * <a href='/some/link' data-core-modal='{"update":false}'>Link</a>
 * }}}
 *
 * @return boolean True
 */
CORE.attachModalBehavior = function() {
	$('[data-core-modal]').each(function() {
		if (!$(this).prop('id')) {
			$(this).prop('id', unique('link-'));
		}

		// regular ajax call if it's already in a modal
		if ($(this).parents('.ui-dialog').length > 0) {
			$(this).on('click', function () {
				$.ajax({
					dataType: 'html',
					success: function (data) {
						$('#modal').html(data);
					},
					url: $(this).prop('href'),
					context: $('#modal')
				});
				return false;
			});
			return;
		}

		var opts = {};
		if ($(this).data('core-modal') != true) {
			opts = $(this).data('core-modal');
		}

		CORE.modal($(this).prop('id'), opts);
		// don't double attach modals
		$(this).removeAttr('data-core-modal');
	});

	return true;
}

/**
 * Makes everything with a the data attribute `data-core-ajax` an ajax request.
 *
 * {{{
 * // when clicked, the link runs in the background and updates the closest
 * // `data-core-update-url` area
 * <a href='/some/link' data-core-ajax='true'>Link</a>
 * // when clicked, the link runs in the background and doesn't update anything
 * <a href='/some/link' data-core-ajax='{"update":false}'>Link</a>
 * }}}
 *
 * @return boolean True
 */
CORE.attachAjaxBehavior = function() {
	$('[data-core-ajax]').each(function() {
		if (!$(this).prop('id')) {
			$(this).prop('id', unique('link-'));
		}

		var opts = {
			url: $(this).prop('href')
		};
		if ($(this).data('core-ajax') == true) {
			// default options
			opts.update = true;
		}
		$(this).click(function() {
			CORE.request(this, opts);
			return false;
		});

		$(this).removeAttr('data-core-ajax');
	});

	return true;
}

/**
 * Creates tabs from <li> tags
 *
 * ####Options:
 * - `next` string Id of the "next" button in the wizard, if any
 * - `previous` string Id of the "previous" button in the wizard, if any
 * - `submit` string Id of the final submit button in the wizard, if any
 * - `alwaysAllowSubmit` boolean True to always show submit button (default true)
 *
 * @param string id The id of the <ul> container
 * @param object taboptions The options for the tabs ui object
 * @param object options Options for tabs, for treatment as a faux-wizard
 * @return object The tab ui object
 */
CORE.tabs = function(id, taboptions, options) {
	// use user defined options if defined
	var useOptions = {
		ajaxOptions: {
			error: function(XMLHttpRequest) {
				if (XMLHttpRequest.status == '403') {
					redirect('/login');
				}
			}
		},
		select: function(event, ui) {
			// set appropriate xhr context
			$(event.target).tabs('option', 'ajaxOptions', {context: ui.panel});
		},
		load: function(event, ui) {
			var url = $(ui.tab).data('load.tabs');
			$(ui.panel).data('core-update-url', url);
		},
		create: function(event) {
			$(event.target).find('.ui-tabs-nav li a').each(function() {
				var hash = $(this).attr('href');
				$(hash).data('core-update-url', $(this).data('load.tabs'))
			})
		}
	};
	if (taboptions != undefined) {
		useOptions = $.extend(useOptions, taboptions);
	}

	var tabbed = $('#'+id);

	if (tabbed.find('ul li.selected-tab').length) {
		useOptions.selected = tabbed.find('ul li.selected-tab').index();
	}

	tabbed.tabs(useOptions);

	// check to see if this is a "wizard"
	if (options != undefined) {
		tabbed.children('ul').children('li').each(function() {
			$(this).prepend('<span class="start-separator" />');
			$(this).append('<span class="end-separator" />');
		});
		tabbed.children('ul').children('li:last').each(function() {
			$(this).children('.end-separator').addClass('last');
		});
		if (options.next != undefined) {
			// hide next button if it automatically was selected (cookie)
			if (tabbed.tabs('option', 'selected')+1 == tabbed.tabs('length')) {
				$('#'+options.next).hide();
			}

			$('#'+options.next).on('click', function(event, ui) {
				var selected = tabbed.tabs('option', 'selected');
				// select next visible tab
				tabbed.children('ul').children('li:nth-child('+(selected+1)+')').nextAll(':visible').eq(0).children('a').click();
			});
		}

		if (options.previous != undefined) {
			// hide prev button if it automatically was selected (cookie)
			if (tabbed.tabs('option', 'selected') == 0) {
				$('#'+options.previous).hide();
			}

			$('#'+options.previous).on('click', function(event, ui) {
				var selected = tabbed.tabs('option', 'selected');
				// select previous visible tab
				tabbed.children('ul').children('li:nth-child('+(selected+1)+')').prevAll(':visible').eq(0).children('a').click();
			});
		}

		if (options.submit != undefined) {
			if (options.alwaysAllowSubmit == undefined) {
				options.alwaysAllowSubmit = true;
			}

			if (!options.alwaysAllowSubmit && tabbed.tabs('option', 'selected')+1 != tabbed.tabs('length')) {
				$('#'+options.submit).hide();
			}
		}

		// bind all button actions to one select event
		if (options.next != undefined || options.previous != undefined || options.submit != undefined) {
			tabbed.on('tabsselect', function(event, ui) {
				var next = $('#'+options.next);
				var previous = $('#'+options.previous);
				var submit = $('#'+options.submit);
				var selected = ui.index;

				var nextTab = tabbed.children('ul').children('li:nth-child('+(selected+1)+')').nextAll(':visible').eq(0);
				var prevTab = tabbed.children('ul').children('li:nth-child('+(selected+1)+')').prevAll(':visible').eq(0);

				if (nextTab.length == 0) {
					next.hide();
					submit.show();
					previous.show();
				} else {
					next.show();
					if (!options.alwaysAllowSubmit) {
						submit.hide();
					}
					previous.show();

					if (prevTab.length == 0) {
						previous.hide();
					}
				}
			});
		}
	}

	// just in case it was missed
	tabbed.find('.ui-tabs-panel').addClass('clearfix');

	return tabbed;
}


/**
 * Attaches a confirmation dialog behavior
 *
 * #### Options:
 *
 * - `update` Boolean. True to update the originator's `core-update-url`
 *		container with the HTML results of this link after the confirmation is
 *		closed, False to perform a request and auto-update on close.
 * - `onYes` Js function to call on confirmation
 * - `yesTitle` Yes button title, `false` for no button
 * - `onNo` Js function to call on cancellation
 * - `noTitle` No button title, `false` for no button
 *
 * @param string id The id of the element to attach the behavior to
 * @param string message The message to display
 * @param object options Customizable options
 * @return boolean
 */
CORE.confirmation = function(id, message, options) {
	if ($('#confirmation-modal').length == 0) {
		$('#wrapper').append('<div id="confirmation-modal"></div>');
	}
	if (id == undefined || message == undefined) {
		return false;
	}

	var el = $('#'+id);

	// extract controller from url
	var href = el.prop('href');

	var _defaultOptions = {
		update: true,
		yesTitle: 'Yes',
		onNo: false,
		noTitle: 'Cancel',
		onYes: false,
		title: 'Confirmation'
	};

	var useOptions = $.extend(_defaultOptions, options || {});

	if (useOptions.onYes === false) {
		(function(useOptions) {
			useOptions.onYes = function() {
				var callingElement = $('#confirmation-modal').data('core-modal-originator');
				CORE.request($(callingElement), {
					url: href,
					success: function(data) {
						// only update with the request's response if
						// `useOptions.update = true`, otherwise perform auto-update
						if (!useOptions.update) {
							data = undefined;
						}
						CORE.update($(callingElement), data);
						CORE.closeModals('confirmation-modal');
					}
				});
			};
		})(useOptions);
	}

	if (useOptions.onNo === false) {
		useOptions.onNo = function() {
			CORE.closeModals("confirmation-modal");
		}
	}

	el.click(function(event) {
		// stop href
		event.preventDefault();

		var extraButtons = {};
		if (useOptions.yesTitle !== false) {
			extraButtons[useOptions.yesTitle] = useOptions.onYes;
		}
		if (useOptions.noTitle !== false) {
			extraButtons[useOptions.noTitle] = useOptions.onNo;
		}

		$('#confirmation-modal').dialog({
			width: 300,
			buttons: extraButtons,
			title: useOptions.title,
			modal: true
		});
		$('#confirmation-modal').html('<p>'+message+'</p>');
		$('#confirmation-modal').dialog('open');
		$('#confirmation-modal').data('core-modal-originator', el);

		// stop href
		return false;
	});

	return true;
}

/**
 * Attaches WYSIWYG behavior to text area
 *
 * Makes the width 100% of it's parent minus 20px (for scrollbar) if the width
 * isn't explicitly set
 *
 * @param string id The Id of the element to attach it to
 * @return boolean True
 */
CORE.wysiwyg = function(id) {
	var toolbar = $(CORE.wysiwygToolbar);
	var toolbarId = unique('wysihtml5-toolbar-');
	toolbar.wrap('div').prop('id', toolbarId);
	$('#'+id).before(toolbar);

	var editor = new wysihtml5.Editor(id, {
		style: false,
		toolbar: toolbarId,
		parserRules: wysihtml5ParserRules,
		stylesheets: [
			'/css/email.css'
		]
	});

	return true;
}

/**
 * Attaches AutoComplete behavior to text field
 *
 * @param string id The Id of the element to attach it to
 * @param string datasource A url to a json datasource
 * @param function onSelect The JavaScript function to call when an item is selected. Item is passed as the first argument.
 * @return Element Element returned by autocomplete creation
 */
CORE.autoComplete = function(id, datasource, onSelect) {
	return $('#'+id).autocomplete({
		source: function(request, response) {
			$.ajax({
				url: datasource,
				success: function(data) {
					response(data);
				},
				data: $('#'+id).closest('form').serializeArray(),
				type: $('#'+id).closest('form').prop('method'),
				dataType: 'json'
			});
		},
		minLength: 3,
		select: function (event, ui) {
			if (onSelect != undefined) {
				onSelect(ui.item);
				return false;
			}
		}
	}).data('autocomplete')._renderItem = function(ul, item) {
		return $('<li></li>')
			.data('item.autocomplete', item)
			.append($('<a style="display:block" class="clearfix"></a>').html(stripslashes(item.label)))
			.appendTo(ul);
	};
}

/**
 * Creates an ajax-like (since ajax upload is technically impossible) behavior
 * for upload fields. Only works for single file upload forms.
 *
 * @param string id The id of form
 */
CORE.ajaxUpload = function(id) {
	if (!$('#'+id).data('ajax-form-attached')) {
		$('#'+id).data('ajax-form-attached', true);
	} else {
		return;
	}

	var submit = $('#'+id+' div.submit');
	var input = $('#'+id+' input[type=file]').prop('multiple', true);
	var button = $('<button id="'+id+'_button">'+submit.children('input').prop('value')+'</button>').button();
	// attach/remove to dom to get the width so we don't end up with a 0 width form
	$('body').append(button);
	var width = $('#'+id+'_button').width();
	$('body').remove('#'+id+'_button');

	input.before(button);

	var form = $('#'+id).css({
		position: 'relative',
		width: width,
		overflow:'hidden',
		padding:0,
		margin:0,
		textAlign:'center',
		cursor: 'pointer'
	});
	form.prop('action', form.prop('action')+'.json');

	button.css({
		width:'100%',
		cursor: 'pointer'
	});
	submit.hide();
	$('#'+id+'_error').hide();
	input.css({
		opacity:0,
		fontSize:'120px',
		margin:0,
		padding:0,
		position:'absolute',
		top:0,
		right:0,
		cursor: 'pointer'
	});
	input.click(function() { form.each(function() { this.reset() }) } );
	input.mouseenter(function() { button.mouseenter() });
	input.mouseleave(function() { button.mouseleave() });
	input.mouseover(function() { button.qtip('destroy'); button.mouseover() });
	input.mouseout(function() { button.mouseout() });
	input.mousedown(function() { button.mousedown() });
	input.mouseup(function() { button.mouseup() });
	input.change(function() { form.submit() });

	form.ajaxForm({
		//iframe: true,
		complete: function(data) {
			if (data.error !== undefined) {
				form.before('<div class="error-message" id="'+id+'_error"></div>');
				CORE.confirmation(id+'_error', 'Upload failed. Please try again.', {
					yesTitle: false,
					noTitle: 'Try Again',
					title: 'Whoops',
					onNo: function() {
						CORE.closeModals("confirmation-modal");
						$('#'+id+'_error').remove();
					}
				});
				$('#'+id+'_error').click();
			}
		},
		success: function(response) {
			form.before('<div class="error-message" id="'+id+'_error"></div>');
			var e = $('#'+id+'_error');
			var msg = '';
			var name = input.prop('name');
			var model = name.substring(name.indexOf('[')+1, name.indexOf(']'));
			if (!response[model]) {
				CORE.update($('#'+id));
			} else if (response == null) {
				msg = "Unknown error."
				e.text(msg);
				e.addClass("error-message upload-error");
			} else {
				msg = response[model]['file'];
				CORE.confirmation(id+'_error', msg, {
					yesTitle: false,
					noTitle: 'Try Again',
					title: 'Upload failed',
					onNo: function() {
						CORE.closeModals("confirmation-modal");
						$('#'+id+'_error').remove();
					}
				});
				$('#'+id+'_error').click();
			}
		},
		dataType: 'json'
	});
}


/**
 * Closes all modals and popups
 *
 * @param modalName string The name of the modal. If empty, all will close
 */
CORE.closeModals = function(modalName) {
	if (modalName != undefined) {
		$('#'+modalName).dialog('close');
	} else {
		$('#modal').dialog('close');
		$('#content').dialog('close');
	}
}

/** Fix for jQuery bug 4671
 *
 * @link http://bugs.jqueryui.com/ticket/4671
 * @link https://github.com/ksenzee/views3ui/blob/bdf8d279d0a78b6a921e446fc7448fabffa3322d/js/jquery.ui.dialog.patch.js
 */
if ($.ui && $.ui.dialog) {
	$.ui.dialog.overlay.events = $.map('focus,keydown,keypress'.split(','), function(event) { return event + '.dialog-overlay'; }).join(' ');
}