<?php
$this->Paginator->options(array(
    'updateable' => 'roster'
));
?>
<div id="roster-index">
<h1><?php echo $involvement['Involvement']['name']; ?> Roster</h1>
<div id="columns" class="clearfix">
	<div class="grid_2 column border-right alpha">
		<span class="font-large">
		<?php
		echo $counts['confirmed'];
		if ($involvement['Involvement']['roster_limit'] > 0) {
			echo ' / '.$involvement['Involvement']['roster_limit'];
		}
		?>
		</span>
		<p>People signed up</p>
	</div>
	<div class="grid_2 column border-right">
		<span class="font-large">
		<?php
			echo $counts['total']-$counts['confirmed']
		?>
		</span>
		<p># Unconfirmed</p>
	</div>
	<?php if ($involvement['Involvement']['offer_childcare']) { ?>
	<div class="grid_2 column border-right">
		<span class="font-large">
		<?php
			echo $counts['childcare'];
		?>
		</span>
		<p>Childcare</p>
	</div>
	<?php } ?>
	<div class="grid_2 column omega" id="time">
		<span class="font-large">
		<?php
			echo $counts['leaders'];
		?>
		</span>
		<p># of Leaders</p>
	</div>
</div>
<div>
	<?php
	echo $this->Form->create('Roster', array(
		'class' => 'core-filter-form update-roster-index',
		'url' => array(
			'controller' => 'rosters',
			'action'=> 'index',
			'Involvement' => $involvement['Involvement']['id']
		)
	));
	?>
	<p>Filter</p>
	<?php
	echo $this->Form->input('Filter.Role', array(
		'label' => false,
		'multiple' => 'checkbox',
		'div' => array(
			'tag' => 'span',
			'class' => 'toggle'
		),
		'options' => $roles
	));
	$rosterStatuses[0] = 'All';
	if (empty($this->data['Filter']['roster_status_id'])) {
		$this->data['Filter']['roster_status_id'] = 0;
	}
	echo $this->Form->input('Filter.roster_status_id', array(
		'options' => $rosterStatuses,
		'selected' => $this->data['Filter']['roster_status_id']
	));
	echo $this->Form->end('Filter');
	?>
</div>
<div>
<?php
	echo $this->MultiSelect->create();
