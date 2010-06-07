<div class="ministries form">
<?php echo $this->Form->create('Ministry');?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Ministry', true)); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('parent_id', array(
			'type' => 'select',
			'options' => $ministries,
			'escape' => false, // for &nbsp;'s
			'empty' => true,
			'label' => 'Parent Ministry'
		));
		echo $this->Form->input('campus_id');
		echo $this->Form->input('group_id', array(
			'label' => 'Private for everyone below:'
		)); 
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Ministries', true)), array('action' => 'index'));?></li>
	</ul>
</div>

<?php
echo $this->Html->script('jquery.plugins/jquery.wysiwyg');
echo $this->Html->css('jquery.wysiwyg');
$this->Js->buffer('CORE.wysiwyg(\'MinistryDescription\');');
?>