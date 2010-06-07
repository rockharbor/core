<div class="publications form">
<?php echo $this->Form->create('Publication');?>
	<fieldset>
 		<legend><?php printf(__('Edit %s', true), __('Publication', true)); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('link');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Publication.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Publication.id'))); ?></li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Publications', true)), array('action' => 'index'));?></li>
	</ul>
</div>