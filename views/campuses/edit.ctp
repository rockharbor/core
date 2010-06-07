<div class="campuses form">
<?php echo $this->Form->create('Campus');?>
	<fieldset>
 		<legend><?php printf(__('Edit %s', true), __('Campus', true)); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>

	<div id="leaders">
	<?php
		// register this div as 'updateable'
		$this->Js->buffer('CORE.register("leaders", "leaders", "'.Router::url(array(
			'controller' => 'campus_leaders',
			'Campus' => $this->data['Campus']['id'],
			'model' => 'Campus'
		)).'")');
		
		$this->Js->buffer('CORE.update("leaders");');		
	?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Campus.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Campus.id'))); ?></li>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Campuses', true)), array('action' => 'index'));?></li>
	</ul>
</div>