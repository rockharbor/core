<div class="households">
	<h2>Households</h2>
	
	<?php
	$h = 0;
	foreach ($households as $household):
		$h++;
	?>
	<h3>Household #<?php echo $h; ?></h3>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>User</th>
			<th>Confirmed?</th>
			<th class="actions">Actions</th>
	</tr>
	
	<?php
	$m = 0;
	foreach ($household['HouseholdMember'] as $householdMember):
		$class = '';
		if ($m++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php  
		if ($householdMember['User']['id'] == $household['HouseholdContact']['id']) {
			echo '*';
		}
		echo $this->Html->link($householdMember['User']['Profile']['name'], array(
				'controller'=>'users',
				'action'=>'edit',
				'User' => $householdMember['User']['id']
			), array('escape'=>false)).'<br />'.$this->Formatting->flags('User', $householdMember).$householdMember['User']['username'];
		?>&nbsp;</td>
		<td><?php echo $this->SelectOptions->booleans[$householdMember['confirmed']];?>&nbsp;</td>
		<td class="actions"><?php
			echo $this->Js->link('Leave', array(
				'controller' => 'households',
				'action' => 'shift_households',
				$householdMember['User']['id'],
				$household['Household']['id'],				
				'User' => $user
			), array(
				'complete' => 'CORE.update(\'households\');'
			));	
			if ($householdMember['User']['id'] != $household['HouseholdContact']['id']) {
				echo $this->Js->link('Make HC', array(
					'controller' => 'households',
					'action' => 'make_household_contact',
					$householdMember['User']['id'],
					$household['Household']['id'],					
					'User' => $user
				), array(
					'complete' => 'CORE.update(\'households\');'
				));
			}
		?></td>
	</tr>
	<?php endforeach; ?>
	</table>
<?php 

echo $this->Html->link('Add someone', 
	array(
		'controller' => 'searches',
		'action' => 'simple',
		'User', 'notInHousehold', $household['Household']['id'],
		'Add User' => 'addTo'.$household['Household']['id'].'HH',
	),
	array(
		'class' => 'button',
		'rel' => 'modal-content'
	)
);

echo $this->Html->scriptBlock(
'function addTo'.$household['Household']['id'].'HH(userid) {
	CORE.request("'.Router::url(array(
		'controller' => 'households',
		'action' => 'shift_households'
	)).'/"+userid+"/'.$household['Household']['id'].'");
}');

endforeach; 


?>
</div>