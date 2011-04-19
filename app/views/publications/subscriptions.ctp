<?php
$this->Js->buffer('CORE.fallbackRegister("subscriptions");');
$this->Paginator->options(array(
    'updateable' => 'parent'
));
?>
<h1>Publications</h1>
<div>	
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th><?php echo $this->Paginator->sort('name');?></th>
				<th><?php echo $this->Paginator->sort('description');?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach ($publications as $publication):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
			<tr<?php echo $class;?>>
				<td><?php
					if (in_array($publication['Publication']['id'], $subscriptions)) {
						echo $this->Html->link('Unsubscribe', array('action' => 'toggle_subscribe', $publication['Publication']['id'], false, 'User'=>$userId), array('id' => 'toggle_btn_'.$i));
						$this->Js->buffer('CORE.confirmation("toggle_btn_'.$i.'", "Are you sure you want to unsubscribe to the '.$publication['Publication']['name'].'?", {update:"subscriptions"});');
					} else {
						echo $this->Html->link('Subscribe', array('action' => 'toggle_subscribe', $publication['Publication']['id'], true, 'User'=>$userId), array('id' => 'toggle_btn_'.$i));
						$this->Js->buffer('CORE.confirmation("toggle_btn_'.$i.'", "Are you sure you want to subscribe to the '.$publication['Publication']['name'].'?", {update:"subscriptions"});');
					}
					?>
				</td>
				<td><?php echo $publication['Publication']['name']; ?>&nbsp;</td>
				<td><?php echo $publication['Publication']['description']; ?>&nbsp;</td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	echo $this->element('pagination');
	?>
</div>