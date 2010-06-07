<div class="publications form">
<?php echo $this->Form->create('Publication');?>
	<fieldset>
 		<legend><?php printf(__('Add %s', true), __('Publication', true)); ?></legend>
	<?php
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
		<li><?php echo $this->Html->link(sprintf(__('List %s', true), __('Publications', true)), array('action' => 'index'));?></li>
	</ul>
</div>