<?php
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Leaders</h1>
<div class="clearfix">
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th><?php echo $this->Paginator->sort('name'); ?></th>
				<th><?php echo $this->Paginator->sort('created', 'Joined'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($leaders as $leader):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
			?>
			<tr<?php echo $class;?>>
				<td>
					<?php echo $this->Html->link('Remove Leader', array('action' => 'delete', 'User' => $leader['Leader']['user_id'], 'model' => $model, $model => $modelId), array(
							'id'=>'delete_btn_'.$i
					)); ?>
				</td>
				<td><?php echo $this->Formatting->flags('User', $leader['User']).$leader['User']['Profile']['name']; ?>&nbsp;</td>
				<td><?php echo $this->Formatting->datetime($leader['Leader']['created']); ?>&nbsp;</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	echo $this->element('pagination'); ?>
</div>

<div>
<?php
	echo $this->Permission->link('Add Leader', array(
		'controller' => 'searches',
		'action' => 'simple',
		'User',
		'add_leader',
		'notLeaderOf',
		$model,
		$modelId,
		'leader_controller' => $this->params['controller'],
		'leader_model' => $model,
		'leader_model_id' => $modelId
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