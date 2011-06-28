<h1>Edit Campus</h1>
<div class="campuses form">
<?php 
echo $this->Form->create('Campus', array(
	'default' => false,
	'url' => $this->here
));
?>
	<fieldset>
 		<legend><?php printf(__('Edit %s', true), __('Campus', true)); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php 
echo $this->Js->submit(__('Submit', true), $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>