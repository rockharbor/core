<h1>Add Ministry</h1>
<div class="ministries form content-box">
<?php echo $this->Form->create('Ministry', array(
	'default' => false
));?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Ministry', true)); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description', array(
			 'type' => 'textarea'
		));
		if (isset($parentId)) {
			echo $this->Form->hidden('parent_id', array('value' => $parentId));
		}
		echo $this->Form->hidden('campus_id');
		echo $this->Form->input('private');
	?>
	</fieldset>
<?php
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
<?php
$this->Js->buffer('CORE.wysiwyg("MinistryDescription");');
