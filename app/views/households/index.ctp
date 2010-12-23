<h1>Households</h1>
<div class="households">
		<?php
		$h = 0;
		foreach ($households as $household):
			$h++;
		?>
		<h3>Household #<?php echo $h; ?></h3>
		<div class="grid_10 alpha omega equal-height">
		<?php
		$m = 0;
		foreach ($household['HouseholdMember'] as $householdMember):
			$m++;
			$class = 'household-member';
			if ($householdMember['User']['id'] == $household['HouseholdContact']['id']) {
				$class = 'household-contact';
			}
			$user = $householdMember['User'];
			$alphaomega = ($m % 2 == 0) ? ' omega' : ' alpha';
		?>
		<div class="grid_5 <?php echo $alphaomega; ?>">
			<div class="<?php echo $class; ?>">
				<div style="float:left;margin-right:10px;width:30%">
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
						echo '<div id='.$user['id'].'Image'.'>';
						echo $this->Media->embed($path, array('restrict' => 'image'));						
						if ($upload) {
							echo $this->element('upload', array(
								'type' => 'image',
								'model' => 'User',
								'User' => $user['id'],
								'title' => 'Upload Photo',
								'update' => $user['id'].'Image'
							));
						} else {
							echo $this->Permission->link('Remove Photo', array('controller' => 'user_images', 'action' => 'delete', $user['Image'][0]['id'], 'User' => $user['id']), array('class' => 'button', 'update' => '#'.$user['id'].'Image'));
						}
						echo '</div>';
					?>
				</div>
				<div style="float:left;width:60%">
					<p>
					<?php
					echo $this->Html->link($user['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
					echo $this->Formatting->flags('User', $householdMember);
					echo '<br />';
					echo $this->Formatting->email($user['Profile']['primary_email'], $user['id']);
					echo $this->Html->tag('dl',
						$this->Html->tag('dt', 'Age:').
						$this->Html->tag('dd', $this->Formatting->age($user['Profile']['age']))
					);
					if ($user['Profile']['child']) {
						echo $this->Html->tag('dl',
							$this->Html->tag('dt', 'Dedication Date:').
							$this->Html->tag('dd', $this->Formatting->date($user['Profile']['baby_dedication_date']))
						);
					}
					if ($class == 'household-contact') {
						echo $this->Html->tag('span', 'Household Contact', array('class' => 'household-contact'));
					}
					?>
					</p>
					<hr>
					<p>
					<?php
					$this->Permission->activeUser = array('User' => $user, 'Group' => $user['Group']);
					$this->Permission->params = array('User' => $user['id']);
					echo $this->Permission->link('Edit Profile', array('controller' => 'profiles', 'action' => 'edit', 'User' => $user['id']));
					echo '<br />';
					echo $this->Permission->link('View Involvement', array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
					echo '<br />';
					if ($class != 'household-contact' && !$user['Profile']['child']) {
						echo $this->Permission->link('Remove', array('controller' => 'households', 'action' => 'shift_households', $user['id'], $household['Household']['id'], 'User' => $activeUser['User']['id']), array('id' => 'household_remove_'.$m));
						$this->Js->buffer('CORE.confirmation("household_remove_'.$m.'","Are you sure you want to remove '.$user['Profile']['name'].' from this household?", {update:"households"});');
						echo '<br />';
						echo $this->Permission->link('Make Household Contact', array('controller' => 'households', 'action' => 'make_household_contact', $user['id'], $household['Household']['id'], 'User' => $activeUser['User']['id']), array('id' => 'household_contact_'.$m));
						$this->Js->buffer('CORE.confirmation("household_contact_'.$m.'","Are you sure you want to make '.$user['Profile']['name'].' the household contact?", {update:"households"});');
						echo '<br />';
					}
					if (!$householdMember['confirmed']) {
						echo $this->Permission->link('Confirm', array('controller' => 'households', 'action' => 'confirm', $user['id'], $household['Household']['id'], 'User' => $activeUser['User']['id']), array('id' => 'household_confirm_'.$m));
						$this->Js->buffer('CORE.confirmation("household_confirm_'.$m.'","Are you sure you want to confirm '.$user['Profile']['name'].'\'s addition to this household?", {update:"households"});');
						echo '<br />';
					}
					?>
					</p>
				</div>
				<?php if ($user['Profile']['child']): ?>
				<div style="clear:both;">
					<hr>
					<?php
					echo $this->Html->tag('dl',
						$this->Html->tag('dt', 'Special Needs:').
						$this->Html->tag('dd', $user['Profile']['special_needs'].'&nbsp;')
					);
					echo $this->Html->tag('dl',
						$this->Html->tag('dt', 'Special Alerts:').
						$this->Html->tag('dd', $user['Profile']['special_alert'].'&nbsp;')
					);
					echo $this->Html->tag('dl',
						$this->Html->tag('dt', 'Allergies:').
						$this->Html->tag('dd', $user['Profile']['allergies'].'&nbsp;')
					);
					?>
				</div>
			<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<ul class="core-admin-tabs">
		<li>
		<?php
		echo $this->Html->link('Add someone',
			array(
				'controller' => 'searches',
				'action' => 'simple',
				'User',
				'add_invite_user_household',
				'notInHousehold',
				$household['Household']['id'],
			),
			array(
				'rel' => 'modal-households'
			)
		);
		?>
		</li>
	</ul>

<?php
echo $this->Html->scriptBlock(
'function addToHH(userid) {
	CORE.request("'.Router::url(array(
		'controller' => 'households',
		'action' => 'shift_households'
	)).'/"+userid+"/'.$household['Household']['id'].'");
}');

endforeach;
?>
</div>