<?php
$this->Paginator->options(array(
	'update' => '#content'
));
?>
<div class="alerts index">
	<h2>Alerts</h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th><?php echo $this->Paginator->sort('Group', 'Group.name');?></th>
			<th><?php echo $this->Paginator->sort('importance');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($alerts as $alert):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $alert['Alert']['id']; ?>&nbsp;</td>
		<td><?php echo $alert['Alert']['name']; ?>&nbsp;</td>
		<td><?php echo $alert['Alert']['description']; ?>&nbsp;</td>
		<td><?php echo $alert['Alert']['created']; ?>&nbsp;</td>
		<td><?php echo $alert['Alert']['modified']; ?>&nbsp;</td>
		<td><?php echo $alert['Group']['name']; ?>&nbsp;</td>
		<td><?php echo $alert['Alert']['importance']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $alert['Alert']['id']), array('rel' => 'modal-content')); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $alert['Alert']['id']), array('id' => 'delete_btn_'.$i)); ?>
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
		<li><?php echo $this->Html->link('New Alert', array('action' => 'add'), array('rel' => 'modal-content')); ?></li>
	</ul>
</div>
<?php
while ($i > 0) {
	$this->Js->buffer('CORE.confirmation(\'delete_btn_'.$i.'\',\'Are you sure you want to delete this Alert?\', {update:\'content\'});');
	$i--;
}
?>