<h1>Add Ministry</h1>
<div class="ministries form">
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
$defaultSubmitOptions['success'] = 'CORE.successForm(event, data, textStatus, {closeModals:true});CORE.showFlash(data);';
echo $this->Js->submit('Submit', $defaultSubmitOptions);
echo $this->Form->end();
?>
</div>
<?php
echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("MinistryDescription");');
?>