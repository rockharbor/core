<div class="involvements index">
	<h2><?php __('Involvements');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('Ministry', 'Ministry.name');?></th>
			<th><?php echo $this->Paginator->sort('Type', 'InvolvementType.name');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('roster_limit');?></th>
			<th><?php echo $this->Paginator->sort('roster_visible');?></th>
			<th><?php echo $this->Paginator->sort('group_id');?></th>
			<th><?php echo $this->Paginator->sort('signup');?></th>
			<th><?php echo $this->Paginator->sort('take_payment');?></th>
			<th><?php echo $this->Paginator->sort('offer_childcare');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($involvements as $involvement):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $involvement['Involvement']['id']; ?>&nbsp;</td>
		<td><?php echo $involvement['Ministry']['name']; ?>&nbsp;</td>
		<td><?php echo $involvement['InvolvementType']['name']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['name']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['description']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['roster_limit']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['roster_visible']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['group_id']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['signup']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['take_payment']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['offer_childcare']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['active']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['created']; ?>&nbsp;</td>
		<td><?php echo $involvement['Involvement']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', 'Involvement' => $involvement['Involvement']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', 'Involvement' => $involvement['Involvement']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $involvement['Involvement']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $involvement['Involvement']['id'])); ?>
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
		<li><?php echo $this->Html->link(sprintf(__('New %s', true), __('Involvement', true)), array('action' => 'add')); ?></li>
	</ul>
</div>