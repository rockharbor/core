if (CORE == undefined) {
	throw 'CORE global.js needs to be imported first!';
} 

/**
 * Attaches modal behavior. 
 *
 * Modal will not open until the link is clicked.
 *
 * @param string id The id of the element
 * @param object options Options for creating the Modal Window.
 * @see jQuery UI dialog options
 * @return mixed Modal response
 */
CORE.modal = function(id, options) {
	if ($('#modal').length == 0) {
		$('#wrapper').append('<div id="modal" class="container_12"></div>');
	}

	var _defaultOptions = {
		modal: true,
		width: 700,
		autoOpen: false,
		height: 'auto'
	}
	
	// use user defined options if defined
	var useOptions;
	if (options != undefined) {
		useOptions = $.extend(_defaultOptions, options);
	} else {
		useOptions = _defaultOptions;
	}

	useOptions.close = function(event, ui) {
		// rewrite the id's
		$('#content').attr('id', 'modal');		
		$('#content-reserved').attr('id', 'content');
		// re-register original content updateable
		CORE.unregister('content');
		CORE.updateables['content'] = CORE._tmpcontent;
		delete CORE._tmpcontent;
		if ($('#modal').dialog('option', 'update') != undefined) {
			CORE.update($('#modal').dialog('option', 'update'));
		}
		$('#modal').empty();
	};
	
	useOptions.open = function(event, ui) {
		// rename content so ajax updates will update content in modal
		$('#content').attr('id', 'content-reserved');
		$('#modal').attr('id', 'content');
		// register this new ajax url as the content updateable, so scripts within
		// the window that update the content updateable update with this url instead
		CORE._tmpcontent = CORE.unregister('content');
		CORE.register('content', 'content', $('#'+id).attr('href'));
	}
	
	$('#'+id).click(function(event) {
		// set to update what this links says to
		$('#modal').dialog('option', 'update', $(this).data('update'))
		
		// remove old settings (from confirmation, other modals, etc
		$('#modal').dialog('option', 'buttons', {});
		$('#modal').dialog('option', 'width', 700);
		$('#modal').dialog('option', 'height', 'auto');
		
		// set options
		var modalOptions = $(this).data('modalOptions');
		for (var o in modalOptions) {
			$('#modal').dialog('option', modalOptions, modalOptions[o]);
		}
		
		// stop link
		event.preventDefault();
		
		// load the link into the modal
		$('#modal').dialog('open');
		$('#content').dialog('option', 'title', 'Loading');
		$("#content").parent().position({
			my: 'center',
			at: 'center',
			of: window
		});
		$('#content').load(this.href, function() {
			$("#content").parent().position({
				my: 'center',
				at: 'center',
				of: window
			});
		});
		
		// stop href
		return false;
	});
	
	$('#'+id).data('modalOptions', useOptions);
	if (useOptions.update == 'parent') {
		var parent = CORE.getUpdateableParent(id);
		useOptions.update =  parent.updateable;
	}
	$('#'+id).data('update', useOptions.update);
	$('#modal').dialog({autoOpen:false});
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
		autoShow: false,
		contentClass: 'qtip-content'
	}
	var useOptions;
	if (options != undefined) {
		useOptions = $.extend(_default, options);
	} else {
		useOptions = _default;
	}

	var _content = content;
	if (typeof content != 'string') {
		_content = $(content).clone(true).removeClass('core-tooltip').attr('id', '');
	}

	$(ele).qtip({
		content: {
			text: _content
		},
		position: {
			corner: {
				target: 'topMiddle',
				tooltip: 'bottomLeft'
			},
			container: useOptions.container,
			type: $(useOptions.container).is('body') ? 'absolute' : 'fixed',
			adjust: {
				screen: true,
				scroll: true,
				mouse: false,
				y: 5
			}
		},
		show: {
			delay: 50,
			solo: true,
			ready: useOptions.autoShow
		},
		hide: {
			fixed: true
		},
		style: {
			width: {
				max: 170
			},
			tip: {
				corner: 'bottomLeft',
				color: useOptions.color
			},
			classes: {
				content: useOptions.contentClass
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
 * Makes everything with a `rel` property "tabs" a tabbed list. Pulls attached
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
			if ($(this).attr('id') == '') {
				$(this).attr('id', unique('link-'));
			}
			CORE.tabs($(this).attr('id'), options);
			$(this).data('hasTabs', true);
		}
	});

	return true;
}

