/**
 * Namespaced object
 */
var CORE_ministry = {};

/**
 * Sets up the subministries scrolling functionality
 */
CORE_ministry.setup = function() {
	$('.subministries').css({
		height: 110,
		width: 780,
		overflow: 'hidden'
	}).children('.subministry').removeClass('alpha');
	$('.subministries .subministry:first').addClass('alpha');
	$('.subministries').siblings('.pagination').show();
	$('.subministries').wrapInner('<div class="scroll"></div>');
	CORE_ministry.scrollWidth = $('.subministries .subministry:last').outerWidth()+10;
	$('.subministries .scroll').css({
		width: $('.subministries .subministry').length * CORE_ministry.scrollWidth,
		marginLeft: 0
	});
	
	CORE_ministry.maxScroll = $('.subministries .scroll').width() - $('.subministries').width() - 10;
	CORE_ministry.prevButton = $('.subministries').siblings('.pagination').children('.prev-button');
	CORE_ministry.prevButton.html('<span class="core-icon icon-arrow-w"></span>').button().click(CORE_ministry.prev);
	CORE_ministry.nextButton = $('.subministries').siblings('.pagination').children('.next-button');
	CORE_ministry.nextButton.html('<span class="core-icon icon-arrow-e"></span>').button().click(CORE_ministry.next);
	CORE_ministry.prevButton.button('disable');
}

/**
 * Shows the next subministry
 */
CORE_ministry.next = function() {
	if (Number($('.subministries .scroll').css('marginLeft').replace('px', '')) > -CORE_ministry.maxScroll) {
		$('.subministries .scroll').animate({marginLeft: '-='+CORE_ministry.scrollWidth}, 400, function() {CORE_ministry.showHideButtons()});
	}
}

/**
 * Shows the next subministry
 */
CORE_ministry.prev = function() {	
	if (Number($('.subministries .scroll').css('marginLeft').replace('px', '')) < 0) {
		$('.subministries .scroll').animate({marginLeft: '+='+CORE_ministry.scrollWidth}, 400, function() {CORE_ministry.showHideButtons()});
	}
}

/**
 * Shows appropriate buttons according to where we are in the scroll
 */
CORE_ministry.showHideButtons = function() {
	var scroll = Number($('.subministries .scroll').css('marginLeft').replace('px', ''));
	CORE_ministry.prevButton.button('disable');
	CORE_ministry.nextButton.button('disable');
	if (scroll > -CORE_ministry.maxScroll) {
		CORE_ministry.nextButton.button('enable');
	}
	if (scroll < 0) {
		CORE_ministry.prevButton.button('enable');
	}
}