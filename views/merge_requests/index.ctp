<?php
$this->Paginator->options(array(
    'update' => '#content', 
    'evalScripts' => true
));
?>

<div class="merge_requests">
	<h2><?php echo $model; ?> Merge Requests</h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort($model, 'model_id');?></th>
			<th><?php echo $this->Paginator->sort('Merge record', 'merge_id');?></th>
			<th><?php echo $this->Paginator->sort('Requester', 'Requester.username');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th>Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($requests as $request):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $request['MergeRequest']['id']; ?>&nbsp;</td>
		<td><?php echo $request['Source'][$displayField]; ?>&nbsp;</td>
		<td><?php echo $request['Target'][$displayField]; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->flags('User', $request['Requester']).$request['Requester']['username']; ?>&nbsp;</td>
		<td><?php echo $request['MergeRequest']['created']; ?>&nbsp;</td>
		<td class="actions">
		<?php echo $this->Html->link('View', array('action' => 'view', $request['MergeRequest']['id']), array('rel'=>'modal-content')); ?>
		<?php echo $this->Html->link('Delete', array('action' => 'delete', $request['MergeRequest']['id']), array('id'=>'delete_btn_'.$i)); ?>
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

<?php

while ($i > 0) {
	$this->Js->buffer('CORE.confirmation(\'delete_btn_'.$i.'\',\'Are you sure you want to delete this Request? All merge data will also be removed.\', {update:\'content\'});');
	$i--;
}

?>