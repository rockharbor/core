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
					$class = ' altrow';
				}
			?>
			<tr class="core-iconable clearfix <?php echo $class;?>">
				<td>
					<?php 
					$link = $this->Html->link($leader['User']['Profile']['name'] ,array('controller' => 'profiles', 'action' => 'view', 'User' => $leader['User']['id']));
					echo $link.$this->Formatting->flags('User', $leader['User']); 
					?>
				</td>
				<td>
					<?php echo $this->Formatting->datetime($leader['Leader']['created']); ?>&nbsp;
					<span class="core-icon-container" style="float:right">
						<?php
						$icon = $this->Html->tag('span', '&nbsp;', array('class' => 'core-icon icon-delete'));
						echo $this->Permission->link($icon, array('action' => 'delete', 'User' => $leader['Leader']['user_id'], 'model' => $model, $model => $modelId), array('id' => 'delete_btn_'.$i, 'escape' => false, 'class' => 'no-hover'));
						$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'","Are you sure you want to remove '.$leader['User']['Profile']['name'].'?", {update:"leaders"});');
						?>
					</span>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	echo $this->element('pagination'); ?>
</div>

<ul class="core-admin-tabs">
	<?php
	if ($this->Permission->check(array(
		'controller' => $this->params['controller'],
		'action' => 'add',
		$model => $modelId
	))) {
		$link = $this->Permission->link('Add Leader', array(
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
			'rel' => 'modal-leaders'
		));
		if ($link) {
			echo $this->Html->tag('li', $link);
		}
	}
	?>
</ul>