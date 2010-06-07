<h2>Signup</h2>

<?php
// get kids in their households
$children = Set::extract('/HouseholdMember/Household/HouseholdMember/User/Profile[child=1]', $user);
// all household members that he can sign up
$householdMembers = Set::extract('/HouseholdMember/Household/HouseholdMember/User', $user);

// remove duplicates (because of users belonging to more than one household)
$inlist = array();
foreach ($householdMembers as $key => $values) {	
	if (in_array($values['User']['id'], $inlist)) {
		unset($householdMembers[$key]);
	} else {
		$inlist[] = $values['User']['id'];
	}	
}
// remove children duplicates
$inlist = array();
foreach ($children as $key => $values) {	
	if (in_array($values['Profile']['user_id'], $inlist)) {
		unset($children[$key]);
	} else {
		$inlist[] = $values['Profile']['user_id'];
	}	
}

if (empty($householdMembers)) {
	$this->Html->para('Everyone is already signed up.');
	return;
}

echo $this->Form->create('Roster', array(
	'default' => false,
	'inputDefaults' => array(),
	'url' => array('User' => $user['User']['id'], 'Involvement' => $involvement['Involvement']['id'])
));



?>

<div class="rosters" id="roster_tabs">

<ul class="tabs">
	<li class="tab"><a href="#members">Choose Members</a></li> 
	<?php if (!empty($involvement['Question'])): ?><li class="tab" id="questions_tab"><a href="#questions">Answer Questions</a></li><?php endif; ?>
	<?php if ($involvement['Involvement']['take_payment']): ?><li class="tab" id="payment_tab"><a href="#payment">Make A Payment</a></li><?php endif; ?>
	<li class="tab"><a href="#options">Signup Options</a></li>
</ul>

	<fieldset id="members">
		<p>Choose everyone you wish to sign up</p>
<?php
		
	echo $this->Form->hidden('Default.involvement_id', array('value' => $involvement['Involvement']['id']));
	
	// CORE.successForm(true, ...) relies on a validation error in the dom, so add one here if there's an issue
	if (!empty($this->validationErrors)) {
		echo '<div class="input" style="display:none"><div class="error-message"></div></div>';
	}
	
	$i = 0;
	foreach ($householdMembers as $householdMember) {
		echo $this->Form->input('Roster.'.$i.'.Roster.user_id', array(
			'type' => 'checkbox',
			'value' => $householdMember['User']['Profile']['user_id'],
			'label' => $householdMember['User']['Profile']['name'],
			'hiddenField' => false
		));
				
		$i++;
	}

	// if this involvement offers childcare and this is the user we're signing up (i.e., the household contact)
	if ($involvement['Involvement']['offer_childcare'] && !empty($children)) {
?>
		<fieldset>
			<legend>Childcare Signups</legend>
			<p>This <?php echo $involvement['InvolvementType']['name']; ?> offers childcare. Select children below to sign up for childcare.</p>
	<?php 
			$c = 0;
			foreach ($children as $child) {
				echo $this->Form->input('Child.'.$c.'.Roster.user_id', array(
					'type' => 'checkbox',
					'value' => $child['Profile']['user_id'],
					'label' => $child['Profile']['name'],
					'hiddenField' => false
				));
				
				$this->Js->buffer('$("#Child'.$c.'RosterUserId").bind("change", updateAmount);');
				
				$c++;
			}
	?>
		</fieldset>
<?php
	}		
?>
	</fieldset>
	<?php if (!empty($involvement['Question'])) {
	?>
	<fieldset id="questions">
		<div id="question_tabs">	
			<ul class="tabs">
			<?php
				foreach ($householdMembers as $householdMember) {
					echo '<li class="tab"><a href="#answers_'.$householdMember['User']['id'].'">'.$householdMember['User']['Profile']['name'].'</a></li> ';
				}
			?>			
			</ul>
		<?php			
			$r = 0;
			foreach ($householdMembers as $householdMember) {
				echo '<div id="answers_'.$householdMember['User']['id'].'">';
				$q = 0;
				foreach ($involvement['Question'] as $question) {
					echo $this->Form->hidden('Roster.'.$r.'.Answer.'.$q.'.question_id', array(
						'value' => $question['id']
					));
					echo $this->Form->input('Roster.'.$r.'.Answer.'.$q.'.description', array(
						'label' => $question['description']
					));
					$q++;
				}
				echo '</div>';
				$r++;
			}
		?>
		</div>
	</fieldset>
	<?php } ?>
	<?php if ($involvement['Involvement']['take_payment']) {
	?>
	<fieldset id="payment">
	<?php
		if ($involvement['Involvement']['force_payment']) {
			echo '<p>This '.$involvement['InvolvementType']['name'].' requires a payment before signing up.</p>'; 
		} else {
			echo '<p>This '.$involvement['InvolvementType']['name'].' requires a payment to sign up. You can choose to pay later if you wish. The payment option you select can be changed later.</p>';
			echo $this->Form->input('Default.pay_later', array(
				'type' => 'checkbox',
				'label' => 'I want to pay later'
			));
			$this->Js->buffer('$("#DefaultPayLater").bind("change", function() {
				if (this.checked) {
					$("#sh_payment").hide();
					$("#billing_info input, #billing_info select").attr("disabled", "disabled");
					$("#credit_card_info input, #credit_card_info select").attr("disabled", "disabled");
				} else {
					$("#sh_payment").show();
					$("#billing_info input, #billing_info select").removeAttr("disabled");
					$("#credit_card_info input, #credit_card_info select").removeAttr("disabled");
					updateAmount();
				}
			});');
		}  

		echo $this->Form->input('Default.payment_option_id');
		
	?>
	
	<div id="sh_payment">
	<div id="payment_info"></div>
	<div id="tax_deductible">This <?php echo $involvement['InvolvementType']['name']; ?> is tax deductible! Isn't that great?</div>
	Your credit card will be charged: <span id="amount"></span>.
	<?php
		echo $this->Form->input('PaymentOption.pay_deposit_amount', array(
			'type' => 'checkbox',
			'div' => array(
				'id' => 'deposit'
			)
		));
	?>
	
	<?php 
	echo $this->element('payment_type'.DS.'credit_card', array(
		'addresses' => $userAddresses
	));
	?>
	</div>
	</fieldset>
	<?php } ?>
	<fieldset id="options">
	<?php		
		echo $this->Form->input('Default.role_id', array(
			'empty' => 'Member'
		));		
	?>
	</fieldset>
	
