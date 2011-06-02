<h1>Notify Users</h1>
<div class="clearfix">
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th>User</th>
				<th>Request Type</th>
				<th>Added</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($requestNotifiers as $requestNotifier):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' altrow';
				}
			?>
			<tr class="core-iconable<?php echo $class;?>">
				<td><?php echo $requestNotifier['User']['Profile']['name']; ?>&nbsp;</td>
				<td><?php echo $requestNotifier['RequestType']['name']; ?>&nbsp;</td>
				<td><?php echo $this->Formatting->datetime($requestNotifier['RequestNotifier']['created']); ?>&nbsp;</td>
				<td>
					<div class="core-icon-container">
						<?php 
						echo $this->Html->link($this->element('icon', array('icon' => 'delete')), array('action' => 'delete', $requestNotifier['RequestNotifier']['id'], 'RequestType' => $requestNotifier['RequestType']['id']), array('id' => 'delete_btn_'.$requestNotifier['RequestNotifier']['id'], 'escape' => false, 'class' => 'no-hover'));
						$this->Js->buffer('CORE.confirmation("delete_btn_'.$requestNotifier['RequestNotifier']['id'].'","Are you sure you want to remove this user from being notified of new '.Inflector::pluralize($requestNotifier['RequestType']['name']).'?", {update:"content"});');
						?>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<ul class="core-admin-tabs">
<?php
	$link = $this->Permission->link('Add User', array(
		'plugin' => false,
		'controller' => 'searches',
		'action' => 'simple',
		'User',
		'CommunicationsRequests.add_notifier',
		'RequestType' => $requestType,
	), array (
		'rel' => 'modal-requesttypes'
	));
	if ($link) {
		echo $this->Html->tag('li', $link);
	}
?>
</ul>