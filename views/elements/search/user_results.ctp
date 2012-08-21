<?php
if (!empty($results)) {

$this->Paginator->options(array(
    'updateable' => 'parent'
));
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
				)
			);
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
		<body>
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
		</body>
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
