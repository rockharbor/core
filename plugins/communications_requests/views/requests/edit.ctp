<h1>Edit Communications Requests</h1>
<div class="content-box">
	<?php
	echo $this->Form->create(array(
		'default' => false
	));
	?>
	<fieldset>
		<legend>Request</legend>
		<?php
		echo $this->Form->input('request_status_id');
		?>
	</fieldset>
	<?php
	$defaultSubmitOptions['success'] = 'CORE.showFlash(data);CORE.successForm(event, data, textStatus, {closeModals:true})';
	echo $this->Js->submit('Save', $defaultSubmitOptions);
	echo $this->Form->end();
	?>
</div>