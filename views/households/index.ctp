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
					$confirmed = null;
					if (!$householdMember['confirmed']) {
						$confirmed = ' (Unconfirmed)';
					}
					if ($this->Permission->check(array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']))) {
						echo $this->Html->link($user['Profile']['name'].$confirmed, array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
					} else {
						echo $user['Profile']['name'].$confirmed;
					}
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
					<p class="actions">
					<?php
					echo $this->Permission->link('Edit Profile', array('controller' => 'profiles', 'action' => 'edit', 'User' => $user['id']));
					echo $this->Permission->link('View Involvement', array('controller' => 'profiles', 'action' => 'view', 'User' => $user['id']));
					if ($class != 'household-contact' || count($households) > 1) {
						echo $this->Permission->link('Remove', array('controller' => 'households', 'action' => 'delete', $user['id'], $household['Household']['id'], 'User' => $user['id']), array('id' => 'household_remove_'.$m));
						$this->Js->buffer('CORE.confirmation("household_remove_'.$m.'","Are you sure you want to remove '.$user['Profile']['name'].' from this household?", {update:true});');
					}
					if ($class != 'household-contact' && !$user['Profile']['child']) {
						echo $this->Permission->link('Make Household Contact', array('controller' => 'households', 'action' => 'make_household_contact', $user['id'], $household['Household']['id'], 'User' => $user['id']), array('id' => 'household_contact_'.$m));
						$this->Js->buffer('CORE.confirmation("household_contact_'.$m.'","Are you sure you want to make '.$user['Profile']['name'].' the household contact?", {update:true});');
					}
					if (!$householdMember['confirmed']) {
						echo $this->Permission->link('Confirm', array('controller' => 'households', 'action' => 'confirm', $user['id'], $household['Household']['id'], 'User' => $user['id']), array('id' => 'household_confirm_'.$m));
						$this->Js->buffer('CORE.confirmation("household_confirm_'.$m.'","Are you sure you want to confirm '.$user['Profile']['name'].' to this household?", {update:true});');
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
				'controller' => 'users',
				'action' => 'household_add',
				'Household' => $household['Household']['id']
			),
			array(
				'data-core-modal' => 'true'
			)
		);
		?>
		</li>
	</ul>

<?php
endforeach;
?>
</div>