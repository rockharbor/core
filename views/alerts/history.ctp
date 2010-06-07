<?php
$this->Paginator->options(array(
	'update' => '#content'
));
echo $this->MultiSelect->create();
?>
<div class="alerts">
	<h2>Alerts</h2>
	
	<p>Quick Filter:</p>
	<p>
		<?php
			echo $this->Js->link('All', array(), array('update' => '#content'));
			echo '&nbsp;&nbsp;';
			echo $this->Js->link('Unread', array('unread'), array('update' => '#content'));
			echo '&nbsp;&nbsp;';
			echo $this->Js->link('Read', array('read'), array('update' => '#content'));
		?>
	</p>
	
	<table cellpadding="0" cellspacing="0">
	<tr class="multi-select">
		<th colspan="4">
		<?php			
			echo $this->Js->link('Mark as read', array(
				'controller' => 'alerts',
				'action' => 'read',
				$this->MultiSelect->cache
			), array(
				'update' => '#content'
			));
		?>
		</th>
	</tr>
	<tr>
			<th><?php echo $this->MultiSelect->checkbox('all'); ?></th>
			<th></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('Released', 'created');?></th>
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
		<td><?php echo $this->MultiSelect->checkbox($alert['Alert']['id']); ?>&nbsp;</td>
		<td><?php echo in_array($alert['Alert']['id'], $read) ? 'read' : 'unread';?></td>
		<td>
			<div class="alert <?php echo $alert['Alert']['importance']; ?>">
				<h1><?php echo $alert['Alert']['name'];?></h1>
				<p><?php echo $alert['Alert']['description'];?></p>
			</div>
		</td>
		<td><?php echo $this->Formatting->date($alert['Alert']['created']);?></td>
	</tr>
<?php
endforeach;
echo $this->MultiSelect->end();
?>
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