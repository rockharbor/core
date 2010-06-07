<div class="publications">
	<h2>Publications</h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('link');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($publications as $publication):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $publication['Publication']['id']; ?>&nbsp;</td>
		<td><?php echo $publication['Publication']['name']; ?>&nbsp;</td>
		<td><?php echo $publication['Publication']['link']; ?>&nbsp;</td>
		<td><?php echo $publication['Publication']['description']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link('View', array('action' => 'view', $publication['Publication']['id'])); ?>
			<?php 
			if (in_array($publication['Publication']['id'], $subscriptions)) {
				echo $this->Html->link('Unsubscribe', array('action' => 'toggle_subscribe', $publication['Publication']['id'], false, 'User'=>$userId), array('id' => 'toggle_btn_'.$i));
				$this->Js->buffer('CORE.confirmation("toggle_btn_'.$i.'", "Are you sure you want to unsubscribe to the '.$publication['Publication']['name'].'?", {update:"subscriptions"});');
			} else {
				echo $this->Html->link('Subscribe', array('action' => 'toggle_subscribe', $publication['Publication']['id'], true, 'User'=>$userId), array('id' => 'toggle_btn_'.$i));
				$this->Js->buffer('CORE.confirmation("toggle_btn_'.$i.'", "Are you sure you want to subscribe to the '.$publication['Publication']['name'].'?", {update:"subscriptions"});');
			}			
			?>
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