/**
 * Attaches modal behaviors to appropriate elements
 *
 * Makes everything with a `rel` property "modal-X" a modal
 * where the X is the to-be-updated registered updateable (optional)
 *
 * @return boolean True
 */
CORE.attachModalBehavior = function() {
	$('[rel|=modal]').each(function() {
		if ($(this).attr('id') == '') {
			$(this).attr('id', unique('link-'));
		}

		// regular ajax call if it's already in a modal
		if ($(this).parents('.ui-dialog').length > 0) {
			$(this).bind('click', function () {
				$.ajax({
					dataType: 'html',
					success: function (data) {
						$('#content').html(data);
					},
					url: $(this).attr('href')
				});
				return false;
			});
			return;
		}

		if ($(this).data('hasModal') == undefined) {	
			// get updateable, if any
			var rel = $(this).attr("rel");
			var update = rel.split("-");

			if (update[1] != undefined) {
				CORE.modal($(this).attr('id'), {update:update[1]});
			} else {
				CORE.modal($(this).attr('id'));
			}
			
			$(this).data('hasModal', true);			
		}
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
	var useOptions = {};
	if (taboptions != undefined) {
		useOptions = $.extend(useOptions, taboptions);
	}
	
	var tabbed = $('#'+id);
	tabbed.tabs(useOptions);

	// pull admin tabs out
	tabbed.append('<ul class="admin ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"></ul>');
	$('.ui-tabs-nav li.admin').appendTo($('ul.admin'));
	
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
			
			$('#'+options.next).bind('click', function(event, ui) {
				var btn = $(this);
				var length = tabbed.tabs('length');
				var selected = tabbed.tabs('option', 'selected');
				if (selected+1 < length) {
					tabbed.tabs('select', selected+1);
				}
			});
		}
		
		if (options.previous != undefined) {
			// hide prev button if it automatically was selected (cookie)
			if (tabbed.tabs('option', 'selected') == 0) {
				$('#'+options.previous).hide();
			}
			
			$('#'+options.previous).bind('click', function(event, ui) {
				var btn = $(this);
				var length = tabbed.tabs('length');
				var selected = tabbed.tabs('option', 'selected');
				
				if (selected > 0) {
					tabbed.tabs('select', selected-1);
				}
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
			tabbed.bind('tabsselect', function(event, ui) {
				var next = $('#'+options.next);
				var previous = $('#'+options.previous);
				var submit = $('#'+options.submit);
				var length = tabbed.tabs('length');
				var selected = ui.index;
				if (selected+1 == length) {
					next.hide();
					submit.show();
					previous.show();
				} else {				
					next.show();
					if (!options.alwaysAllowSubmit) {
						submit.hide();
					}
					previous.show();
					
					if (selected == 0) {
						previous.hide();
					}
				}
			});
		}
	}
	
	return tabbed;
}


/**
 * Attaches a confirmation dialog behavior
 *
 * #### Options:
 *
 * - `update` An "updateable" to automatically update on close
 * - `updateHtml` If a string, replaces this id element with the returned content.
 *		`update` takes priority over this option
 * - `onYes` Js function to call on confirmation, in addition to calling the href
 * - `yesTitle` Yes button title
 * - `onNo` Js function to call on cancellation
 * - `noTitle` No button title
 *
 * @param string id The id of the element to attach the behavior to
 * @param string message The message to display
 * @param object options Customizable options
 * @return boolean
 * @see CORE.updateables
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
	var href = el.attr('href');
	
	var _defaultOptions = {
		update: '',
		updateHtml: false,
		yesTitle: 'Yes',
		onNo: 'CORE.closeModals("confirmation-modal");',
		noTitle: 'Cancel',
		onYes: '',
		title: 'Confirmation'
	};
		
	var useOptions;
	if (options != undefined) {
		useOptions = $.extend(_defaultOptions, options);
	} else {
		useOptions = _defaultOptions;
	}

	if (useOptions.update == 'parent') {
		var parent = CORE.getUpdateableParent(id);
		useOptions.update = parent.updateable;
	}
	if (useOptions.updateHtml == 'parent') {
		var parent = CORE.getUpdateableParent(id);
		useOptions.updateHtml = parent.id;
	}

	if (useOptions.update != '') {
		useOptions.onYes = 'CORE.request("'+href+'", {update:"'+useOptions.update+'"});CORE.closeModals("confirmation-modal");'+useOptions.onYes;
	} else if (useOptions.updateHtml !== false) {
		useOptions.onYes = 'CORE.request("'+href+'", {updateHtml:"'+useOptions.updateHtml+'"});CORE.closeModals("confirmation-modal");'+useOptions.onYes
	} else {
		useOptions.onYes = 'CORE.request("'+href+'");CORE.closeModals("confirmation-modal");'+useOptions.onYes
	}

	el.click(function(event) {
		// stop href
		event.preventDefault();
		
		var extraButtons = {};
		if (useOptions.yesTitle !== false) {
			extraButtons[useOptions.yesTitle] = function () {eval(useOptions.onYes)};
		}
		if (useOptions.noTitle !== false) {
			extraButtons[useOptions.noTitle] = function () {eval(useOptions.onNo)};
		}

		$('#confirmation-modal').dialog({
			width: 300,
			buttons: extraButtons,
			title: useOptions.title,
			modal: true
		});
		$('#confirmation-modal').html(message);
		$('#confirmation-modal').dialog('open');
		
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
	delayWysiwyg = function() {
		if ($('#'+id).css('width') != 'auto') {
			var parent = $('#'+id).closest('fieldset');
			if (parent.length == 0) {
				parent = $('#'+id).closest('form');
			}
			$('#'+id).width(parent.width() - 20);
		}
		$('#'+id).wysiwyg({
			rmUnusedControls: true,
			controls: {
				html: { visible : true, groupIndex: 0 },
				strikeThrough: { visible : true, groupIndex: 1 },
				underline: { visible : true, groupIndex: 1 },
				bold: { visible : true, groupIndex: 1 },
				italic: { visible : true, groupIndex: 1 },
				undo: { visible : true, groupIndex: 2 },
				redo: { visible : true, groupIndex: 2 },
				insertOrderedList: { visible : true, groupIndex: 3 },
				insertUnorderedList: { visible : true, groupIndex: 3 }
			},
			initialContent: '',
			css: '/css/jquery.wysiwyg.iframe.css'
		});
		// fix some display issues
		var width = $('div.wysiwyg').width();
		$('div.wysiwyg ul.panel').css({width: (width-8)});
		$('div.wysiwyg iframe').css({width: '100% !important'});
	}
	
	// delay applying the wysiwyg for a bit
	// in case of ajax, tabs, etc.
	setTimeout('delayWysiwyg()', 10);

	return true;	
}

/**
 * Attaches AutoComplete behavior to text field
 *
 * The jQuery menu widget was monkey patched to support using a `div` tag as the
 * item element instead of an `a`. This allows us to include other links inside
 * the auto complete menu items.
 *
 * @param string id The Id of the element to attach it to
 * @param string datasource A url to a json datasource
 * @param function onSelect The JavaScript function to call when an item is selected. Item is passed as the first argument.
 * @return Element Element returned by autocomplete creation
 */
CORE.autoComplete = function(id, datasource, onSelect) {
	$.ui.menu.prototype._create = function() {
		var self = this;
		this.element
			.addClass("ui-menu ui-widget ui-widget-content ui-corner-all")
			.attr({
				role: "listbox",
				"aria-activedescendant": "ui-active-menuitem"
			})
			.click(function( event ) {
				self.select( event );
			});
		this.element.find('a')
			.click(function(event) {
				event.stopPropagation();
			});
		this.refresh();
	}

	$.ui.menu.prototype.refresh = function() {
		var self = this;

		// don't refresh list items that are already adapted
		var items = this.element.children("li:not(.ui-menu-item):has(div)")
			.addClass("ui-menu-item")
			.attr("role", "menuitem");

		items.children("div")
			.addClass("ui-corner-all")
			.attr("tabindex", -1)
			// mouseenter doesn't work with event delegation
			.mouseenter(function( event ) {
				self.activate( event, $(this).parent() );
			})
			.mouseleave(function() {
				self.deactivate();
			});
		CORE.attachModalBehavior();
	};

	$.ui.menu.prototype.activate = function(event, item) {
		this.deactivate();
		if (this.hasScroll()) {
			var offset = item.offset().top - this.element.offset().top,
				scroll = this.element.attr("scrollTop"),
				elementHeight = this.element.height();
			if (offset < 0) {
				this.element.attr("scrollTop", scroll + offset);
			} else if (offset >= elementHeight) {
				this.element.attr("scrollTop", scroll + offset - elementHeight + item.height());
			}
		}
		this.active = item.eq(0)
			.children("div")
				.addClass("ui-state-hover")
				.attr("id", "ui-active-menuitem")
			.end();
		this._trigger("focus", event, { item: item });
	};

	$.ui.menu.prototype.deactivate = function() {
		if (!this.active) { return; }

		this.active.children("div")
			.removeClass("ui-state-hover")
			.removeAttr("id");
		this._trigger("blur");
		this.active = null;
	};

	return $('#'+id).autocomplete({
		source: function(request, response) {
			$.ajax({
				url: datasource,
				success: function(data) {
					response(data);
				},
				data: $('#'+id).closest('form').serializeArray(),
				type: 'post',
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
			.append($('<div></div>').html(stripslashes(item.label)))
			.appendTo(ul);
	};
}

/**
 * Creates an ajax-like (since ajax upload is technically impossible) behavior
 * for upload fields. Only works for single file upload forms.
 *
 * @param string id The id of form
 * @param string updateable An updateable to update after success
 */
CORE.ajaxUpload = function(id, updateable) {
	var submit = $('#'+id+' div.submit');
	var input = $('#'+id+' input[type=file]').attr('multiple', 'multiple');
	var button = $('<button id="'+id+'_button">'+submit.children('input').attr('value')+'</button>').button();
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
	form.attr('action', form.attr('action')+'.json');

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
				CORE.confirmation(id+'_error', 'An unknown error occurred during upload.', {
					yesTitle: false,
					noTitle: 'Try Again',
					title: 'Whoops',
					onNo: 'CORE.closeModals("confirmation-modal");$("#'+id+'_error").remove();'
				});
				$('#'+id+'_error').click();
			}
		},
		success: function(response) {
			form.before('<div class="error-message" id="'+id+'_error"></div>');
			var e = $('#'+id+'_error');
			var msg = '';
			if (response.length == 0) {
				if (updateable != undefined) {
					CORE.update(updateable);
				}
			} else if (response == null) {
				msg = "Unknown error."
				e.text(msg);
				e.addClass("error-message upload-error");
			} else {
				var model = input.attr('name').substring(input.attr('name').indexOf('[')+1, input.attr('name').indexOf(']'));
				msg = response[model]['file'];
				CORE.confirmation(id+'_error', msg, {
					yesTitle: false,
					noTitle: 'Try Again',
					title: 'Whoops',
					onNo: 'CORE.closeModals("confirmation-modal");$("#'+id+'_error").remove();'
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