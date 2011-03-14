<span class="breadcrumb editing"><?php
$icon = $this->element('icon', array('icon' => 'delete'));
echo $this->Html->link($icon, array('action' => 'view'), array('escape' => false, 'class' => 'no-hover'));
?>Editing</span>
<h1>Edit <?php echo $involvementTypes[$this->data['Involvement']['involvement_type_id']]; ?></h1>
<div class="core-tabs">
	<ul>
		<li><a href="#details"><?php echo $involvementTypes[$this->data['Involvement']['involvement_type_id']]; ?> Details</a></li>
		<li><a href="#media">Media</a></li>
		<li><?php echo $this->Html->link('Dates',
		array(
			'controller' => 'dates',
			'Involvement' => $this->data['Involvement']['id'],
			'model' => 'Involvement'
		),
		array(
			'title' => 'dates'
		)); ?></li>
		<li id="questions_tab"><?php echo $this->Html->link('Questions',
		array(
			'controller' => 'questions',
			'Involvement' => $this->data['Involvement']['id']
		),
		array(
			'title' => 'questions'
		)); ?></li>
		<li id="payment_options_tab"><?php echo $this->Html->link('Payment Options',
		array(
			'controller' => 'payment_options',
			'Involvement' => $this->data['Involvement']['id']
		),
		array(
			'title' => 'payment_options'
		)); ?></li>
		<li><?php echo $this->Html->link('Leaders',
		array(
			'controller' => 'involvement_leaders',
			'Involvement' => $this->data['Involvement']['id']
		),
		array(
			'title' => 'leaders'
		)); ?></li>
	</ul>
	<div class="content-box clearfix">
		<div id="details" class="clearfix">
			<?php echo $this->Form->create('Involvement', array(
				'url' => $this->passedArgs
			));?>
			<div class="clearfix">
				<fieldset class="grid_5 alpha">
					<legend>Details</legend>
				<?php
					echo $this->Form->input('id');
					echo $this->Form->input('ministry_id');
					echo $this->Form->input('DisplayMinistry');
					echo $this->Form->input('involvement_type_id');
					echo $this->Form->input('name');
					echo $this->Form->input('description');
				?>
				</fieldset>
				<fieldset class="grid_5 omega">
					<legend>Location</legend>
					<div id="addresses">
					<?php
					echo $this->requestAction('/involvement_addresses/index', array(
						'renderAs' => 'ajax',
						'bare' => false,
						'return',
						'named' => array(
							'Involvement' => $this->data['Involvement']['id']
						),
						'data' => null,
						'form' => array('data' => null)
					));
					$this->Js->buffer('CORE.register(\'addresses\', \'addresses\', \''.Router::url(array(
					'controller' => 'involvement_addresses',
					'Involvement' => $this->data['Involvement']['id']
					)).'\')');
					?>
					</div>
				</fieldset>
			</div>
			<div class="clearfix">
				<fieldset class="grid_5 alpha">
					<legend>Roster</legend>
					<?php
						echo $this->Form->input('signup', array(
							'label' => 'Offer signup'
						));
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
				<fieldset class="grid_5 omega">
					<legend>Privacy</legend>
					<?php
						echo $this->Form->hidden('active');
						echo $this->Form->input('roster_visible');
						echo $this->Form->input('private');

					?>
				</fieldset>
			</div>
			<?php echo $this->Form->end(__('Submit', true));?>
			<ul class="core-admin-tabs">
				<?php
				$type = $involvementTypes[$this->data['Involvement']['involvement_type_id']];
				if ($this->data['Involvement']['active']) {
					$link = $this->Html->link('Disable '.$type, array('action' => 'toggle_activity', 'Involvement' => $this->data['Involvement']['id'], 0, 1), array('id' => 'disable_btn'));
					$this->Js->buffer('CORE.confirmation("disable_btn", "Are you sure you want to disable this '.$type.'?", {updateHtml:"content"});');
				} else {
					$link = $this->Html->link('Enable '.$type, array('action' => 'toggle_activity', 'Involvement' => $this->data['Involvement']['id'], 1, 1), array('id' => 'enable_btn'));
					$this->Js->buffer('CORE.confirmation("enable_btn", "Are you sure you want to enable this '.$type.'?", {updateHtml:"content"});');
				}
				if ($link) {
					echo $this->Html->tag('li', $link);
				}
				?>
			</ul>
		</div>

		<div id="media">
			<div id="image"></div>
			<div id="image_upload">
			<?php
				echo $this->requestAction('/involvement_images/index', array(
					'renderAs' => 'ajax',
					'bare' => false,
					'return',
					'named' => array(
						'Involvement' => $this->data['Involvement']['id']
					),
					'data' => null,
					'form' => array('data' => null)
				));
				$this->Js->buffer('CORE.register(\'ImageAttachments\', \'image_upload\', \''.Router::url(array(
					'controller' => 'involvement_images',
					'action' => 'index',
					'Involvement' => $this->data['Involvement']['id']
				)).'\')');
			?></div>
			<div id="documents_upload">
			<?php
				echo $this->requestAction('/involvement_documents/index', array(
					'renderAs' => 'ajax',
					'bare' => false,
					'return',
					'named' => array(
						'Involvement' => $this->data['Involvement']['id']
					),
					'data' => null,
					'form' => array('data' => null)
				));
				$this->Js->buffer('CORE.register(\'DocumentAttachments\', \'documents_upload\', \''.Router::url(array(
					'controller' => 'involvement_documents',
					'action' => 'index',
					'Involvement' => $this->data['Involvement']['id']
				)).'\')');
			?></div>
		</div>

		<div id="dates">
			<?php
			echo $this->requestAction('/dates/index', array(
				'renderAs' => 'ajax',
				'bare' => false,
				'return',
				'named' => array(
					'Involvement' => $this->data['Involvement']['id']
				),
				'data' => null,
				'form' => array('data' => null)
			));
			$this->Js->buffer('CORE.register("dates", "dates", "'.Router::url(array(
				'controller' => 'dates',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');
			?>
		</div>

		<div id="questions">
			<?php
			echo $this->requestAction('/questions/index', array(
				'renderAs' => 'ajax',
				'bare' => false,
				'return',
				'named' => array(
					'Involvement' => $this->data['Involvement']['id']
				),
				'data' => null,
				'form' => array('data' => null)
			));
			$this->Js->buffer('CORE.register("questions", "questions", "'.Router::url(array(
				'controller' => 'questions',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');
			?>
		</div>

		<div id="payment_options">
			<?php
			echo $this->requestAction('/payment_options/index', array(
				'renderAs' => 'ajax',
				'bare' => false,
				'return',
				'named' => array(
					'Involvement' => $this->data['Involvement']['id']
				),
				'data' => null,
				'form' => array('data' => null)
			));
			$this->Js->buffer('CORE.register("paymentOptions", "payment_options", "'.Router::url(array(
				'controller' => 'payment_options',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');
			?>
		</div>

		<div id="leaders">
			<?php
			echo $this->requestAction('/involvement_leaders/index', array(
				'renderAs' => 'ajax',
				'bare' => false,
				'return',
				'named' => array(
					'Involvement' => $this->data['Involvement']['id']
				),
				'data' => null,
				'form' => array('data' => null)
			));
			// register this div as 'updateable'
			$this->Js->buffer('CORE.register("leaders", "leaders", "'.Router::url(array(
				'controller' => 'involvement_leaders',
				'Involvement' => $this->data['Involvement']['id']
			)).'")');
			?>
		</div>
	</div>
</div>

<?php

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