?>
	<table cellpadding="0" cellspacing="0" id="rosterTable" class="datatable">
		<thead>
			<?php
			$links = array(
				array(
					'title' => 'Email',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'roster'
					),
					'options' => array(
						'rel' => 'modal-none'
					)
				),
				array(
					'title' => 'Export',
					'url' => array(
						'controller' => 'reports',
						'action' => 'export',
						'Roster',
						$this->MultiSelect->token
					),
					'options' => array(
						'rel' => 'modal-none'
					)
				),				
				array(
					'title' => 'View Map Results',
					'url' => array(
						'controller' => 'reports',
						'action' => 'user_map',
						'Roster',
						$this->MultiSelect->token
					),
					'options' => array(
						'rel' => 'modal-none'
					)
				),
				array(
					'title' => 'Remove',
					'url' => array(
						'controller' => 'rosters',
						'action' => 'delete',
						$this->MultiSelect->token,
						'Involvement' => $involvement['Involvement']['id']
					),
					'options' => array(
						'id' => 'roster-remove'
					)
				),
				array(
					'title' => 'Confirm',
					'url' => array(
						'controller' => 'rosters',
						'action' => 'status',
						$this->MultiSelect->token,
						'Involvement' => $involvement['Involvement']['id'],
					),
					'options' => array(
						'success' => 'CORE.showFlash(data);'
					)
				)
			);
			$colCount = 7;
			if ($involvement['Involvement']['take_payment']) {
				$link[] = array(
					'title' => 'Add Payment',
					'url' => array(
						'controller' => 'payments',
						'action' => 'add',
						'Involvement' => $involvement['Involvement']['id'],
						$this->MultiSelect->token
					),
					'options' => array(
						'rel' => 'modal-roster'
					)
				);
			} else {
				$colCount--;
			}
			$this->Js->buffer('CORE.confirmation("roster-remove", "Are you sure you want to remove the selected users?", {update:"roster"})');
			if ($fullAccess) {
				echo $this->element('multiselect', array(
					'colCount' => $colCount,
					'checkAll' => $fullAccess,
					'links' => $links
				));
			}
			?>			
			<tr>
				<?php if ($fullAccess): ?>
					<th>&nbsp;</th>
				<?php endif; ?>
				<th><?php echo $this->Paginator->sort('Name', 'Profile.last_name');?></th>
				<th><?php echo $this->Paginator->sort('Email', 'Profile.primary_email');?></th>
				<?php if ($fullAccess): ?>
					<th><?php echo $this->Paginator->sort('Phone', 'Profile.cell_phone');?></th>
					<th><?php echo $this->Paginator->sort('Status', 'RosterStatus.name');?></th>
					<?php if ($involvement['Involvement']['take_payment']) { ?>
					<th><?php echo $this->Paginator->sort('balance');?></th>
					<?php } ?>
					<th><?php echo $this->Paginator->sort('Date Joined', 'created');?></th>
				<?php endif; ?>
				<th>Roles</th>
			</tr>
		</thead>
	<tbody>
	<?php
	$addIcon = $this->element('icon', array('icon' => 'add'));
	$canModifyRoles = $this->Permission->check(array('controller' => 'rosters', 'action' => 'roles', 'Involvement' => $involvement['Involvement']['id'], $roster['Roster']['id']));
	$i = 0;
	foreach ($rosters as $roster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<?php if ($fullAccess): ?>
			<td><?php echo $this->MultiSelect->checkbox($roster['Roster']['id']); ?></td>
		<?php endif; ?>
		<td><?php 
		$name = $roster['Profile']['name'].$this->Formatting->flags('User', $roster);
		$link = array('controller' => 'profiles', 'action' => 'view', 'User' => $roster['User']['id']);
		$viewProfilePermission = $this->Permission->check($link);
		if ($viewProfilePermission) {
			echo $this->Html->link($name, $link, array('escape' => false));
		} else {
			echo $this->Html->link($name, '#', array('escape' => false));
		}
                $tooltipWrapper = ($fullAccess || $viewProfilePermission);
                if ($tooltipWrapper): ?>
                &nbsp;<div class="core-tooltip"><?php
                endif;
			if ($fullAccess) {
				echo $this->Html->link('Edit Info', array('controller' => 'rosters', 'action' => 'edit', $roster['Roster']['id'], 'Involvement' => $involvement['Involvement']['id'], 'User' => $roster['User']['id']), array('rel' => 'modal-roster'));
				echo $this->Html->link('View Payments', array('controller' => 'payments', 'action' => 'index', 'User' => $roster['User']['id'], 'Roster' => $roster['Roster']['id']), array('rel' => 'modal-none'));
			}
			if ($viewProfilePermission) {
				echo $this->Html->link('View Profile', array('controller' => 'profiles', 'action' => 'view', 'User' => $roster['User']['id']));
			}
                if ($tooltipWrapper) {
                    echo '</div>';
                }
			?>
		</td>
		<td><?php echo $this->Formatting->email($roster['Profile']['primary_email'], $roster['User']['id']); ?>&nbsp;</td>
		<?php if ($fullAccess): ?>
			<td><?php echo $this->Formatting->phone($roster['Profile']['cell_phone']); ?>&nbsp;</td>
			<td><?php 
			$link = array('controller' => 'rosters', 'action' => 'edit', $roster['Roster']['id'], 'Involvement' => $involvement['Involvement']['id'], 'User' => $roster['User']['id']);
			if ($this->Permission->check($link)) {
				echo $this->Html->link($roster['RosterStatus']['name'], $link, array('rel' => 'modal-roster')); 
			} else {
				echo $roster['RosterStatus']['name'];
			}
			?>&nbsp;</td>
			<?php if ($involvement['Involvement']['take_payment']) { ?>
			<td><?php echo $this->Formatting->money($roster['Roster']['balance']); ?>&nbsp;</td>
			<?php } ?>
			<td><?php echo $this->Formatting->date($roster['Roster']['created']); ?>&nbsp;</td>
		<?php endif; ?>
		<td><?php
		if ($canModifyRoles) {
			echo $this->Html->link($addIcon, array('controller' => 'rosters', 'action' => 'roles', 'Involvement' => $involvement['Involvement']['id'], $roster['Roster']['id']), array('rel' => 'modal-roster', 'escape' => false, 'class' => 'no-hover'));
			echo $this->Html->link(count($roster['Role']).' Roles', array('controller' => 'rosters', 'action' => 'roles', 'Involvement' => $involvement['Involvement']['id'], $roster['Roster']['id']), array('rel' => 'modal-roster'));
		} else {
			echo $this->Html->link(count($roster['Role']).' Roles', '#');
		}
		if (!empty($roster['Role'])) {
			echo '<div class="core-tooltip">';
			echo $this->Text->toList(Set::extract('/name', $roster['Role']));
			echo '</div>';
		}
		?></td>
	</tr>
<?php endforeach; ?>
	</table>	
<?php
	echo $this->element('pagination');
	echo $this->MultiSelect->end();
?>
</div>
<ul class="core-admin-tabs">
	<?php if ($this->Permission->check(array('controller' => 'involvements', 'action' => 'invite', 'Involvement' => $involvement['Involvement']['id']))): ?>
	<li>
	<?php
	echo $this->Permission->link('Invite/Add A User',
		array(
			'controller' => 'searches',
			'action' => 'simple',
			'User',
			'add_invite_user',
			'notSignedUp',
			$involvement['Involvement']['id'],
			'Involvement' => $involvement['Involvement']['id'],
		),
		array(
			'rel' => 'modal-roster'
		)
	);
	?>
	</li>
	<?php endif; ?>
	<?php if ($this->Permission->check(array('controller' => 'involvements', 'action' => 'invite_roster', 'Involvement' => $involvement['Involvement']['id']))): ?>
	<li>
	<?php
	echo $this->Permission->link('Invite/Add This Roster To',
		array(
			'controller' => 'searches',
			'action' => 'simple',
			'Involvement',
			'add_invite_roster',
			'notInvolvement',
			$involvement['Involvement']['id'],
			'Involvement' => $involvement['Involvement']['id'],
		),
		array(
			'rel' => 'modal-roster'
		)
	);
	?>
	</li>
	<?php endif; ?>
</ul>
</div>