<?php
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Js->submit('Sign up', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<?php
$this->Js->buffer('addresses = '.$this->Js->object(Set::combine($userAddresses, '/Address/id', '/Address')));
$this->Js->buffer('payments = '.$this->Js->object(Set::combine($involvementPaymentOptions, '/PaymentOption/id', '/PaymentOption')));
$this->Js->buffer('$("#DefaultAddressId").bind("change", function() {
	if ($(this).val() == 0) {
		$("#AddressAddressLine1").val("");
		$("#AddressAddressLine2").val("");
		$("#AddressCity").val("");
		$("#AddressState").val("");
		$("#AddressZip").val("");
	} else {
		selected = addresses[$(this).val()].Address;
		$("#AddressAddressLine1").val(selected.address_line_1);
		$("#AddressAddressLine2").val(selected.address_line_2);
		$("#AddressCity").val(selected.city);
		$("#AddressState").val(selected.state);
		$("#AddressZip").val(selected.zip); 
	}
});');

$this->Js->buffer('function updateAmount() {	
	if (payments[$("#DefaultPaymentOptionId").val()] == undefined) {
		return;
	}
	
	amount = 0;
	text = "";

	selected = payments[$("#DefaultPaymentOptionId").val()].PaymentOption;
	
	selected.deposit > 0 ? $("#deposit").show() : $("#deposit").hide();
	selected.tax_deductible == 1 ? $("#tax_deductible").show() : $("#tax_deductible").hide();
	
	if (selected.deposit > 0 && $("#PaymentOptionPayDepositAmount").attr("checked")) {
		amount = Number(selected.deposit)*$("input[id^=Roster]:checked").length;
	} else {
		amount = Number(selected.total)*$("input[id^=Roster]:checked").length;
	}
	
	if (selected.childcare > 0) {
		// get number of children checked
		childcare = $("input[id^=Child]:checked").length;
		amount += childcare*Number(selected.childcare);
	}
	
	text += "$"+selected.total+" per signup";
	if (selected.deposit > 0) {
		text += " / $"+selected.deposit+" deposit";
	}
	if (selected.childcare > 0) {
		text += " / $"+selected.childcare+" per childcare signup";
	}
	
	$("#payment_info").text(text);
	
	$("#amount").text("$"+amount);
}');


$this->Js->buffer('CORE.noDuplicateCheckboxes("members");');
$this->Js->buffer('$("#members input[id^=Roster][type=checkbox]").bind("change", function() {
	// only show the answer tabs for members that are checked
	if (this.checked) {
		$("a[href=#answers_"+$(this).val()+"]").parent().show();
	} else {
		$("a[href=#answers_"+$(this).val()+"]").parent().hide();
	}
	$("#members input[id^=Roster]:checked").length > 0 ? $("#questions_tab").show() : $("#questions_tab").hide();
	updateAmount();
});');
$this->Js->buffer('$("#members input[type=checkbox]").change();');
$this->Js->buffer('$("#PaymentOptionPayDepositAmount").bind("change", updateAmount);');
$this->Js->buffer('$("#DefaultPaymentOptionId").change();');
$this->Js->buffer('$("#RosterAddressId").change();');

$this->Js->buffer('CORE.tabs("roster_tabs", {cookie:false});');
$this->Js->buffer('CORE.tabs("payment_tabs", {cookie:false});');
$this->Js->buffer('CORE.tabs("question_tabs", {cookie:false});');

// by default, click the first available tab under answers
$this->Js->buffer('$("#questions_tab a").bind("click", function() {
	$("#questions ul.tabs li:visible:first a").click()
});');
?>