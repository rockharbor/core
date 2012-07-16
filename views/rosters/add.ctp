<h1>Signup</h1>
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

<div class="rosters core-tabs-wizard" id="roster_tabs">

<ul class="tabs">
	<li class="tab"><a href="#members">Choose Members</a></li> 
	<?php if (!empty($involvement['Question'])): ?>
	<li class="tab" id="questions_tab"><a href="#questions">Answer Questions</a></li>
	<?php endif; ?>
	<?php if ($involvement['Involvement']['take_payment']): ?>
	<li class="tab" id="payment_tab"><a href="#payment">Make A Payment</a></li>
	<li id="billing-tab" class="tab"><a href="#billing">Billing Info</a></li>
	<?php endif; ?>
</ul>

	<div id="members">
		<p>Choose everyone you wish to sign up</p>
<?php
		
	echo $this->Form->hidden('Default.involvement_id', array('value' => $involvement['Involvement']['id']));
	
	// CORE.successForm(true, ...) relies on a validation error in the dom, so add one here if there's an issue
	if (!empty($this->validationErrors) && isset($this->validationErrors['Roster']['validation'])) {
		echo '<div class="input error"><div class="error-message">'.$this->validationErrors['Roster']['validation'].'</div></div>';
	}
	
	$i = 0;
	foreach ($householdMembers as $householdMember) {
		echo $this->Form->input('Adult.'.$i.'.Roster.user_id', array(
			'type' => 'checkbox',
			'value' => $householdMember['User']['Profile']['user_id'],
			'label' => $householdMember['User']['Profile']['name']
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
					'label' => $child['Profile']['name']
				));
				
				$c++;
			}
	?>
		</fieldset>
<?php
	}		
?>
	</div>
	<?php if (!empty($involvement['Question'])) {
	?>
	<div id="questions">
		<p>Please answer the questions on behalf of all members you are signing up.</p>
		<div id="question_tabs" class="core-tabs">
			<ul>
			<?php
				foreach ($householdMembers as $householdMember) {
					echo '<li><a href="#answers_'.$householdMember['User']['id'].'">'.$householdMember['User']['Profile']['name'].'</a></li> ';
				}
			?>			
			</ul>
		<?php			
			$r = 0;
			foreach ($householdMembers as $householdMember) {
				echo '<div id="answers_'.$householdMember['User']['id'].'">';
				$q = 0;
				foreach ($involvement['Question'] as $question) {
					echo $this->Form->hidden('Adult.'.$r.'.Answer.'.$q.'.question_id', array(
						'value' => $question['id']
					));
					echo $this->Form->input('Adult.'.$r.'.Answer.'.$q.'.description', array(
						'label' => $question['description'],
						'type' => 'textarea'
					));
					$q++;
				}
				echo '</div>';
				$r++;
			}
		?>
		</div>
	</div>
	<?php } ?>
	<?php if ($involvement['Involvement']['take_payment']) { ?>
	<div id="payment" class="clearfix">
		<h3>Make a Payment</h3>
		<p>This <?php echo $involvement['InvolvementType']['name']; ?> requires a payment to sign up.
		<?php
			if (!$involvement['Involvement']['force_payment']) {
				echo 'You can choose to pay later if you wish. The payment option you select can be changed later.';
			}
		?>
		</p>
		<div>
			<div class="payment-options">
			<?php
			$options = array();
			foreach ($involvementPaymentOptions as $option) {
				$amounts = array();
				$options[$option['PaymentOption']['id']] = $this->Html->tag('span', $option['PaymentOption']['name'], array('class' => 'emphasized'));
				$amounts[] = $this->Formatting->money($option['PaymentOption']['total']).' per person';
				if ($option['PaymentOption']['childcare'] > 0) {
					$amounts[] = $this->Formatting->money($option['PaymentOption']['childcare']).' per child';
				}
				if ($option['PaymentOption']['deposit'] > 0) {
					$amounts[] = $this->Formatting->money($option['PaymentOption']['deposit']).' deposit';
				}
				$deductible = null;
				if ($option['PaymentOption']['tax_deductible']) {
					$deductible = ' | Tax deductible';
				}
				$options[$option['PaymentOption']['id']] .= $this->Html->tag('span', ' | '.implode(', ', $amounts).$deductible, array('class' => 'deemphasized'));
			}
			echo $this->Form->input('Default.payment_option_id', array(
				'type' => 'radio',
				'div' => 'input radio payment-option',
				'options' => $options,
				'value' => key($options),
				'legend' => false,
				'separator' => '</div><div class="input radio payment-option">'
			));
			?>
			</div>
			<div id="payment-details">
				<div id="payment-totals">
					<div id="payment-info" class="clearfix">
						<div class="info-left deemphasized">People x <span id="people-number"></span></div>
						<div class="info-right emphasized">$<span id="people-total"></span></div>
						<?php if ($involvement['Involvement']['offer_childcare']): ?>
						<div class="info-left deemphasized">Children x <span id="children-number"></span></div>
						<div class="info-right emphasized">$<span id="children-total"></span></div>
						<?php endif; ?>
						<div class="info-left deemphasized">Total Due:</div>
						<div class="info-right emphasized">$<span id="total-total"></span></div>
					</div>
					<div id="pay-deposit" class="clearfix">
						<?php
						echo $this->Form->input('Default.pay_deposit_amount', array(
							'type' => 'checkbox',
							'div' => array(
								'id' => 'deposit'
							)
						));
						?>
					</div>
					<div id="todays-payment" class="clearfix">
						<div class="info-left">Today's<br />Payment</div>
						<div class="info-right big">$<span id="amount"></span></div>
					</div>
					<div id="remaining-balance" class="clearfix">
						Remaining Balance <span class="balance">$<span id="balance"></span></span>
					</div>
					
				</div>
				<div id="pay-later">
					<?php
					if (!$involvement['Involvement']['force_payment']) {
						echo $this->Form->input('Default.pay_later', array(
							'type' => 'checkbox',
							'label' => 'I want to pay later'
						));
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<div id="billing" class="clearfix">
		<?php
		$ptypes = Set::combine(Set::extract('/PaymentType[type=0]', $paymentTypes), '{n}.PaymentType.id', '{n}.PaymentType.name');
		if (count($ptypes) > 1) {
			echo $this->Form->input('Default.payment_type_id', array('options' => $ptypes));
		} else {
			echo $this->Form->hidden('Default.payment_type_id', array('value' => current($ptypes)));
		}
		echo $this->element('payment_type'.DS.'credit_card', array(
			'addresses' => $userAddresses
		));
		?>
	</div>
	<?php } ?>
	
<?php
$defaultSubmitOptions['id'] = 'submit_button';
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true})';
echo $this->Form->button('Previous', array('id' => 'previous_button', 'class' => 'button', 'type' => 'button'));
echo $this->Form->button('Next', array('id' => 'next_button', 'class' => 'button', 'type' => 'button'));
echo $this->Js->submit('Sign up', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>

<?php
echo $this->Html->script('misc/roster');
$this->Js->buffer('CORE_roster.addresses = '.$this->Js->object(Set::combine($userAddresses, '/Address/id', '/Address')));
$this->Js->buffer('CORE_roster.payments = '.$this->Js->object(Set::combine($involvementPaymentOptions, '/PaymentOption/id', '/PaymentOption')));
$this->Js->buffer('CORE.tabs("roster_tabs",
	{
		cookie:false
	},
	{
		next: "next_button",
		previous: "previous_button",
		submit: "submit_button",
		alwaysAllowSubmit: false
	}
);');
$this->Js->buffer('CORE_roster.init()');
