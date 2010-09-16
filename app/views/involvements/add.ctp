<h2>Create Involvement Opportunity</h2>
<div id="involvement_tabs" class="involvements">

<p>Create a new involvement opportunity! You will be able to add dates, a location, and media after you've created the involvement opportunity. The involvement opportunity will remain inactive until you activate it.</p>

<?php echo $this->Form->create('Involvement');?>
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
		echo $this->Form->input('description');
	?>
		<fieldset>
			<legend>Roster</legend>
	<?php		
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
echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("InvolvementDescription");');


$this->Js->buffer('$("#InvolvementTakePayment").bind("change", function() {
	if (this.checked) {
		if ($(this).parent().css("display") != "none") {
			$("#InvolvementForcePayment").parent().show();
		} else {
			$("#InvolvementForcePayment").parent().hide();
		}
		$("#payment_options_tab").show();
	} else {
		$("#InvolvementForcePayment").parent().hide();
		$("#payment_options_tab").hide();
	}
});');
$this->Js->buffer('$("#InvolvementTakePayment").change();');
$this->Js->buffer('$("#InvolvementSignup").bind("change", function() {
	if (this.checked) {
		$("#InvolvementRosterVisible").parent().show();
		$("#InvolvementTakePayment").parent().show();
		$("#InvolvementRosterLimit").parent().show();
		$("#InvolvementOfferChildcare").parent().show();
		$("#questions_tab").show();
	} else {
		$("#InvolvementRosterVisible").parent().hide();
		$("#InvolvementTakePayment").parent().hide();
		$("#InvolvementRosterLimit").parent().hide();
		$("#InvolvementOfferChildcare").parent().hide();
		$("#questions_tab").hide();
	}
	$("#InvolvementTakePayment").change();
});');
$this->Js->buffer('$("#InvolvementTakePayment").change();');
$this->Js->buffer('$("#InvolvementSignup").change();');
?>