<h1>Search Users</h1>
<div class="searches content-box">
<?php echo $this->Form->create(null, array(
	'action' => 'user',
	'default' => false,
	'id' => 'SearchUserForm',
	'inputDefaults' => array(
		'empty' => true,
		'hiddenField' => false
	)
));?>
	<div class="clearfix">
		<div class="grid_third alpha">
			<fieldset>
				<legend>Basic Information</legend>
		<?php
			echo $this->Form->input('Search.operator', array(
				'type' => 'select',
				'options' => array(
					'AND' => 'Match all',
					'OR' => 'Match any'
				),
				'empty' => false
			));
			echo $this->Form->input('User.username');
			echo $this->Form->input('User.group_id', array(
				'multiple' => 'checkbox',
				'empty' => false
			));
			echo $this->Form->input('User.active', array(
				'type' => 'select',
				'options' => array(
					1 => 'Active',
					0 => 'Inactive'
				),
				'selected' => isset($this->data['User']['active']) ? $this->data['User']['active'] : 1
			));
			echo $this->Form->input('User.flagged', array(
				'type' => 'select',
				'options' => array(
					1 => 'Flagged',
					0 => 'Unflagged'
				)
			));
			echo $this->Form->input('Profile.first_name');
			echo $this->Form->input('Profile.last_name');
			echo $this->Form->input('Profile.email');
			echo $this->Form->input('Profile.gender', array(
				'type' => 'select',
				'options' => $this->SelectOptions->genders
			));
			echo $this->Form->input('Profile.marital_status', array(
				'type' => 'select',
				'options' => $this->SelectOptions->maritalStatuses
			));
			echo $this->Form->input('Profile.background_check_complete');
			echo $this->Form->input('Profile.BackgroundCheck.start', array(
				'type' => 'date',
				'label' => 'Background Check Range',
				'minYear' => 1900
			));
			echo $this->Form->input('Profile.BackgroundCheck.end', array(
				'type' => 'date',
				'label' => false,
				'minYear' => 1900
			));
		?>
			</fieldset>
		</div>
		<div class="grid_third">
			<fieldset>
				<legend>Location</legend>
		<?php
			echo $this->Form->input('Address.city');
			echo $this->Form->input('Address.state', array(
				'options' => $this->SelectOptions->states
			));
			echo $this->Form->input('Address.zip');
			echo $this->Form->input('Address.Zipcode.region_id', array(
				'type' => 'select',
				'options' => $regions
			));
			echo $this->Form->input('Distance.distance_from', array(
				'after' => '<p>(try: orange, ca)</p>'
			));
			echo $this->Form->input('Distance.distance', array(
				'type' => 'select',
				'options' => array(
					'5' => '5 miles',
					'10' => '10 miles',
					'15' => '15 miles',
					'25' => '25 miles'
				),
				'empty' => false
			));
		?>
			</fieldset>
			<fieldset>
				<legend>Involvement</legend>
			<?php
			echo $this->Form->input('Roster.Involvement.name', array(
				'label' => 'Involved in'
			));
			echo $this->Form->input('Roster.Involvement.Ministry.name', array(
				'label' => 'Involved in Ministry'
			));
			echo $this->Form->input('Profile.campus_id', array(
				'multiple' => 'checkbox',
				'empty' => false
			));
			echo $this->Form->input('Profile.classification_id');
			echo $this->Form->input('Profile.qualified_leader', array(
				'type' => 'select',
				'options' => $this->SelectOptions->booleans
			));
			echo $this->Form->input('Profile.currently_leading', array(
				'type' => 'checkbox',
				'label' => 'Currently Leading'
			));
			echo $this->Form->input('Profile.signed_covenant_2011');
			echo $this->Form->input('Profile.signed_covenant_2012');
			echo $this->Form->input('Profile.signed_covenant_2013');
			?>
			</fieldset>
		</div>
		<div class="grid_third omega">
			<fieldset>
				<legend>Age</legend>
			<?php
			$chunks = array_chunk($this->SelectOptions->ageGroups, 4, true);
			foreach ($chunks as $chunk) {
				echo $this->Html->tag('div', $this->Form->input('Profile.age', array(
					'type' => 'select',
					'options' => $chunk,
					'multiple' => 'checkbox',
					'label' => false,
					'empty' => false
				)), array(
					 'style' => 'float:left;width:50px;margin-right:10px;'
				));
			}
			echo '<br clear="all" />';
			echo $this->Form->input('Profile.child', array(
				'type' => 'checkbox',
				'label' => 'Considered a child'
			));
			echo $this->Form->input('Profile.adult', array(
				'type' => 'checkbox',
				'label' => 'Marked as an adult'
			));
			echo $this->Form->input('Profile.Birthday.start', array(
				'type' => 'date',
				'label' => 'Birthday Range',
				'minYear' => 1900
			));
			echo $this->Form->input('Profile.Birthday.end', array(
				'type' => 'date',
				'label' => false,
				'minYear' => 1900
			));
			?>
			</fieldset>
			<fieldset>
				<legend>School</legend>
			<?php
			$chunks = array_chunk($this->SelectOptions->grades, 8, true);
			foreach ($chunks as $chunk) {
				echo $this->Html->tag('div', $this->Form->input('Profile.grade', array(
					'type' => 'select',
					'options' => $chunk,
					'multiple' => 'checkbox',
					'label' => false,
					'empty' => false
				)), array(
					 'style' => 'float:left;width:49%;margin-right:1%;'
				));
			}
			echo '<br clear="all" />';
			echo $this->Form->input('Profile.graduation_year', array(
				'type' => 'select',
				'options' => $this->SelectOptions->generateOptions('year', array(
					'min' => 1900,
					'max' => date('Y') + 20
				))
			));
			echo $this->Form->input('Profile.elementary_school_id');
			echo $this->Form->input('Profile.middle_school_id');
			echo $this->Form->input('Profile.high_school_id');
			echo $this->Form->input('Profile.college_id');
			?>
			</fieldset>
		</div>
	</div>
