<h1>Add a Campus</h1>
<div class="campuses form">
<?php 
echo $this->Form->create('Campus', array(
	'default' => false
));
?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Campus', true)); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php 
echo $this->Js->submit(__('Submit', true), $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>