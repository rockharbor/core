<h2>Leaders</h2>
<div class="leaders">
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>User</th>
			<th>Model</th>
			<th>Model Id</th>
			<th>Created</th>
			<th>Modified</th>
			<th class="actions">Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($leaders as $leader):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Formatting->flags('User', $leader['User']).$leader['User']['username']; ?>&nbsp;</td>
		<td><?php echo $leader['Leader']['model']; ?>&nbsp;</td>
		<td><?php echo $leader['Leader']['model_id']; ?>&nbsp;</td>
		<td><?php echo $leader['Leader']['created']; ?>&nbsp;</td>
		<td><?php echo $leader['Leader']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('Remove Leader', array('action' => 'delete', 'User' => $leader['Leader']['user_id'], 'model' => $model, $model => $modelId), array(
					'id'=>'delete_btn_'.$i
			)); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>

<div>
<?php
	$this->Js->buffer('function addLeader(userid) {
		var data = {
			"data[Leader][model]": "'.$model.'",
			"data[Leader][model_id]": "'.$modelId.'",
			"data[Leader][user_id]": userid
		};
		
		CORE.request("'.Router::url(array(
		'action' => 'add',
		'model' => $model, 
		$model => $modelId
		)).'", [], data);
	}');

	echo $this->Html->link('Add Leader', array(
		'controller' => 'users',
		'action' => 'simple_search',
		'Add Leader' => 'addLeader',
		'is Leader.model '.$model, 
		'not Leader.model_id '.$modelId,
		'is Profile.qualified_leader 1'
	), array (
		'rel' => 'modal-leaders',
		'class' => 'button'
	));
?>
</div>

<?php
while ($i > 0) {
	$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'","Are you sure you want to remove this Leader?", {update:"leaders"});');
	$i--;
}
?>