<?php
echo $this->Js->submit('Search!', $defaultSubmitOptions);
echo $this->Form->end();

if (!empty($results)) {
echo $this->MultiSelect->create();
?>
<h3>Results</h3>
	<table cellpadding="0" cellspacing="0" class="datatable">
		<thead>
			<?php
			$links = array(
				 array(
					'title' => 'Email',
					'url' => array(
						'controller' => 'sys_emails',
						'action' => 'user'
					),
					'options' => array(
						'data-core-modal' => '{"update":false}'
					)
				 ),
				 array(
					'title' => 'Export List',
					'url' => array(
						'controller' => 'reports',
						'action' => 'export',
						'User',
						$this->MultiSelect->token
					),
					'options' => array(
						'data-core-modal' => '{"update":false}'
					)
				),
				array(
					'title' => 'Map Results',
					'url' => array(
						'controller' => 'reports',
						'action' => 'user_map',
						'User',
						$this->MultiSelect->token
					),
					'options' => array(
						'data-core-modal' => '{"update":false}'
					)
				),
				array(
					'title' => 'Delete',
					'url' => array(
						'controller' => 'users',
						'action' => 'delete',
						0
					),
					'options' => array(
						'id' => 'users-delete'
					)
				)
			);
			$this->Js->buffer('CORE.confirmation("users-delete", "Are you sure you want to delete the selected users?", {update:true})');
			$colCount = 6;
			$checkAll = true;
			echo $this->element('multiselect', compact('links', 'colCount', 'checkAll'));
			?>
			<tr>
				<th width="20px;"></th>
				<th><?php echo $this->Paginator->sort('First Name', 'Profile.first_name').' / '.$this->Paginator->sort('Last Name', 'Profile.last_name'); ?></th>
				<th>Address</th>
				<th>Contact Info</th>
				<th>Household Contact</th>
			</tr>
		</thead>
		<tbody>
<?php
	$i = 0;
	foreach ($results as $result):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->MultiSelect->checkbox($result['User']['id']); ?></td>
			<td><?php
			echo $this->Html->link($result['Profile']['name'], array('controller' => 'profiles', 'action' => 'view', 'User' => $result['User']['id'])).$this->Formatting->flags('User', $result);
			echo '<br />';
			if (!empty($result['Image'])) {
				$path = 's'.DS.$result['Image'][0]['dirname'].DS.$result['Image'][0]['basename'];
				echo $this->Media->embed($path, array('restrict' => 'image'));
			} else {
				$default = Core::read('user.default_image');
				if ($default) {
					$path = 's'.DS.$default['Image']['dirname'].DS.$default['Image']['basename'];
				}
			}
			?></td>
			<td><?php echo $this->Formatting->address($result['ActiveAddress']); ?></td>
			<td><?php
			$emails = array();
			$emails[] = $result['Profile']['primary_email'];
			$emails[] = $result['Profile']['alternate_email_1'];
			$emails[] = $result['Profile']['alternate_email_2'];
			$emails = array_filter($emails);
			foreach ($emails as &$email) {
				$email = $this->Formatting->email($email, $result['User']['id']);
			}
			echo implode('<br />', $emails);
			?><br />
			<?php
			$phones = array(
				'Cell:' => 'cell_phone',
				'Home:' => 'home_phone',
				'Office:' => 'work_phone'
			);
			foreach ($phones as $title => $phone) {
				if (!empty($result['Profile'][$phone])) {
					echo $this->Html->tag('dt', $title);
					$ext = isset($result['Profile'][$phone.'_ext']) ? $result['Profile'][$phone.'_ext'] : null;
					echo $this->Html->tag('dd', $this->Formatting->phone($result['Profile'][$phone], $ext));
				}
			}
			?></td>
			<td><?php
			$contact = $result['HouseholdMember'][0]['Household']['HouseholdContact'];
			echo $this->Html->link($contact['Profile']['name'].$this->Formatting->flags('User', array('User' => $contact)), array('controller' => 'profiles', 'action' => 'view', 'User' => $contact['Profile']['user_id']), array('escape' => false));
			echo '<br />';
			echo $this->Formatting->address($contact['ActiveAddress']);
			?></td>
		</tr>
<?php
	endforeach;
?>
		</tbody>
	</table>
<?php
echo $this->element('pagination');
echo $this->MultiSelect->end();

} else {
?>
<h3>Results</h3>
<p>No results</p>
<?php
}
?>
</div>