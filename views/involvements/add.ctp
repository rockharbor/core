<h1>Create Involvement Opportunity</h1>
<div id="involvement_tabs" class="involvements content-box">

<p>Create a new involvement opportunity! You will be able to add dates, a location, and media after you've created the involvement opportunity. The involvement opportunity will remain inactive until you activate it.</p>

<?php echo $this->Form->create('Involvement', array(
	'url' => $this->here
));?>
	<fieldset>
 		<legend>Create Involvement Opportunity</legend>
	<?php
		if (isset($this->passedArgs['Ministry'])) {
			echo $this->Form->hidden('ministry_id', array('value' => $this->passedArgs['Ministry']));
		} else {
			echo $this->Form->input('ministry_id');
		}
		echo $this->Form->input('involvement_type_id');
		echo $this->Form->input('name');
		echo $this->Form->input('description', array(
			'type' => 'textarea'
		));
	?>
		<fieldset>
			<legend>Roster</legend>
	<?php		
		echo $this->Form->input('default_status_id');
		echo $this->Form->input('signup', array(
			'label' => 'Offer signup'
		));
		echo $this->Form->input('roster_visible');
		echo $this->Form->input('take_payment');
		echo $this->Form->input('force_payment', array(
			'label' => 'Force user to pay upon signup'
		));
		echo $this->Form->input('roster_limit', array(
			'label' => 'Roster Limit (leave blank for unlimited)'
		));
		
		echo $this->Form->input('offer_childcare');
	?>
		</fieldset>
	</fieldset>
<?php 
echo $this->Form->end('Submit');
?>
</div>
<?php
$this->Js->buffer('CORE.wysiwyg("InvolvementDescription");');

$this->Js->buffer('$("#InvolvementTakePayment").bind("change", function() {
	if ($(this).is(":checked")) {
		if ($(this).parent().parent().css("display") != "none") {
			$("#InvolvementForcePayment").closest("div.checkbox").show();
		} else {
			$("#InvolvementForcePayment").closest("div.checkbox").hide();
		}
	} else {
		$("#InvolvementForcePayment").closest("div.checkbox").hide();
	}
});');
$this->Js->buffer('$("#InvolvementTakePayment").change();');
$this->Js->buffer('$("#InvolvementSignup").bind("change", function() {
	if ($(this).is(":checked")) {
		$("#InvolvementRosterVisible").closest("div.checkbox").show();
		$("#InvolvementTakePayment").closest("div.checkbox").show();
		$("#InvolvementRosterLimit").closest("div.checkbox").show();
		$("#InvolvementOfferChildcare").closest("div.checkbox").show();
		$("#questions_tab").show();
	} else {
		$("#InvolvementRosterVisible").closest("div.checkbox").hide();
		$("#InvolvementTakePayment").closest("div.checkbox").hide();
		$("#InvolvementRosterLimit").closest("div.checkbox").hide();
		$("#InvolvementOfferChildcare").closest("div.checkbox").hide();
		$("#questions_tab").hide();
	}
	$("#InvolvementTakePayment").change();
});');
$this->Js->buffer('$("#InvolvementTakePayment").change();');
$this->Js->buffer('$("#InvolvementSignup").change();');
