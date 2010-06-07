<h2>Edit <?php echo $involvementTypes[$this->data['Involvement']['involvement_type_id']]; ?></h2>

<?php
if (!empty($revision)) {
	$changes = array_diff_assoc($revision, $this->data['Involvement']);
}

if ($revision && !empty($changes)): ?>
<div id="change" class="message change">
	<?php 
	echo $this->Html->link('There is a pending change for this '.$involvementTypes[$this->data['Involvement']['involvement_type_id']].'.', 
		array(
			'action' => 'history',
			'Involvement' => $this->data['Involvement']['id']
		),
		array(
			'rel' => 'modal-content'
		)
	);
	?>
</div>
<?php endif; ?>

<div id="involvement_tabs" class="involvements form">

<ul class="tabs">
	<li class="tab"><a href="#details"><?php echo $involvementTypes[$this->data['Involvement']['involvement_type_id']]; ?> Details</a></li> 
	<li class="tab"><a href="#media">Media</a></li>
	<li class="tab"><?php echo $this->Html->link('Dates', 
	array(
		'controller' => 'dates',
		'Involvement' => $this->data['Involvement']['id'],
		'model' => 'Involvement'
	), 
	array(
		'title' => 'dates'
	)); ?></li>
	<li class="tab" id="questions_tab"><?php echo $this->Html->link('Questions', 
	array(
		'controller' => 'questions',
		'Involvement' => $this->data['Involvement']['id']
	), 
	array(
		'title' => 'questions'
	)); ?></li> 
	<li class="tab" id="payment_options_tab"><?php echo $this->Html->link('Payment Options', 
	array(
		'controller' => 'payment_options',
		'Involvement' => $this->data['Involvement']['id']
	), 
	array(
		'title' => 'payment_options'
	)); ?></li> 
	<li class="tab"><?php echo $this->Html->link('Leaders', 
	array(
		'controller' => 'involvement_leaders',
		'Involvement' => $this->data['Involvement']['id']
	), 
	array(
		'title' => 'leaders'
	)); ?></li> 
	<li class="tab"><?php echo $this->Html->link('Roster', 
	array(
		'controller' => 'rosters',
		'Involvement' => $this->data['Involvement']['id']
	), 
	array(
		'title' => 'roster'
	)); ?></li>
</ul>

<?php echo $this->Form->create('Involvement', array(
	'url' => array(
		'Involvement' => $this->data['Involvement']['id']
	)
));?>

	<fieldset id="details">
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('ministry_id');
		echo $this->Form->input('involvement_type_id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
	?>
		<fieldset>
			<legend>Location</legend>
			<div id="addresses">
			<?php
				// register this fieldset as 'updateable'
				$this->Js->buffer('CORE.register(\'addresses\', \'addresses\', \''.Router::url(array(
				'controller' => 'involvement_addresses',
				'Involvement' => $this->data['Involvement']['id']
				)).'\')');	
				
				$this->Js->buffer('CORE.update("addresses");');
			?>
			</div>
		</fieldset>
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
		echo $this->Form->hidden('active');
	?>
		</fieldset>
	<?php 
		echo $this->Form->input('group_id', array(
			'label' => 'Private for everyone below:',
			'empty' => true
		));
	?>
	</fieldset>
	
	<div id="media">
		<div id="image"></div>
		<div id="image_upload">
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register(\'ImageAttachments\', \'image_upload\', \''.Router::url(array(
				'controller' => 'involvement_images',
				'action' => 'index',
				'Involvement' => $this->data['Involvement']['id']
			)).'\')');
			// and tell it to update image as well
			$this->Js->buffer('CORE.register(\'ImageAttachments\', \'image\', \''.Router::url(array(
				'controller' => 'involvement_images',
				'action' => 'view',
				'Involvement' => $this->data['Involvement']['id'],
				0, // just pull the first image
				'l'
			)).'\')');

			$this->Js->buffer('CORE.update(\'ImageAttachments\');');
		?></div>
		<div id="documents_upload">
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register(\'DocumentAttachments\', \'documents_upload\', \''.Router::url(array(
				'controller' => 'involvement_documents',
				'action' => 'index',
				'Involvement' => $this->data['Involvement']['id']
			)).'\')');
			$this->Js->buffer('CORE.update(\'DocumentAttachments\');');
		?></div>
	</div>
	
	<fieldset id="dates">
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register("dates", "dates", "'.Router::url(array(
				'controller' => 'dates',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');	
		?>
	</fieldset>
	
	<fieldset id="questions">		
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register("questions", "questions", "'.Router::url(array(
				'controller' => 'questions',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');	
		?>
	</fieldset>
	
	<fieldset id="payment_options">		
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register("paymentOptions", "payment_options", "'.Router::url(array(
				'controller' => 'payment_options',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');	
		?>
	</fieldset>
	
	<div id="leaders">
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register("leaders", "leaders", "'.Router::url(array(
				'controller' => 'involvement_leaders',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');	
		?>
	</div>
	
	<div id="roster">
		<?php
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register("roster", "roster", "'.Router::url(array(
				'controller' => 'rosters',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');	
		?>
	</div>
	
<?php echo $this->Form->end(__('Submit', true));?>
</div>

<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php 
			$type = $involvementTypes[$this->data['Involvement']['involvement_type_id']];
			if ($this->data['Involvement']['active']) {
				echo $this->Html->link('Disable '.$type, array('action' => 'toggle_activity', 'Involvement' => $this->data['Involvement']['id'], 0, 1), array('id' => 'disable_btn'));
				$this->Js->buffer('CORE.confirmation("disable_btn", "Are you sure you want to disable this '.$type.'?", {onYes:"redirect(\''.$this->here.'\');"});');
			} else {
				echo $this->Html->link('Enable '.$type, array('action' => 'toggle_activity', 'Involvement' => $this->data['Involvement']['id'], 1, 1), array('id' => 'enable_btn'));
				$this->Js->buffer('CORE.confirmation("enable_btn", "Are you sure you want to enable this '.$type.'?", {onYes:"redirect(\''.$this->here.'\');"});');
			}
		?></li>			
	</ul>
</div>

<?php

$this->Js->buffer('CORE.wysiwyg("InvolvementDescription");');
$this->Js->buffer('CORE.tabs("involvement_tabs");');

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