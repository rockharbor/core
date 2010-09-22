<div class="queues index">
	<h2><?php __('Queues');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php __('To');?></th>
			<th><?php __('Cc');?></th>
			<th><?php __('Bcc');?></th>
			<th><?php echo $this->Paginator->sort('from');?></th>
			<th><?php echo $this->Paginator->sort('subject');?></th>
			<th><?php echo $this->Paginator->sort('delivery');?></th>
			<th><?php __('Message');?></th>
			<th><?php __('Header');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($queues as $queue):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Text->truncate($this->Text->toList($queue['Queue']['to'])); ?>&nbsp;</td>
		<td><?php echo $this->Text->truncate($this->Text->toList($queue['Queue']['cc'])); ?>&nbsp;</td>
		<td><?php echo $this->Text->truncate($this->Text->toList($queue['Queue']['bcc'])); ?>&nbsp;</td>
		<td><?php echo $queue['Queue']['from']; ?>&nbsp;</td>
		<td><?php echo $queue['Queue']['subject']; ?>&nbsp;</td>
		<td><?php echo $queue['Queue']['delivery']; ?>&nbsp;</td>
		<td><?php echo $this->Text->truncate(implode('',$queue['Queue']['header'])); ?>&nbsp;</td>
		<td><?php echo $this->Text->truncate(implode('',$queue['Queue']['message'])); ?>&nbsp;</td>
		<td><?php echo $queue['Queue']['created']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $queue['Queue']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $queue['Queue']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $queue['Queue']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>