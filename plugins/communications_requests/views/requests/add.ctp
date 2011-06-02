<h1>Make a Communications Request</h1>
<div class="content-box">
	<?php
	echo $this->Form->create(array(
		'default' => false
	));
	
	?>
	<div class="clearfix">
		<fieldset>
			<legend>Request Type</legend>
			<?php
			echo $this->Form->input('request_type_id');
			?>
			<p id="type-description"></p>
		</fieldset>
	</div>
	<div class="clearfix">
		<fieldset class="grid_6 alpha">
			<legend>Request</legend>
			<?php
			echo $this->Form->input('ministry_name');
			echo $this->Form->input('budget', array(
				'between' => '$'
			));
			echo $this->Form->hidden('user_id', array(
				'value' => $activeUser['User']['id']
			))
			?>
		</fieldset>
		<fieldset class="grid_6 omega">
			<p>Please include event name, dates and overview.</p>
			<?php
			echo $this->Form->input('description', array(
				'type' => 'textarea'
			));
			?>
		</fieldset>
	</div>
	<?php
	$defaultSubmitOptions['success'] = 'CORE.showFlash(data);CORE.successForm(event, data, textStatus, {closeModals:true})';
	echo $this->Js->submit('Make Request', $defaultSubmitOptions);
	echo $this->Form->end();
	?>
</div>
<?php
$this->Js->buffer('var requestTypeDescriptions = '.$this->Js->object($requestTypeDescriptions));
$this->Js->buffer('$("#RequestRequestTypeId").change(function() {
	$("#type-description").html(requestTypeDescriptions[$("#RequestRequestTypeId").val()]);
});');
$this->Js->buffer('$("#RequestRequestTypeId").change();');
?>