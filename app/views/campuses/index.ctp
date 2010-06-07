<div class="campuses index">
	<h2><?php __('Campuses');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($campuses as $campus):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $campus['Campus']['id']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['name']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['description']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['active']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['created']; ?>&nbsp;</td>
		<td><?php echo $campus['Campus']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', 'Campus' => $campus['Campus']['id']), array('rel' => 'modal-content')); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', 'Campus' => $campus['Campus']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $campus['Campus']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $campus['Campus']['id'])); ?>
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
		<?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('New %s', true), __('Campus', true)), array('action' => 'add'), array('rel' => 'modal-content')); ?></li>
	</ul>
</div>