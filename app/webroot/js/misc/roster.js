/*
 * Namespaced object
 */
var CORE_roster = {};

/*
 * Initializes the roster sign up page
 */
CORE_roster.init = function() {
	CORE.noDuplicateCheckboxes('members');
	$('#members input[type=checkbox]').change();
	$('#PaymentOptionPayDepositAmount').bind('change', CORE_roster.updateAmount);
	$('#DefaultPaymentOptionId').change();
	$('#RosterAddressId').change();
	// by default, click the first available tab under answers
	$('#questions_tab a').bind('click', function() {
		$('#questions ul.tabs li:visible:first a').click()
	});
	$('input[id^=Child]').bind('change', CORE_roster.updateAmount);
	$('#DefaultPayLater').bind('change', function() {
		if (this.checked) {
			$('#sh_payment').hide();
			$('#billing_info input, #billing_info select').attr('disabled', 'disabled');
			$('#credit_card_info input, #credit_card_info select').attr('disabled', 'disabled');
		} else {
			$('#sh_payment').show();
			$('#billing_info input, #billing_info select').removeAttr('disabled');
			$('#credit_card_info input, #credit_card_info select').removeAttr('disabled');
		}
		CORE_roster.updateAmount();
	});

	$('#DefaultAddressId').bind('change', function() {
		if ($(this).val() == 0) {
			$('#AddressAddressLine1').val('');
			$('#AddressAddressLine2').val('');
			$('#AddressCity').val('');
			$('#AddressState').val('');
			$('#AddressZip').val('');
		} else {
			var selected = CORE_roster.addresses[$(this).val()].Address;
			$('#AddressAddressLine1').val(selected.address_line_1);
			$('#AddressAddressLine2').val(selected.address_line_2);
			$('#AddressCity').val(selected.city);
			$('#AddressState').val(selected.state);
			$('#AddressZip').val(selected.zip);
		}
	});

	$('#members input[id^=Roster]:checkbox').bind('change', function() {
		// only show the answer tabs for members that are checked
		if (this.checked) {
			$('a[href=#answers_'+$(this).val()+']').parent().show();
		} else {
			$('a[href=#answers_'+$(this).val()+']').parent().hide();
		}
		//$('#members input[id^=Roster]:checked').length > 0 ? $('#questions_tab').show() : $('#questions_tab').hide();
		CORE_roster.updateAmount();
	});

	$('.payment-option input:radio').bind('change', function() {
		$('.payment-option').removeClass('selected');
		if ($(this).is(':checked')) {
			$(this).parent().parent().addClass('selected');
		}
		CORE_roster.updateAmount();
	});

	$('#members input[id^=Roster]:checkbox').change();
	$('.payment-option input:radio:checked').change().parent().addClass('selected');
}

/**
 * Updates amount fields based on selected options
 */
CORE_roster.updateAmount = function() {
	if (CORE_roster.payments[$('.payment-options input:radio:checked').val()] == undefined) {
		return;
	}

	var peopleAmount, totalDue, childcareAmount, numberChildcareSignedUp, payToday, deposit, childcare, numberSignedUp, balance, payLater = 0;
	var selectedOption = CORE_roster.payments[$('.payment-options input:radio:checked').val()].PaymentOption;

	deposit = selectedOption.deposit > 0;
	childcare = selectedOption.deposit > 0;
	payLater = $('#DefaultPayLater').is(':checked');

	deposit > 0 ? $('#pay-deposit').show() : $('#pay-deposit').hide();
	selectedOption.tax_deductible == 1 ? $('#tax-deductible').show() : $('#tax-deductible').hide();

	numberSignedUp = $('input[id^=Roster]:checked').length;
	peopleAmount = Number(selectedOption.total);
	totalDue = peopleAmount*numberSignedUp;
	if (deposit && $('#PaymentOptionPayDepositAmount').is(':checked')) {
		totalDue = Number(selectedOption.deposit)*numberSignedUp;
	}
	$('#people-number').html(numberSignedUp);
	$('#people-total').html(peopleAmount*numberSignedUp);

	if (childcare) {
		// get number of children checked
		numberChildcareSignedUp = $('input[id^=Child]:checked').length;
		childcareAmount = Number(selectedOption.childcare);
		$('#children-number').html(numberChildcareSignedUp);
		$('#children-total').html(numberChildcareSignedUp*childcareAmount);
		totalDue += numberChildcareSignedUp*childcareAmount;
	}

	if (payLater) {
		totalDue = 0;
	}
	
	$('#total-total').html(numberChildcareSignedUp*childcareAmount + peopleAmount*numberSignedUp);
	$('#balance').html($('#total-total').html() - totalDue);
	$('#amount').html(totalDue);
}
