<h1>Households</h1>
<div class="households">
	<div class="grid_10 alpha omega">
	<?php
	$h = 0;
	foreach ($households as $household):
		$h++;
	?>
	<h3>Household #<?php echo $h; ?></h3>	
	<?php
	$m = 0;
	foreach ($household['HouseholdMember'] as $householdMember):
		$class = 'household_member';
		if ($householdMember['User']['id'] == $household['HouseholdContact']['id']) {
			$class = 'household_contact';
		}
		$user = $householdMember['User'];
	?>
	<div class="grid_5 alpha omega <?php echo $class; ?>">
		<div class="grid_2 alpha">
			<?php
				$path = null;
				$upload = false;
				if (!empty($user['Image'])) {
					$path = 'm'.DS.$user['Image'][0]['dirname'].DS.$user['Image'][0]['basename'];
				} else {
					$default = Core::read('user.default_image');
					if ($default) {
						$path = 'm'.DS.$default['Image']['dirname'].DS.$default['Image']['basename'];
					}
					$upload = true;
				}
				echo '<div id='.$user['User']['id'].'Image'.'>';
					echo $this->Media->embed($path, array('restrict' => 'image'));
				echo '</div>';
				if ($upload) {
					echo $this->element('upload', array(
						'type' => 'image',
						'model' => 'User',
						'User' => $user['User']['id'],
						'title' => 'Upload Photo',
						'update' => $user['User']['id'].'Image'
					));
				} else {
					echo $this->Js->link('Remove Photo', array('controller' => 'user_images', 'action' => 'delete', $user['Image'][0]['id'], 'User' => $user['User']['id']), array('class' => 'button', 'update' => '#'.$user['User']['id'].'Image'));
				}
			?>
		</div>
		<div class="grid_3 omega">
			<?php
			echo $this->Html->link($user['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
			echo $this->Formatting->flags('User', $householdMember);
			echo $this->Formatting->email($user['Profile']['primary_email']);
			echo $this->Html->tag('dl',
				$this->Html->tag('dt', 'Age:').
				$this->Html->tag('dd', $this->Formatting->age($user['Profile']['age']))
			);
			if ($user['Profile']['child']) {
				echo $this->Html->tag('dl',
					$this->Html->tag('dt', 'Dedication Date:').
					$this->Html->tag('dd', $this->Formatting->age($user['Profile']['baby_dedication_date']))
				);
			}
			if ($class == 'household_contact') {
				echo $this->Html->tag('span', 'Household Contact', array('class' => 'household_contact'));
			}
			?>
			<hr>
			<?php
			echo $this->Permission->link('Edit Profile', array('controller' => 'profiles', 'action' => 'edit', 'User' => $user['id']));
			echo $this->Permission->link('View Involvement', array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
			echo $this->Permission->link('Remove', array('controller' => 'households', 'action' => 'shift_households', $user['id'], $household['Household']['id'], 'User' => $activeUser['User']['id']));
			echo $this->Permission->link('Make Household Contact', array('controller' => 'households', 'action' => 'make_household_contact', $user['id'], $household['Household']['id'], 'User' => $activeUser['User']['id']));
			echo $this->Permission->link('Confirm', array('controller' => 'households', 'action' => 'confirm', $user['id'], $household['Household']['id'], 'User' => $activeUser['User']['id']));
			?>
		</div>
		<?php if ($user['Profile']['child']): ?>
		<div class="grid_5 alpha omega">
			<hr>
			<?php
			echo $this->Html->tag('dl',
				$this->Html->tag('dt', 'Special Needs:').
				$this->Html->tag('dd', $user['Profile']['special_needs'])
			);
			echo $this->Html->tag('dl',
				$this->Html->tag('dt', 'Special Alerts:').
				$this->Html->tag('dd', $user['Profile']['special_alerts'])
			);
			echo $this->Html->tag('dl',
				$this->Html->tag('dt', 'Allergies:').
				$this->Html->tag('dd', $user['Profile']['allergies'])
			);
			?>
		</div>
		<?php endif; ?>
		<?php
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
	</div>
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