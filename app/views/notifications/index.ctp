<div class="notifications">
<?php
	/*echo $this->Form->create('Roster', array(
		'default' => false,
		'action' => 'delete'
	));*/
	echo $this->MultiSelect->create();
?>
	<h2><?php __('Notifications');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr class="multi-select">
		<th colspan="3">
		<?php			
			echo $this->Js->link('Mark as read', array(
				'controller' => 'notifications',
				'action' => 'read',
				$this->MultiSelect->token
			), array(
				'update' => '#content'
			));
			echo $this->Js->link('Delete', array(
				'controller' => 'notifications',
				'action' => 'delete',
				$this->MultiSelect->token
			), array(
				'update' => '#content'
			));
		?>
		</th>
	</tr>
	<tr>
		<th><?php echo !empty($notifications) ? $this->MultiSelect->checkbox('all'): ''; ?></th>
		<th><?php echo $this->Paginator->sort('Notification', 'id'); ?></th>
		<th>Actions</th>
	</tr>
	<?php
	$i = 0;
	foreach ($notifications as $notification):
		$class = null;
		if (!$notification['Notification']['read']) {
			$class = ' class="unread"';
		}
	?>
	<tr<?php echo $class;?> id="notification-<?php echo $notification['Notification']['id'];?>">
		<td><?php echo $this->MultiSelect->checkbox($notification['Notification']['id']); ?></td>
		<td><?php 
		echo $notification['Notification']['body']; 
		if (!$notification['Notification']['read']) {
			$this->Js->buffer('$("#notification-'.$notification['Notification']['id'].'").bind("mouseenter", function() {
				CORE.request("'.Router::url(array('action' => 'read', $notification['Notification']['id'])).'");
				$(this).unbind("mouseenter");
				$(this).children("td").animate({backgroundColor:"#fff"}, "slow");
			});');
		} 
		?></td>
		<td class="actions"><?php
			echo $this->Js->link('Delete', array('action' => 'delete', $notification['Notification']['id']), array(
				'update' => '#content'
			));			
		?></td>
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