<h1>Edit Campus</h1>
<div class="campuses form content-box">
<?php
if (!empty($revision)) {
	$changes = array_diff_assoc($revision, $this->data['Campus']);
}

if ($revision && !empty($changes)): ?>
<div id="change" class="message change">
	There is a pending change for this campus
	<?php
	echo $this->Permission->link('History', array('action' => 'history','Campus' => $this->data['Campus']['id']),array('rel' => 'modal-content', 'class' => 'button')
	);
	?>
</div>
<?php endif; ?>
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