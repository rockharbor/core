<?php
$this->Paginator->options(array(
    'update' => '#involvement'
));
?>
<h2>Rosters</h2>
<div class="rosters">
<?php
	echo $this->Form->create('Roster', array(
		'default' => false
	));
	echo $this->MultiSelect->create();
?>
	<table cellpadding="0" cellspacing="0" id="rosterTable">
	<tr class="multi-select">
		<th colspan="11">
		<?php			
			echo $this->Html->link('Make A Payment', array(
				'controller' => 'payments',
				'action' => 'add',
				'Involvement' => $involvementId,
				$this->MultiSelect->token
			), array(
				'rel' => 'modal-involvement'
			));
			
			if ($canCheckAll) {
				echo $this->Html->link('Export', array(
					'controller' => 'reports',
					'action' => 'export',
					'Roster',
					$this->MultiSelect->token
				), array(
					'rel' => 'modal-involvement'
				));
			}
		?>
		</th>
	</tr>
	<tr>
			<th><?php 
			if ($canCheckAll) {
				echo $this->MultiSelect->checkbox('all'); 
			}
			?></th>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('User', 'User.username');?></th>
			<th><?php echo $this->Paginator->sort('Involvement', 'Involvement.name');?></th>
			<th><?php echo $this->Paginator->sort('Role', 'Role.name');?></th>
			<th><?php echo $this->Paginator->sort('Payment Option', 'PaymentOption.name');?></th>
			<th><?php echo $this->Paginator->sort('Joined', 'created');?></th>
			<th><?php echo $this->Paginator->sort('amount_due');?></th>
			<th><?php echo $this->Paginator->sort('amount_paid');?></th>
			<th><?php echo $this->Paginator->sort('balance');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($rosters as $roster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php 
		if (in_array($roster['User']['id'], $householdIds) || $roster['User']['Profile']['allow_sponsorage'] || $canCheckAll) {
			echo $this->MultiSelect->checkbox($roster['Roster']['id']);
		}
		?></td>
		<td><?php echo $roster['Roster']['id']; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->flags('User', $roster).$roster['User']['username']; ?>&nbsp;</td>
		<td><?php echo $roster['Involvement']['name']; ?>&nbsp;</td>
		<td><?php echo $roster['Role']['name']; ?>&nbsp;</td>
		<td><?php echo $roster['PaymentOption']['name']; ?>&nbsp;</td>
		<td><?php echo $this->Formatting->date($roster['Roster']['created']); ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($roster['Roster']['amount_due']); ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($roster['Roster']['amount_paid']); ?>&nbsp;</td>
		<td><?php echo $this->Formatting->money($roster['Roster']['balance']); ?>&nbsp;</td>
		<td class="actions">
		<?php
			if ($roster['Roster']['parent_id']) {				
				echo $this->Html->link('Cancel Childcare', array('controller' => 'rosters', 'action' => 'delete', $roster['Roster']['id']), array('id' => 'delete_child_btn_'.$i));
				$this->Js->buffer('CORE.confirmation("delete_child_btn_'.$i.'", "Are you sure you want to opt out of childcare for this child?", {update:"involvement"});');
			} else {
				echo $this->Html->link('Edit', array('action' => 'edit', $roster['Roster']['id']), array('rel' => 'modal-involvement'));
				echo $this->Html->link('Leave '.$roster['Involvement']['InvolvementType']['name'], array('controller' => 'rosters', 'action' => 'delete', $roster['Roster']['id']), array('id' => 'delete_btn_'.$i));
				$this->Js->buffer('CORE.confirmation("delete_btn_'.$i.'", "Are you sure you want to leave? Any children you\'re bringing will also be cancelled.", {update:"involvement"});');
			}
		?></td>
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
	
<?php
	echo $this->MultiSelect->end();
	echo $this->Form->end();
?>

	<p><?php 
	echo $this->Html->link('Add A User', 
		array(
			'controller' => 'searches',
			'action' => 'simple',
			'User', 'notSignedUp', $roster['Involvement']['id'],
			'Add User' => 'addToRoster',
		),
		array(
			'class' => 'button',
			'rel' => 'modal-roster'
		)
	);
	?></p>
</div>

<?php

$this->Js->buffer('function addToRoster(userid) {
	redirect("'.Router::url(array(
		'controller' => 'rosters',
		'action' => 'add',
		'Involvement' => $involvementId
	)).'/User:"+userid);
}');

?>