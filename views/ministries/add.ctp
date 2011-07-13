<div class="ministries form">
<?php echo $this->Form->create('Ministry');?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Ministry', true)); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description', array(
			 'type' => 'textarea'
		));
		if (isset($parentId)) {
			echo $this->Form->hidden('parent_id', array('value' => $parentId));
		} else {
			echo $this->Form->input('parent_id', array(
				'type' => 'select',
				'options' => $ministries,
				'escape' => false, // for &nbsp;'s
				'empty' => true,
				'label' => 'Parent Ministry'
			));
		}
		echo $this->Form->hidden('campus_id');
		echo $this->Form->input('private');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<?php
echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg("MinistryDescription");');
?>