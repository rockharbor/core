<div class="leaders form">
<?php echo $this->Form->create('Leader');?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Leader', true)); ?></legend>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('model');
		echo $this->Form->input('model_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Leaders', true)), array('action' => 'index'));?></li>
	</ul>
</div>